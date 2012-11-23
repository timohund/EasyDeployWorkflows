<?php

namespace EasyDeployWorkflows\Workflows;

use EasyDeployWorkflows\Workflows;
use EasyDeployWorkflows\Workflows\Exception as Exception;

abstract class AbstractConfiguration {

	/**
	 * @var
	 */
	private $folders = array();

	/**
	 * @var
	 */
	protected $servers = array();

	/**
	 * @param string $scope
	 * @param int $index
	 */
	protected function getFolder($scope, $index=0) {
		if(!isset($this->folders[$scope][$index])) {
			return '';
		}

		return $this->folders[$scope][$index];
	}

	/**
	 * @param string  $folderName
	 * @param string $scope
	 * @param int $index
	 * @throws InvalidArgumentException
	 */
	protected function setFolder($folderName, $scope, $index = 0) {
		if(!is_int($index)) {
			throw new \InvalidArgumentException('Invalid index '.serialize($index));
		}
		if(!is_string($scope)) {
			throw new \InvalidArgumentException('Invalid scope '.serialize($scope));
		}
		if(!is_string($folderName)) {
			throw new \InvalidArgumentException('Invalid folder '.serialize($folderName));
		}

		$this->folders[$scope][$index] = $folderName;

		return $this;
	}


	/**
	 * @param string $hostName
	 * @param string $scope
	 * @throws InvalidArgumentException
	 */
	protected function addServer($hostName, $scope) {
		if(!is_string($scope)) {
			throw new \InvalidArgumentException('Invalid scope '.serialize($scope));
		}

		if(!is_string($hostName)) {
			throw new \InvalidArgumentException('Invalid hostname '.serialize($hostName));
		}

		$hasHost = isset($this->servers[$scope]) && is_array($this->servers[$scope]);
		if($hasHost && in_array($hostName, $this->servers[$scope])) {
			throw new \InvalidArgumentException('Could not set same hostname twice');
		}

		$this->servers[$scope][] = $hostName;

		return $this;
	}

	/**
	 * @param $scope
	 * @return array
	 * @throws InvalidArgumentException
	 */
	protected function getServers($scope)  {
		if(!is_string($scope)) {
			throw new \InvalidArgumentException('Invalid scope '.serialize($scope));
		}

		if(!isset($this->servers[$scope]) || !is_array($this->servers[$scope])) {
			return array();
		}

		return $this->servers[$scope];
	}

	/**
	 * @return boolean
	 */
	public function isValid() {
		try {
			$this->validate();
		}catch(Exception\InvalidConfigurationException $e) {
			return false;
		}

		return true;
	}

	/**
	 * @return boolean
	 * throws Exception\InvalidConfigurationException
	 */
	abstract function validate();
}