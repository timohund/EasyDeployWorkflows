<?php

namespace EasyDeployWorkflows\Workflows;

use EasyDeployWorkflows\Workflows;

class WorkflowFactory {

	/**
	 * @var string
	 */
	protected $configurationFolder;

	/**
	 * sets the folder by convention
	 */
	public function __construct() {
		$this->setConfigurationFolder(dirname(__FILE__).'/../../../Configuration/');
	}

	/**
	 * @param string $configurationFolder
	 */
	public function setConfigurationFolder($configurationFolder)
	{
		$this->configurationFolder = $configurationFolder;
	}

	/**
	 * Creates the workflow depending on the passed configuration.
	 *
	 * @param InstanceConfiguration $instanceConfiguration
	 * @param AbstractWorkflowConfiguration $workflowConfiguration
	 * @return AbstractWorkflow
	 */
	public function create(	InstanceConfiguration $instanceConfiguration,
							AbstractWorkflowConfiguration $workflowConfiguration
							) {

		if (!class_exists($workflowConfiguration->getWorkflowClassName())) {
			throw new UnknownWorkflowException('Workflow "'.$workflowConfiguration->getWorkflowClassName().'" not existend or not loaded',2212);
		}

		$workflowClass = $workflowConfiguration->getWorkflowClassName();

		$workflow = $this->getWorkflow($workflowClass, $instanceConfiguration, $workflowConfiguration);
		$workflow->injectDownloader(new \EasyDeploy_Helper_Downloader());

		return $workflow;
	}

	/**
	 * @param $projectName
	 * @param $environmentName
	 * @param $releaseVersion
	 * @param $workFlowConfigurationVariableName
	 * @param string $instanceConfigurationVariableName
	 * @return AbstractWorkflow
	 * @throws \Exception
	 */
	public function createByConfigurationVariable($projectName,$environmentName,$releaseVersion,$workFlowConfigurationVariableName, $instanceConfigurationVariableName='instanceConfiguration') {
		if (!is_dir($this->configurationFolder)) {
			throw new \Exception('Configurationfolder not existend. Please check if you followed the convention - or set your Configurationfolder explicit');
		}
		$configurationFile = $this->configurationFolder.$projectName.DIRECTORY_SEPARATOR.$environmentName.'.php';
		if (!is_file($configurationFile)) {
			throw new \Exception('No configuration file found for project and environment. Looking in: '.$configurationFile);
		}
		include( $configurationFile );

		$instanceConfiguration = $$instanceConfigurationVariableName;
		if (!$instanceConfiguration instanceof InstanceConfiguration
			|| $instanceConfiguration->getEnvironmentName() != $environmentName
			|| $instanceConfiguration->getProjectName() != $projectName) {
			throw new \Exception('No Instance Environment Data could be found or it is invalid! Expected a variable with the name $'.$instanceConfigurationVariableName);
		}

		$workFlowConfiguration = $$workFlowConfigurationVariableName;
		if (!$workFlowConfiguration instanceof AbstractWorkflowConfiguration
			) {
			throw new \EasyDeployWorkflows\Workflows\Exception\WorkflowConfigurationNotExistendException('No Workflow Configuration found or it is invalid! Expected a Variable with the name $'.$workFlowConfigurationVariableName);
		}
		$workFlowConfiguration->setReleaseVersion($releaseVersion);
		return $this->create($instanceConfiguration, $workFlowConfiguration);
	}

	/**
	 * @param $name
	 * @param InstanceConfiguration $instanceConfiguration
	 * @param AbstractWorkflowConfiguration $workflowConfiguration
	 * @return AbstractWorkflow
	 */
	protected function getWorkflow($name,InstanceConfiguration $instanceConfiguration, AbstractWorkflowConfiguration $workflowConfiguration) {
		return new $name($instanceConfiguration,$workflowConfiguration);
	}

}