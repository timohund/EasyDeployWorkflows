<?php

namespace EasyDeployWorkflows\Workflows;

use EasyDeployWorkflows\Workflows;

require_once dirname(__FILE__) . '/AbstractConfiguration.php';
require_once dirname(__FILE__) . '/AbstractWorkflowConfiguration.php';
require_once dirname(__FILE__) . '/AbstractWorkflow.php';

require_once dirname(__FILE__) . '/InstanceConfiguration.php';

require_once dirname(__FILE__) . '/Servlet/ServletConfiguration.php';
require_once dirname(__FILE__) . '/Servlet/ServletWorkflow.php';

require_once dirname(__FILE__) . '/Web/WebConfiguration.php';
require_once dirname(__FILE__) . '/Web/WebWorkflow.php';


class WorkflowFactory {

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
	 * @param $name
	 * @param InstanceConfiguration $instanceConfiguration
	 * @param AbstractWorkflowConfiguration $workflowConfiguration
	 * @return AbstractWorkflows
	 */
	protected function getWorkflow($name,InstanceConfiguration $instanceConfiguration, AbstractWorkflowConfiguration $workflowConfiguration) {
		return new $name($instanceConfiguration,$workflowConfiguration);
	}

}