<?php

namespace EasyDeployWorkflows\Tasks\Web;

use EasyDeployWorkflows\Tasks;


/**
 * Specific Task that is bound to the AOE installbinaries
 */
class RunPackageInstallBinaries extends \EasyDeployWorkflows\Tasks\AbstractServerTask  {

	/**
	 * @var boolean
	 */
	private $createBackupBeforeInstalling = TRUE;

	/**
	 * @var boolean
	 */
	private $silentMode = FALSE;

	/**
	 * @var string
	 */
	protected $phpbinary = 'php';

	/**
	 * @var string
	 */
	protected $targetSystemPath;

	/**
	 * @var string
	 */
	protected $packageFolder;

	/**
	 * @var \EasyDeploy_Helper_Downloader
	 */
	protected $downloader;

	public function __construct() {
	//	$this->injectDownloader(new \EasyDeploy_Helper_Downloader());
	}

	/**
	 * @param \EasyDeploy_Helper_Downloader $downloader
	 */
	public function injectDownloader(\EasyDeploy_Helper_Downloader $downloader) {
		$this->downloader = $downloader;
	}

	/**
	 * @param string $packageFolder
	 */
	public function setPackageFolder($packageFolder) {
		$this->packageFolder = $packageFolder;
	}

	/**
	 * @param string $targetSystemPath
	 */
	public function setTargetSystemPath($targetSystemPath) {
		$this->targetSystemPath = $targetSystemPath;
	}

	/**
	 * @param string $bin
	 */
	public function setPHPBinary($bin) {
		if (file_exists($bin) && is_executable($bin)) {
			$this->phpbinary = $bin;
		} else {
			print $this->out('PHP binary '.$bin.' does not exist or is not executable.', self::MESSAGE_TYPE_WARNING);
		}
	}

	/**
	 * Default is set to true
	 *
	 * @depreciated this is a concept of the install strategy - you should pass a initialised strategie
	 *
	 * @param boolean $createBackup
	 * @return void
	 */
	public function setCreateBackupBeforeInstalling($createBackup) {
		$this->createBackupBeforeInstalling = (boolean) $createBackup;
	}

	/**
	 * Set this flag to force the installation without any confirmation.
	 *
	 * @param boolean $activate
	 */
	public function setSilentMode($activate) {
		$this->silentMode = $activate;
	}

	/**
	 * @param TaskRunInformation $taskRunInformation
	 * @return mixed
	 */
	protected function runOnServer(\EasyDeployWorkflows\Tasks\TaskRunInformation $taskRunInformation,\EasyDeploy_AbstractServer $server) {
		$additionalParameters = '';
		$installBinariesFolder = $this->replaceConfigurationMarkers($this->packageFolder.'/installbinaries',$taskRunInformation->getWorkflowConfiguration(),$taskRunInformation->getInstanceConfiguration());
		if (!$server->isDir($installBinariesFolder)) {
			throw new \Exception('No Installbinaries are available in '.$installBinariesFolder);
		}

		// fix permissions
		$server->run('chmod -R ug+x '.$installBinariesFolder);

		if ($this->createBackupBeforeInstalling === TRUE) {
			$additionalParameters .=' --createNewMasterBackup=1';
		}

		if ($this->silentMode === TRUE) {
			$additionalParameters .=' --silent';
		}

		// install package
		$server->run($this->phpbinary . ' ' . $installBinariesFolder.'/install.php \
			--systemPath="' . $this->targetSystemPath  . '" \
			--backupstorageroot="' . $this->getBackupStorageRoot($taskRunInformation, $server) . '" \
			--environmentName="' . $taskRunInformation->getInstanceConfiguration()->getEnvironmentName() . '"'.$additionalParameters, TRUE);
	}

	/**
	 * gets the relevant backupstorage root
	 */
	protected function getBackupStorageRoot(\EasyDeployWorkflows\Tasks\TaskRunInformation $taskRunInformation,\EasyDeploy_AbstractServer $server) {
		$this->out('Checking The Existence of BackupStorage', self::MESSAGE_TYPE_WARNING);

		$backupStorageRoot 			= $taskRunInformation->getWorkflowConfiguration()->getBackupStorageRootFolder();
		$backupMasterEnvironment	= $taskRunInformation->getWorkflowConfiguration()->getBackupMasterEnvironment();

		if ($this->hasBackupStorage($server,$backupStorageRoot,$backupMasterEnvironment)) {
			return $backupStorageRoot;
		}

		$this->out('Ohoh! Master Backup not available... Getting at least a minified backup', self::MESSAGE_TYPE_WARNING);

		$minifiedBackupRootFolder = $taskRunInformation->getWorkflowConfiguration()->getBackupStorageMinifiedRootFolder();
		$minifiedBackupRootFolder = $this->replaceConfigurationMarkers($minifiedBackupRootFolder,$taskRunInformation->getWorkflowConfiguration(),$taskRunInformation->getInstanceConfiguration());
		$server->run('mkdir -p ' . $minifiedBackupRootFolder);

		if (!$taskRunInformation->getWorkflowConfiguration()->hasMinifiedBackupSource()) {
			throw new \Exception('No minified Backup source given. Check minified root configuration');
		}

		$minifiedBackupSource = $taskRunInformation->getWorkflowConfiguration()->getMinifiedBackupSource();
		$minifiedBackupSource = $this->replaceConfigurationMarkers($minifiedBackupSource,$taskRunInformation->getWorkflowConfiguration(),$taskRunInformation->getInstanceConfiguration());

		$baseName = pathinfo(parse_url($minifiedBackupSource, PHP_URL_PATH),PATHINFO_BASENAME);

		if(!$this->hasBackupStorage($server,$minifiedBackupRootFolder,$backupMasterEnvironment)) {
			$this->downloader->download($server,$minifiedBackupSource, $minifiedBackupRootFolder);
			$server->run('cd '.$minifiedBackupRootFolder.'; tar -xzf '.$baseName);
			$server->run('cd '.$minifiedBackupRootFolder.'; mv productionMinified production');
		}

		if (!$this->hasBackupStorage($server,$minifiedBackupRootFolder, $backupMasterEnvironment)) {
			throw new \Exception('Even no minified Backup is available. Check the download Source');
		}

		//fake that minified is new
		$server->run('date +\'%Y-%m-%d %H:%M:%S\' > "'.$minifiedBackupRootFolder.'/'.$backupMasterEnvironment.'/backup_successful.txt"');

		// Now switch to use minified Backup in deployment
		$this->out('Finished getting minified backup');
		return $minifiedBackupRootFolder;
	}
	/**
	 * @return boolean
	 * @throws \EasyDeployWorkflows\Exception\InvalidConfigurationException
	 */
	public function validate() {
		if (empty($this->packageFolder)) {
			throw new \EasyDeployWorkflows\Exception\InvalidConfigurationException('packageFolder not set');
		}
		if (empty($this->targetSystemPath)) {
			throw new \EasyDeployWorkflows\Exception\InvalidConfigurationException('targetSystemPath not set');
		}
		if (empty($this->phpbinary)) {
			throw new \EasyDeployWorkflows\Exception\InvalidConfigurationException('phpbinary not set');
		}
		return true;
	}

	/**
	 * @param \EasyDeploy_AbstractServer $server
	 * @param string $root
	 * @param string $environment
	 * @return bool
	 */
	private function hasBackupStorage(\EasyDeploy_AbstractServer $server, $root, $environment) {
		if ($server->isDir($root)
			&& $server->isDir($root.'/'.$environment)
			&& $server->isDir($root.'/'.$environment.'/files') ) {

			$this->out('Fine! Backup seems available');
			return true;
		}
		return false;
	}
}