<?php

namespace EasyDeployWorkflows\Workflows\Web;

use EasyDeployWorkflows\Workflows as Workflows;

class NFSWebWorkflow extends Workflows\TaskBasedWorkflow {

	/**
	 * Can be used to do individual workflow initialisation and/or checks
	 */
	protected function workflowInitialisation() {
		$packageFileName = substr($this->workflowConfiguration->getDeploymentSource(),strrpos($this->workflowConfiguration->getDeploymentSource(),'/'));
		$packageExtractedFolderName = substr($packageFileName,0,strpos($packageFileName,'.'));


		$this->addTask('check correct deploy node',
						new \EasyDeployWorkflows\Tasks\Common\CheckCorrectDeployNode());

		if ($this->workflowConfiguration->getIndexerDataFolder() != '') {
			$this->addTask('Create indexer folders',
			$this->getIndexerFolderTask());
		}


		$this->addTask('Download Package',
						$this->getDownloadPackageTask());


		$this->addTask('Untar Package',
						$this->getUnzipPackageTask($packageFileName));


		$this->addTask('Install Package',
			$this->getInstallPackageTask($packageExtractedFolderName));


		$this->addTask('Run NFS Sync',
			$this->getRunNFSSyncScriptTask());

	}

	protected function getRunNFSSyncScriptTask()
	{
		$step = new \EasyDeployWorkflows\Tasks\Common\RunScript();
		$step->addServersByName($this->workflowConfiguration->getWebServers());
		$projectName = $this->instanceConfiguration->getProjectName();
		$environmentName = $this->instanceConfiguration->getEnvironmentName();
		$script = '/usr/local/bin/deployment_' . $projectName . '_' . $environmentName . '_nfs_sync';
		$step->setScript($script);
		$step->setIsOptional(true);
		return $step;
	}

	protected function getInstallPackageTask($packageExtractedFolderName)
	{
		$step = new \EasyDeployWorkflows\Tasks\Web\RunPackageInstallBinaries();
		$step->addServerByName($this->workflowConfiguration->getNFSServer());
		$step->setCreateBackupBeforeInstalling(false);
		$step->setPackageFolder($this->instanceConfiguration->getDeliveryFolder() . $packageExtractedFolderName);
		$step->setTargetSystemPath($this->replaceMarkers($this->workflowConfiguration->getWebRootFolder()));
		$step->setSilentMode($this->workflowConfiguration->getInstallSilent());
		return $step;
	}

	protected function getUnzipPackageTask($packageFileName)
	{
		$step = new \EasyDeployWorkflows\Tasks\Common\Untar();
		$step->addServerByName($this->workflowConfiguration->getNFSServer());
		$step->autoInitByPackagePath($this->instanceConfiguration->getDeliveryFolder() . '/' . $packageFileName);
		$step->setMode(\EasyDeployWorkflows\Tasks\Common\Untar::MODE_SKIP_IF_EXTRACTEDFOLDER_EXISTS);
		return $step;
	}

	protected function getDownloadPackageTask()
	{
		$step = new \EasyDeployWorkflows\Tasks\Common\Download();
		$step->addServerByName($this->workflowConfiguration->getNFSServer());
		$step->setSource($this->workflowConfiguration->getDeploymentSource());
		$step->setTarget($this->instanceConfiguration->getDeliveryFolder());
		return $step;
	}

	protected function getIndexerFolderTask()
	{
		$step = new \EasyDeployWorkflows\Tasks\Common\CreateMissingFolder();
		$step->addServersByName($this->workflowConfiguration->getIndexerServers());
		$step->setFolder($this->replaceMarkers($this->workflowConfiguration->getIndexerDataFolder()));
		return $step;
	}

}