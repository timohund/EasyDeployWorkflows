<?php

namespace EasyDeployWorkflows\Workflows\Web;

use EasyDeployWorkflows\Workflows as Workflows;
use EasyDeployWorkflows\Workflows\Exception as Exception;


class WebConfiguration extends Workflows\AbstractWorkflowConfiguration {

	/**
	 * Name of the environment that should be used as master for backups.
	 *
	 * @var string
	 */
	protected $backupMasterEnvironment = '';

	/**
	 * @var string
	 */
	protected $minifiedBackupSource = '';

	/**
	 * @var string
	 */
	protected $apacheGroup = 'www-data';

	/**
	 * @param string $apacheGroup
	 * @return WebConfiguration
	 */
	public function setApacheGroup($apacheGroup) {
		$this->apacheGroup = $apacheGroup;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getApacheGroup() {
		return $this->apacheGroup;
	}

	/**
	 * @param boolean $backupMasterEnvironment
	 */
	public function setBackupMasterEnvironment($backupMasterEnvironment) {
		$this->backupMasterEnvironment = $backupMasterEnvironment;

		return $this;
	}

	/**
	 * @param string $targetEnvironment
	 * @return bool
	 */
	public function isBackupMasterEnvironment($targetEnvironment) {
		return $this->getBackupMasterEnvironment() === $targetEnvironment;
	}

	/**
	 * @return boolean
	 */
	public function getBackupMasterEnvironment() {
		return $this->backupMasterEnvironment;
	}

	/**
	 * @param string $webRoot
	 */
	public function setWebRootFolder($webRootFolder) {
		$this->setFolder($webRootFolder,'www',0);
		return $this;
	}

	/**
	 * @return bool
	 */
	public function hasWebRootFolder() {
		return $this->getWebRootFolder() != '';
	}

	/**
	 * @return string
	 */
	public function getWebRootFolder() {
		return $this->getFolder('www',0);
	}

	/**
	 * @param string $backupStorageRoot
	 * @return WebConfiguration
	 */
	public function setBackupStorageRootFolder($backupStorageRoot) {
		$this->setFolder($backupStorageRoot, 'backupstorage');
		return $this;
	}

	/**
	 * @return string
	 */
	public function getBackupStorageRootFolder() {
		return $this->getFolder('backupstorage');
	}

	/**
	 * @param string $backupStorageMinifiedRoot
	 * @return WebConfiguration
	 */
	public function setBackupStorageMinifiedRootFolder($backupStorageMinifiedRoot) {
		$this->setFolder($backupStorageMinifiedRoot, 'backupstorage_minified');
		return $this;
	}

	/**
	 * @return string
	 */
	public function getBackupStorageMinifiedRootFolder() {
		return $this->getFolder('backupstorage_minified');
	}

	/**
	 * @return array
	 */
	public function getWebServers() {
		return $this->getServers('www');
	}

	/**
	 * @return bool
	 */
	public function hasWebServers() {
		return count($this->getWebServers()) > 0;
	}

	/**
	 * @param string $hostName
	 * @return WebConfiguration
	 */
	public function addWebServer($hostName) {
		$this->addServer($hostName,'www');
		return $this;
	}

	/**
	 * @param string $indexerDataFolder
	 */
	public function setIndexerDataFolder($indexerDataFolder) {
		$this->setFolder($indexerDataFolder,'indexer',0);
		return $this;
	}

	/**
	 * @return string
	 */
	public function getIndexerDataFolder() {
		return $this->getFolder('indexer',0);
	}

	/**
	 * @return bool
	 */
	public function hasIndexerServers() {
		return count($this->getIndexerServers()) > 0;
	}

	/**
	 * @param string $hostName
	 * @return WebConfiguration
	 */
	public function addIndexerServer($hostName) {
		$this->addServer($hostName,'indexer');
		return $this;
	}

	/**
	 * @return array
	 */
	public function getIndexerServers() {
		return $this->getServers('indexer');
	}

	/**
	 * @param string $minifiedBackupSource
	 */
	public function setMinifiedBackupSource($minifiedBackupSource) {
		$this->minifiedBackupSource = $minifiedBackupSource;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function hasMinifiedBackupSource() {
		return $this->getMinifiedBackupSource() != '';
	}

	/**
	 * @return string
	 */
	public function getMinifiedBackupSource() {
		return $this->minifiedBackupSource;
	}

	/**
	 * @return string
	 */
	public function getWorkflowClassName() {
		return 'EasyDeployWorkflows\Workflows\Web\WebWorkflow';
	}

	/**
	 * @return bool
	 */
	public function validate() {
		if(!$this->hasWebServers()) {
			throw new Exception\InvalidConfigurationException("Please configure at least one web for workflow: ".get_class($this));
		}

		if(!$this->hasWebRootFolder()) {
			throw new Exception\InvalidConfigurationException("Please configure the webroot folder for workflow: ".get_class($this));
		}

		return true;
	}

}