<?php

use EasyDeployWorkflows\Tasks as Tasks;

require_once EASYDEPLOY_WORKFLOW_ROOT . 'Classes/Autoloader.php';

class CreateMissingFolderTest extends PHPUnit_Framework_TestCase {

	/**
	 * test needs easydeploy to run
	 */
	public function setUp() {
		if (is_file(EASYDEPLOY_WORKFLOW_ROOT.'../EasyDeploy/Classes/RemoteServer.php')) {
			require_once EASYDEPLOY_WORKFLOW_ROOT.'../EasyDeploy/Classes/RemoteServer.php';
		}

		if (!class_exists('EasyDeploy_RemoteServer')) {
			$this->markTestSkipped(
				'EasyDeploy_RemoteServer class is not available.'
			);
		}

	}
	/**
	 *
	 * @test
	 * @return void
	 */
	public function canValidate() {
		$task = new \EasyDeployWorkflows\Tasks\Common\CreateMissingFolder();
		$this->assertFalse($task->isValid());
		$task->setFolder('/folder');
		$this->assertTrue($task->isValid());
	}

	/**
	 *
	 * @test
	 * @return void
	 */
	public function canCreateFolder() {
		$task = new \EasyDeployWorkflows\Tasks\Common\CreateMissingFolder();
		$serverMock	 = $this->getMock('EasyDeploy_RemoteServer',array('run','isDir'),array(),'',false);
		$task->addServer($serverMock);
		$task->setFolder('/folder');
		$taskRunInformation = new Tasks\TaskRunInformation();
		$serverMock->expects($this->at(0))->method('isDir')->will($this->returnValue(false));
		$serverMock->expects($this->at(1))->method('run')->with('mkdir -p /folder');
		$serverMock->expects($this->at(2))->method('run')->with('chmod g+rws /folder');
		$serverMock->expects($this->at(3))->method('isDir')->will($this->returnValue(true));
		$task->run($taskRunInformation);
	}

}