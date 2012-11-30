<?php

use EasyDeployWorkflows\Task as Task;

class AbstractTaskTest extends AbstractMockedTest {

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