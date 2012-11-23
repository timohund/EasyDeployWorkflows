<?php

namespace EasyDeployWorkflows\Workflows;

use EasyDeployWorkflows\Workflows;

abstract class AbstractWorkflowConfiguration extends AbstractConfiguration {

	/**
	 * @var string
	 */
	protected $deploymentPackageSource = '';

	/**
	 * @var boolean
	 */
	protected $installSilent = false;


	/**
	 * @param $packageSource
	 */
	public function setDeploymentPackageSource($packageSource) {
		$this->deploymentPackageSource = $packageSource;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getDeploymentPackageSource() {
		return $this->deploymentPackageSource;
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
}
