<?php

namespace EasyDeployWorkflows\Workflows\Solr;

use EasyDeployWorkflows\Workflows as Workflows;
use EasyDeployWorkflows\Workflows\Exception as Exception;

class SolrConfiguration extends Workflows\AbstractWorkflowConfiguration {

	/**
	 * @var string
	 */
	protected $instancePath = '';

	/**
	 * @var string
	 */
	protected $restartCommand = '';

	/**
	 * @param string $instancePath
	 * @return SolrConfiguration
	 */
	public function setInstancePath($instancePath) {
		$this->instancePath = $instancePath;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getInstancePath() {
		return $this->instancePath;
	}

	/**
	 * @param string $restartCommand
	 * @return SolrConfiguration
	 */
	public function setRestartCommand($restartCommand) {
		$this->restartCommand = $restartCommand;

		return $this;
	}

	/**
	 * @param $hostName
	 * @return SolrConfiguration
	 */
	public function addMasterServers($hostName) {
		$this->addServer($hostName, 'solrmaster');

		return $this;
	}

	/**
	 * @return string
	 */
	public function getRestartCommand() {
		return $this->restartCommand;
	}

	/**
	 * @return boolean
	 */
	public function validate() {
		if(trim($this->restartCommand) == '') {
			throw new \EasyDeployWorkflows\Exception\InvalidConfigurationException('Please configure a start command for the solr configuration!');
		}

		if(trim($this->instancePath) == '') {
			throw new \EasyDeployWorkflows\Exception\InvalidConfigurationException('Please configure an instance path for the solr configuration!');
		}

		return true;
	}

	/**
	 * @return string
	 */
	public function getWorkflowClassName() {
		return 'EasyDeployWorkflows\Workflows\Solr\SolrWorkflow';
	}
}