<?php

use EasyDeployWorkflows\Task as Task;

require_once EASYDEPLOY_WORKFLOW_ROOT . 'Classes/Autoloader.php';

class AbstractTaskTest extends PHPUnit_Framework_TestCase {

	/**
	 *
	 * @test
	 * @return void
	 */
	public function canValidate() {
		$task = $this->getMockForAbstractClass('EasyDeployWorkflows\Tasks\AbstractTask');
		$this->assertTrue($task->isValid());
	}

}