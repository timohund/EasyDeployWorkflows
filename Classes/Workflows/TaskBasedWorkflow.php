<?php

namespace EasyDeployWorkflows\Workflows;

use EasyDeployWorkflows\Workflows;

require_once dirname(__FILE__) . '/Exception/DuplicateStepAssignmentException.php';

class TaskBasedWorkflow extends AbstractWorkflow {

	/**
	 * @var
	 */
	protected $tasks = array();

	/**
	 * @param $name
	 * @param \EasyDeployWorkflows\Tasks\AbstractTask $step
	 * @throws DuplicateStepAssignmentException
	 */
	public function addTask($name, \EasyDeployWorkflows\Tasks\AbstractTask $step) {
		if (isset($this->tasks[$name])) {
			throw new \EasyDeployWorkflows\Workflows\Exception\DuplicateStepAssignmentException($name.' already existend!');
		}
		$step->validate();
		$this->tasks[$name] = $step;
	}

	public function deploy() {
		$taskRunInformation = new \EasyDeployWorkflows\Tasks\TaskRunInformation();
		$taskRunInformation->setInstanceConfiguration($this->instanceConfiguration);
		$taskRunInformation->setWorkflowConfiguration($this->workflowConfiguration);
		foreach ($this->tasks as $taskName => $task) {
			$this->out(' [Task] '.$taskName.' starting:');
			$task->run($taskRunInformation);
		}
	}
}