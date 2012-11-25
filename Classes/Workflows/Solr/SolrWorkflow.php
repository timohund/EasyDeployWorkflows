<?php

namespace EasyDeployWorkflows\Workflows\Solr;

use EasyDeployWorkflows\Workflows as Workflows;

class SolrWorkflow extends Workflows\AbstractWorkflow {

	/**
	 * @var \EasyDeployWorkflows\Workflows\Solr\SolrConfiguration
	 */
	protected $workflowConfiguration;

	/**
	 * @param string $releaseVersion
	 * @return mixed|void
	 */
	public function deploy() {
		$localServer = new \EasyDeploy_LocalServer();

		$task = new \EasyDeployWorkflows\Tasks\Common\CheckCorrectDeployNode();
		$task->run($this->createTaskRunInformation());

		$deployService =  new \EasyDeploy_DeployService($this->getInstallStrategy());
		$this->initDeployService($deployService);

		$deploymentPackage = $this->replaceMarkers($this->workflowConfiguration->getDeploymentSource());
		$this->out('Start deploying SolrConf Package: "'.$deploymentPackage.'"', self::MESSAGE_TYPE_INFO);
		$deployService->deploy( $localServer, $this->workflowConfiguration->getReleaseVersion(), $deploymentPackage);

		$this->reloadSolr($localServer);
	}

	protected function reloadSolr(\EasyDeploy_AbstractServer $server) {
		if ($this->workflowConfiguration->getRestartCommand() != '') {
			$this->out('No restart Command is Set for the deployment!',self::MESSAGE_TYPE_WARNING);
		}
		$server->run($this->workflowConfiguration->getRestartCommand());
	}

	protected function getInstallStrategy() {
		$strategy = new \EasyDeploy_InstallStrategy_PHPInstaller();
		$strategy->setSilentMode($this->workflowConfiguration->getInstallSilent());
		return $strategy;
	}

	/**
	 * @param EasyDeploy_DeployService $deployService
	 */
	protected function initDeployService(\EasyDeploy_DeployService $deployService ) {
		$deployService->setEnvironmentName($this->instanceConfiguration->getEnvironmentName());
		$deployService->setDeliveryFolder($this->instanceConfiguration->getDeliveryFolder());
		$deployService->setSystemPath($this->workflowConfiguration->getInstancePath());
	}
}