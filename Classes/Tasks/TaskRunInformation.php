<?php

namespace EasyDeployWorkflows\Tasks;

use EasyDeployWorkflows\Workflows;

class TaskRunInformation {

	/**
	 * @var \EasyDeployWorkflows\Workflows\InstanceConfiguration
	 */
	protected $instanceConfiguration;

	/**
	 * @var \EasyDeployWorkflows\Workflows\AbstractConfiguration
	 */
	protected $workflowConfiguration;

	/**
	 * @param \EasyDeployWorkflows\Workflows\InstanceConfiguration $instanceConfiguration
	 */
	public function setInstanceConfiguration(\EasyDeployWorkflows\Workflows\InstanceConfiguration $instanceConfiguration) {
		$this->instanceConfiguration = $instanceConfiguration;
	}

	/**
	 * @return \EasyDeployWorkflows\Workflows\InstanceConfiguration
	 */
	public function getInstanceConfiguration() {
		return $this->instanceConfiguration;
	}

	/**
	 * @param \EasyDeployWorkflows\Workflows\AbstractConfiguration $workflowConfiguration
	 */
	public function setWorkflowConfiguration(\EasyDeployWorkflows\Workflows\AbstractConfiguration $workflowConfiguration) {
		$this->workflowConfiguration = $workflowConfiguration;
	}

	/**
	 * @return \EasyDeployWorkflows\Workflows\AbstractConfiguration
	 */
	public function getWorkflowConfiguration() {
		return $this->workflowConfiguration;
	}
}