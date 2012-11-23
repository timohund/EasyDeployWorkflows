<?php

namespace EasyDeployWorkflows\Workflows;

use EasyDeployWorkflows\Workflows;

class InstanceConfiguration extends AbstractConfiguration {

	/**
	 * @var string
	 */
	protected $deliveryFolder = '';

	/**
	 * @var string
	 */
	protected $environmentName = '';

	/**
	 * @var string
	 */
	protected $projectName = '';

	/**
	 * @param $hostname
	 * @return \AbstractConfiguration
	 */
	public function addAllowedDeployServer($hostname) {
		return $this->addServer($hostname,'allowed_deploy_servers');
	}

	/**
	 * @return array
	 */
	public function getAllowedDeployServers() {
		return $this->getServers('allowed_deploy_servers');
	}

	/**
	 * @param $hostname
	 * @return bool
	 */
	public function isAllowedDeployServer($hostname) {
		return in_array($hostname, $this->getAllowedDeployServers());
	}

	/**
	 * @return boolean
	 */
	public function hasAllowedDeployServers() {
		return count($this->getAllowedDeployServers()) > 0;
	}

	/**
	 * @param string $deliveryFolder
	 * @return \EasyDeployWorkflows\Workflows\InstanceConfiguration
	 */
	public function setDeliveryFolder($deliveryFolder) {
		$this->deliveryFolder = $deliveryFolder;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function hasDeliveryFolder() {
		return $this->getDeliveryFolder() != '';
	}

	/**
	 * @return string
	 */
	public function getDeliveryFolder() {
		return $this->deliveryFolder;
	}

	/**
	 * @param string $environmentName
	 * @return \EasyDeployWorkflows\Workflows\InstanceConfiguration
	 */
	public function setEnvironmentName($environmentName) {
		$this->environmentName = $environmentName;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getEnvironmentName() {
		return $this->environmentName;
	}

	/**
	 * @return bool
	 */
	public function hasEnvironmentName() {
		return $this->environmentName != '';
	}

	/**
	 * @param string $projectName
	 * @return \EasyDeployWorkflows\Workflows\InstanceConfiguration
	 */
	public function setProjectName($projectName) {
		$this->projectName = $projectName;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getProjectName() {
		return $this->projectName;
	}

	/**
	 * @return bool
	 */
	public function isValid() {
		return $this->hasAllowedDeployServers() && $this->hasEnvironmentName() && $this->hasDeliveryFolder();
	}
}
