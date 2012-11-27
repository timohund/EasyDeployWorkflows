<?php

use EasyDeployWorkflows\Tasks as Tasks;

require_once EASYDEPLOY_WORKFLOW_ROOT . 'Classes/Autoloader.php';

class RunPackageInstallerBinariesTest extends PHPUnit_Framework_TestCase {

	/**
	 * @test
	 */
	public function canUseExistingBackupWhenAllreadyInFileSystem() {
		$workflowConfiguration = new \EasyDeployWorkflows\Workflows\Web\NFSWebConfiguration();
		$workflowConfiguration->setBackupStorageRootFolder('/home/homer.simpson');
		$workflowConfiguration->setBackupMasterEnvironment('deploy');

		$instanceConfiguration = new \EasyDeployWorkflows\Workflows\InstanceConfiguration();

		$taskConfiguration = new \EasyDeployWorkflows\Tasks\TaskRunInformation();
		$taskConfiguration->setWorkflowConfiguration($workflowConfiguration);
		$taskConfiguration->setInstanceConfiguration($instanceConfiguration);

			//avoid constructor calling because the downloader is injected there
			/** @var $task  \EasyDeployWorkflows\Tasks\Web\RunPackageInstallBinaries */
	//	$task = $this->getMock('EasyDeployWorkflows\Tasks\Web\RunPackageInstallBinaries', array(), array(),'',false);
		$task = new EasyDeployWorkflows\Tasks\Web\RunPackageInstallBinaries();
		$task->setCreateBackupBeforeInstalling(false);
		$task->setPackageFolder('/home/package');
		$task->setPHPBinary('php5');
		$task->setTargetSystemPath('/opt/web');

		$serverMock	 = $this->getMock('EasyDeploy_RemoteServer',array('run','isDir'),array(),'',false);

			//we fake that the install binaries exist
		$serverMock->expects($this->any())->method('isDir')->will(
			$this->returnCallback(function($dir) use ($workflowConfiguration) {
				$root 			= $workflowConfiguration->getBackupStorageRootFolder();
				$environment 	= $root.'/'.$workflowConfiguration->getBackupMasterEnvironment();
				$backupfiles	= $environment.'/files';

				return in_array($dir, array(
						//we fake existing installbinaries
					'/home/package/installbinaries',
						//and an existing backup
					$root,
					$environment,
					$backupfiles
				));
			})
		);


			//@todo check the exexuted server commands
		$serverMock->expects($this->any())->method('run')->will($this->returnCallback(
			function ($command){
				echo $command;
			}
		));


		$task->addServer($serverMock);
		$task->run($taskConfiguration);

	}
}