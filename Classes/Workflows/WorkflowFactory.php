<?php

namespace EasyDeployWorkflows\Workflows;

use EasyDeployWorkflows\Workflows;

class WorkflowFactory {

	/**
	 * Creates the workflow depending on the passed configuration.
	 *
	 * @param InstanceConfiguration $instanceConfiguration
	 * @param AbstractWorkflowConfiguration $workflowConfiguration
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

	}

}