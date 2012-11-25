<?php

namespace EasyDeployWorkflows\Tasks\Common;

use EasyDeployWorkflows\Tasks;



class Untar extends \EasyDeployWorkflows\Tasks\AbstractServerTask  {


	protected $folder;

	protected $packageFileName;

	protected $expectedExtractedFolder;

	protected $mode;

	const MODE_SKIP_IF_EXTRACTEDFOLDER_EXISTS=1;
	const MODE_DELETE_IF_EXTRACTEDFOLDER_EXISTS=2;

	public function setExpectedExtractedFolder($expectedExtractedFolder)
	{
		$this->expectedExtractedFolder = $expectedExtractedFolder;
	}

	public function setFolder($folder)
	{
		$this->folder = $folder;
	}

	public function setMode($mode)
	{
		$this->mode = $mode;
	}

	public function setPackageFileName($packageFileName)
	{
		$this->packageFileName = $packageFileName;
	}

	/**
	 * @param $path
	 */
	public function autoInitByPackagePath($path) {
		$infos = pathinfo($path);
		$this->setFolder($infos['dirname']);
		//fix .tar.gz
		$extractedFolder = str_replace('.tar','',$infos['filename']);
		$this->setExpectedExtractedFolder($extractedFolder);

		$this->setPackageFileName($infos['filename'].'.'.$infos['extension']);
	}


	/**
	 * @param TaskRunInformation $taskRunInformation
	 * @return mixed
	 */
	protected function runOnServer(\EasyDeployWorkflows\Tasks\TaskRunInformation $taskRunInformation,\EasyDeploy_AbstractServer $server) {

		$folder = rtrim($this->replaceConfigurationMarkers($this->folder,$taskRunInformation->getWorkflowConfiguration(),$taskRunInformation->getInstanceConfiguration()),DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
		$packageFileName = $this->replaceConfigurationMarkers($this->packageFileName,$taskRunInformation->getWorkflowConfiguration(),$taskRunInformation->getInstanceConfiguration());
		$expectedExtractedFolder = $this->replaceConfigurationMarkers($this->expectedExtractedFolder,$taskRunInformation->getWorkflowConfiguration(),$taskRunInformation->getInstanceConfiguration());

		if ($server->isDir($folder.$expectedExtractedFolder)) {
			if ($this->mode == self::MODE_SKIP_IF_EXTRACTEDFOLDER_EXISTS) {
				$this->out('Extracted Version already existend.. skipping');
				return;
			}
			else {
				$server->run('rm -rf '.$folder.$expectedExtractedFolder);
			}
		}
		//extract
		$server->run('cd ' . $folder . '; tar -xzf ' . $packageFileName);
	}

	/**
	 * @return boolean
	 * throws Exception\InvalidConfigurationException
	 */
	public function validate() {
		if (empty($this->folder)) {
			throw new \EasyDeployWorkflows\Exception\InvalidConfigurationException('source not set');
		}
		if (empty($this->packageFileName)) {
			throw new \EasyDeployWorkflows\Exception\InvalidConfigurationException('packageFileName not set');
		}
		if (empty($this->expectedExtractedFolder)) {
			throw new \EasyDeployWorkflows\Exception\InvalidConfigurationException('expectedExtractedFolder not set');
		}
		return true;
	}
}