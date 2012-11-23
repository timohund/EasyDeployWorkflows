<?php

namespace EasyDeployWorkflows\Workflows;

use EasyDeployWorkflows\Workflows;

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
	 * @var EasyDeploy_Helper_Downloader
	 */
	protected $downloader;

	/**
	 * @param InstanceConfiguration $instanceConfiguration
	 * @param AbstractWorkflowConfiguration $workflowConfiguration
	 */
	public function __construct(InstanceConfiguration $instanceConfiguration, AbstractWorkflowConfiguration $workflowConfiguration) {
		$instanceConfiguration->validate();
		$workflowConfiguration->validate();

		$this->instanceConfiguration = $instanceConfiguration;
		$this->workflowConfiguration = $workflowConfiguration;
	}

	/**
	 * @param \EasyDeploy_Helper_Downloader $downloader
	 */
	public function injectDownloader(\EasyDeploy_Helper_Downloader $downloader) {
		$this->downloader = $downloader;
	}

	/**
	 * @param string $message
	 * @param string $type
	 */
	protected function out($message, $type='') {
		echo \EasyDeploy_Utils::formatMessage($message,$type).PHP_EOL;
	}

	/**
	 * @param string $serverName
	 * @return \EasyDeploy_LocalServer|\EasyDeploy_RemoteServer
	 */
	protected function getServer($serverName) {
		if ($serverName == 'localhost') {
			return new \EasyDeploy_LocalServer($serverName);
		}

		return new \EasyDeploy_RemoteServer($serverName);
	}

	/**
	 * @param string $releaseVersion
	 * @return mixed
	 */
	abstract function deploy($releaseVersion);
}