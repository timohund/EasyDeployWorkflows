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
		if($workflowConfiguration instanceof \EasyDeployWorkflows\Workflows\Web\WebWorkflow) {
			$workflow = new \EasyDeployWorkflows\Workflows\Web\WebWorkflow(
				$instanceConfiguration,
				$workflowConfiguration
			);
		} elseif ($workflowConfiguration instanceof \EasyDeployWorkflows\Workflows\Servlet\ServletWorkflow) {
			$workflow = new \EasyDeployWorkflows\Workflows\Servlet\ServletWorkflow(
				$instanceConfiguration,
				$workflowConfiguration
			);
		} else {
			throw new UnknownWorkflowException('',2212);
		}

		$workflow->injectDownloader(new \EasyDeploy_Helper_Downloader());
		return $workflow;
	}

}