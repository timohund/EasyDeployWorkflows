<?php

namespace EasyDeployWorkflows\Workflows;

abstract class AbstractWorkflow {

	/**
	 * @var InstanceConfiguration
	 */
	protected $instanceConfiguration;

	/**
	 * @var AbstractConfiguration
	 */
	protected $workflowConfiguration;

	/**
	 * @param InstanceConfiguration $instanceConfiguration
	 * @param AbstractConfiguration $workflowConfiguration
	 */
	public function __construct(	InstanceConfiguration $instanceConfiguration,
									AbstractConfiguration $workflowConfiguration
								) {
		$this->instanceConfiguration = $instanceConfiguration;
		$this->workflowConfiguration = $workflowConfiguration;
	}

	/**
	 * @param string $message
	 * @param string $type
	 */
	protected function out($message, $type='') {
		echo EasyDeploy_Utils::formatMessage($message,$type).PHP_EOL;
	}

	/**
	 * @param string $releaseVersion
	 * @return mixed
	 */
	abstract function deploy($releaseVersion);
}