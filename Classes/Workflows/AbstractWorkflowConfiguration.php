<?php

namespace EasyDeployWorkflows\Workflows;

use EasyDeployWorkflows\Workflows;

abstract class AbstractWorkflowConfiguration extends AbstractConfiguration {

	/**
	 * @var string
	 */
	protected $deploymentSource = '';

	/**
	 * @var boolean
	 */
	protected $installSilent = false;

	/**
	 * @param $packageSource
	 */
	public function setDeploymentSource($packageSource) {
		$this->deploymentSource = $packageSource;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getDeploymentSource() {
		return $this->deploymentSource;
	}

	/**
	 * @param boolean $installSilent
	 */
	public function setInstallSilent($installSilent) {
		$this->installSilent = $installSilent;

		return $this;
	}

	/**
	 * @return boolean
	 */
	public function getInstallSilent() {
		return $this->installSilent;
	}

	/**
	 * @return string
	 */
	abstract function getWorkflowClassName();
}
