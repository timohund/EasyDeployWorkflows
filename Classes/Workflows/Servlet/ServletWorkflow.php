<?php

namespace EasyDeployWorkflows\Workflows\Servlet;

use EasyDeployWorkflows\Workflows as Workflows;

class ServletWorkflow extends Workflows\AbstractWorkflow {

	/**
	 * @var \EasyDeployWorkflows\Workflows\Servlet\ServletConfiguration
	 */
	protected $workflowConfiguration;

	/**
	 * @param string $releaseVersion
	 * @return mixed|void
	 */
	public function deploy() {
		$localServer 				= $this->getServer('localhost');

		$deliveryFolder				= $this->replaceMarkers($this->instanceConfiguration->getDeliveryFolder());
		$downloadSource = $this->replaceMarkers($this->workflowConfiguration->getDeploymentSource());
		$this->downloader->download($localServer,$downloadSource,$deliveryFolder);

		$downloadedWarFile 			= $deliveryFolder.pathinfo($downloadSource,PATHINFO_BASENAME);
		$tmpWarLocation 			= '/tmp/'.pathinfo($downloadSource,PATHINFO_BASENAME);

		$task 						= $this->getDeployWarInTomcatTask(
			$downloadedWarFile,
			$tmpWarLocation,
			$this->workflowConfiguration->getTomcatUsername(),
			$this->workflowConfiguration->getTomcatPassword(),
			$this->workflowConfiguration->getTomcatPort(),
			$this->workflowConfiguration->getTargetPath(),
			'localhost'
		);

		foreach($this->workflowConfiguration->getServletServers() as $server) {
			$task->addServer($this->getServer($server));
		}

		return $task->run($this->createTaskRunInformation());
	}

	/**
	 * @param $downloadWar
	 * @param $tmpWar
	 * @param $tomcatUser
	 * @param $tomcatPassword
	 * @param $tomcatPort
	 * @param $tomcatPath
	 * @param $tomcatHostname
	 * @return \EasyDeployWorkflows\Tasks\Servlet\DeployWarInTomcat
	 */
	protected function getDeployWarInTomcatTask($downloadWar, $tmpWar, $tomcatUser, $tomcatPassword, $tomcatPort, $tomcatPath, $tomcatHostname) {
		$task = new \EasyDeployWorkflows\Tasks\Servlet\DeployWarInTomcat();
		$task->setDownloadWarFile($downloadWar);
		$task->setTmpWarFile($tmpWar);
		$task->setTomcatUser($tomcatUser);
		$task->setTomcatPassword($tomcatPassword);
		$task->setTomcatPort($tomcatPort);
		$task->setTomcatPath($tomcatPath);
		$task->setTomcatHostname($tomcatHostname);

		return $task;
	}

}