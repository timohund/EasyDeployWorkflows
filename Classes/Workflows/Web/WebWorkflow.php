<?php

namespace EasyDeployWorkflows\Workflows\Web;

use EasyDeployWorkflows\Workflows as Workflows;

class WebWorkflow extends Workflows\AbstractWorkflow{

	/**
	 * @var \EasyDeployWorkflows\Workflows\InstanceConfiguration
	 */
	protected $instanceConfiguration;

	/**
	 * @var \EasyDeployWorkflows\Workflows\Web\WebConfiguration
	 */
	protected $workflowConfiguration;

	/**
	 * @var
	 */
	protected $deployService;

	/**
	 * @param InstanceConfiguration $instanceConfiguration
	 * @param WebConfiguration $workflowConfiguration
	 */
	public function __construct(\EasyDeployWorkflows\Workflows\InstanceConfiguration $instanceConfiguration, \EasyDeployWorkflows\Workflows\Web\WebConfiguration $workflowConfiguration) {
		if (!$instanceConfiguration->isValid()) {
			throw new \InvalidArgumentException('Invalid instance configuration');
		}

		if (!$workflowConfiguration->isValid()) {
			throw new \InvalidArgumentException('Invalid workflow configuration');
		}

		parent::__construct($instanceConfiguration,$workflowConfiguration);
	}

	/**
	 * @param string $releaseVersion
	 * @throws InvalidArgumentException
	 */
	public function deploy($releaseVersion) {
		$localServer = $this->getServer('localhost');

		if(!$this->instanceConfiguration->isAllowedDeployServer($localServer->getHostname())) {
			throw new \InvalidArgumentException("Unallowed deploy server ".$localServer->getHostname());
		}

		$this->initDeploymentService();
		$deployService = $this->getDeploymentService();
		$this->prepareDeployService($deployService);
		$this->downloadAndUseMinifiedBackup($localServer, $deployService);

		$this->beforeDeployment();

		$this->out('Start deploying Package: "'.$this->workflowConfiguration->getDeploymentSource().'"', \EasyDeploy_Utils::MESSAGE_TYPE_INFO) ;

		$downloadTarget = $this->instanceConfiguration->getDeliveryFolder() . '/' . $releaseVersion;
		$deploymentPackageSource 			= sprintf($this->workflowConfiguration->getDeploymentSource(),$releaseVersion);
		$downloadedReleaseDirectory = $this->downloader->download($localServer, $deploymentPackageSource, $downloadTarget);
		$deployService->installPackage($localServer,$downloadedReleaseDirectory);

		$this->afterDeployment();
	}

	/**
	 * @return void
	 */
	protected function beforeDeployment() {
		$this->prepareIndexerDeployment();
	}

	/**
	 * @return void
	 */
	protected function afterDeployment() {
		$this->runNFSSyncOnAllFrontendNodes();
	}

	/**
	 * @return void
	 */
	protected function initDeploymentService() {
		$deployService = new \EasyDeploy_DeployService($this->getInstallStrategy());
		$this->setDeploymentService($deployService);
	}

	/**
	 * @param $deployService
	 */
	public function setDeploymentService(\EasyDeploy_DeployService $deployService) {
		$this->deployService = $deployService;
	}

	/**
	 * @return \EasyDeploy_DeployService
	 */
	protected function getDeploymentService() {
		return $this->deployService;
	}

	/**
	 * @throws Exception
	 * @return void
	 */
	protected function prepareIndexerDeployment() {
		foreach ($this->workflowConfiguration->getIndexerServers() as $serverName) {

			$server = $this->getServer($serverName);
			$indexerDataFolder = $this->workflowConfiguration->getIndexerDataFolder();

			if (!$server->isDir($indexerDataFolder)) {
				$message = 'indexerDataFolder on IndexerNode is not present! Try to create "'.indexerDataFolder.'"';
				$this->out($message, \EasyDeploy_Utils::MESSAGE_TYPE_WARNING);

				$server->run('mkdir -p '.$indexerDataFolder);
				$server->run('chmod g+rws '.$indexerDataFolder);
			}

			if (!$server->isDir($indexerDataFolder)) {
				$message = 'indexerDataFolder on IndexerNode "'.$serverName.'" is not present! Could not create "'.indexerDataFolder.'"';
				$this->out($message, \EasyDeploy_Utils::MESSAGE_TYPE_ERROR);
				throw new \Exception('indexerDataFolder on IndexerNode "'.$serverName.'" is not present!');
			}
		}
	}

	/**
	 * @return void
	 */
	protected function runNFSSyncOnAllFrontendNodes() {
		$projectName 		= $this->instanceConfiguration->getProjectName();
		$environmentName 	= $this->instanceConfiguration->getEnvironmentName();
		$script = '/usr/local/bin/deployment_'.$projectName.'_' . $environmentName . '_nfs_sync';

		$i = 0;

		foreach ($this->workflowConfiguration->getWebServers() as $serverName) {
			$i++;
			$server = $this->getServer($serverName);

			if ($server->isFile($script)) {
				$message = 'NFS to Local Sync for "'.$serverName.'"';
				$this->out($message, \EasyDeploy_Utils::MESSAGE_TYPE_INFO);
				$server->run($script, TRUE);
			} else {
				$message = 'NO NFS to Local Sync for "'.$serverName.'" required. (Script is not configured on Server)';
				$this->out($message, \EasyDeploy_Utils::MESSAGE_TYPE_WARNING);
			}
		}
	}

	/**
	 * @param \EasyDeploy_AbstractServer $server
	 * @param \EasyDeploy_DeployService $deployService
	 * @return bool
	 * @throws Exception
	 */
	protected function downloadAndUseMinifiedBackup(\EasyDeploy_AbstractServer $server, \EasyDeploy_DeployService $deployService) {

		$this->out('Checking The Existence of BackupStorage', \EasyDeploy_Utils::MESSAGE_TYPE_WARNING);

		$backupStorageRoot 			= $this->workflowConfiguration->getBackupStorageRootFolder();
		$backupMasterEnvironment	= $this->workflowConfiguration->getBackupMasterEnvironment();

		if ($this->hasBackupStorage($server,$backupStorageRoot,$backupMasterEnvironment)) {
			return true;
		}

		$this->out('Ohoh! Production Backup not available... Getting at least a minified backup', \EasyDeploy_Utils::MESSAGE_TYPE_WARNING);

		$minifiedBackupRootFolder = $this->workflowConfiguration->getBackupStorageMinifiedRootFolder();
		$server->run('mkdir -p ' . $minifiedBackupRootFolder);

		if (!$this->workflowConfiguration->hasMinifiedBackupSource()) {
			throw new \Exception('No minified Backup source given. Check minified root configuration');
		}

		$minifiedBackupSource = $this->workflowConfiguration->getMinifiedBackupSource();
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
		$deployService->setBackupstorageroot($minifiedBackupRootFolder);
		$this->out('Finished getting minified backup');
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

	/**
	 * @return \EasyDeploy_InstallStrategy_WebProjectPHPInstaller
	 */
	protected function getInstallStrategy() {
		$strategy = new \EasyDeploy_InstallStrategy_WebProjectPHPInstaller();

		if ($this->workflowConfiguration->isBackupMasterEnvironment($this->instanceConfiguration->getEnvironmentName())) {
			$strategy->setCreateBackupBeforeInstalling(TRUE);
		} else {
			$strategy->setCreateBackupBeforeInstalling(FALSE);
		}

		$strategy->setSilentMode($this->workflowConfiguration->getInstallSilent());

		return $strategy;
	}

	/**
	 * @param \EasyDeploy_DeployService $deployService
	 */
	protected function prepareDeployService(\EasyDeploy_DeployService $deployService ) {
		$deployService->setEnvironmentName($this->instanceConfiguration->getEnvironmentName());
		$deployService->setDeliveryFolder($this->instanceConfiguration->getDeliveryFolder());

		$deployService->setDeployerUnixGroup($this->workflowConfiguration->getApacheGroup());
		$deployService->setSystemPath($this->workflowConfiguration->getWebRootFolder());
		$deployService->setBackupstorageroot($this->workflowConfiguration->getWebRootFolder());
	}
}