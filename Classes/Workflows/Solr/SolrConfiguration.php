<?php

namespace EasyDeployWorkflows\Workflows\Solr;

use EasyDeployWorkflows\Workflows as Workflows;

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
	public function isValid() {
		return $this->restartCommand != '' && $this->instancePath != '';
	}

	/**
	 * @return string
	 */
	public function getWorkflowClassName() {
		return 'EasyDeployWorkflows\Workflows\Solr\SolrWorkflow';
	}
}