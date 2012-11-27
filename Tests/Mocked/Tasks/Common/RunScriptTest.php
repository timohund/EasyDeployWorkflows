<?php

use EasyDeployWorkflows\Tasks as Tasks;

require_once EASYDEPLOY_WORKFLOW_ROOT . 'Classes/Autoloader.php';

class RunScriptTest extends PHPUnit_Framework_TestCase {

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
	 * @expectedException \EasyDeployWorkflows\Exception\FileNotFoundException
	 * @return void
	 */
	public function canThrowExepctionIfScriptIsNotThere() {
		$task = new \EasyDeployWorkflows\Tasks\Common\RunScript();
		$serverMock	 = $this->getMock('EasyDeploy_RemoteServer',array('run','isFile'),array(),'',false);
		$serverMock->expects($this->once())->method('isFile')->will($this->returnValue(false));
		$task->addServer($serverMock);
		$task->setScript('/folder');
		$taskRunInformation = new Tasks\TaskRunInformation();
		$task->run($taskRunInformation);
	}

	/**
	 *
	 * @test
	 * @return void
	 */
	public function canRunScript() {
		$task = new \EasyDeployWorkflows\Tasks\Common\RunScript();
		$serverMock	 = $this->getMock('EasyDeploy_RemoteServer',array('run','isFile'),array(),'',false);
		$task->addServer($serverMock);
		$task->setScript('/script.sh -i');
		$taskRunInformation = new Tasks\TaskRunInformation();
		$serverMock->expects($this->at(0))->method('isFile')->will($this->returnValue(true));
		$serverMock->expects($this->at(1))->method('isFile')->will($this->returnValue(true));
		$serverMock->expects($this->at(2))->method('run')->with('/script.sh -i');
		$task->run($taskRunInformation);
	}

}