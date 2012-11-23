<?php

namespace EasyDeployWorkflows\Workflows\Web;

use EasyDeployWorkflows\Workflows as Workflows;

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
	 */
	public function setBackupStorageRootFolder($backupStorageRoot) {
		return $this->setFolder($backupStorageRoot, 'backupstorage');
	}

	/**
	 * @return string
	 */
	public function getBackupStorageRootFolder() {
		return $this->getFolder('backupstorage');
	}

	/**
	 * @param string $backupStorageMinifiedRoot
	 */
	public function setBackupStorageMinifiedRootFolder($backupStorageMinifiedRoot) {
		return $this->setFolder($backupStorageMinifiedRoot, 'backupstorage_minified');
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
	 */
	public function addWebServer($hostName) {
		return $this->addServer($hostName,'www');
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
	 */
	public function addIndexerServer($hostName) {
		return $this->addServer($hostName,'indexer');
	}

	/**
	 * @return array
	 */
	public function getIndexerServers() {
		return $this->getServers('indexer');
	}


	/**
	 * @return bool
	 */
	public function isValid() {
		return $this->hasWebServers() && $this->hasWebRootFolder();
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
}