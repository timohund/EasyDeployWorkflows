<?php

namespace EasyDeployWorkflows\Tasks\Common;

use EasyDeployWorkflows\Tasks;



class Download extends \EasyDeployWorkflows\Tasks\AbstractServerTask  {

	/**
	 * @var string
	 */
	protected $source;

	/**
	 * @param string $source
	 */
	public function setSource($source)
	{
		$this->source = $source;
	}

	/**
	 * @param string $target
	 */
	public function setTarget($target)
	{
		$this->target = $target;
	}

	/**
	 * @var string
	 */
	protected $target;

	/**
	 * @var \EasyDeploy_Helper_Downloader
	 */
	protected $downloader;

	public function __construct() {
		$this->injectDownloader(new \EasyDeploy_Helper_Downloader());
	}

	/**
	 * @param \EasyDeploy_Helper_Downloader $downloader
	 */
	public function injectDownloader(\EasyDeploy_Helper_Downloader $downloader) {
		$this->downloader = $downloader;
	}

	/**
	 * @param TaskRunInformation $taskRunInformation
	 * @return mixed
	 */
	protected function runOnServer(\EasyDeployWorkflows\Tasks\TaskRunInformation $taskRunInformation,\EasyDeploy_AbstractServer $server) {

		$source = $this->replaceConfigurationMarkers($this->source,$taskRunInformation->getWorkflowConfiguration(),$taskRunInformation->getInstanceConfiguration());
		$target = rtrim($this->replaceConfigurationMarkers($this->target,$taskRunInformation->getWorkflowConfiguration(),$taskRunInformation->getInstanceConfiguration()),DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;

		if ($source == $target) {
			$this->out('Source and Target are the same... skipping download');
			return;
		}
		$this->downloader->download($server,$source,$target);
		$this->out('Download ready');
	}

	/**
	 * @return boolean
	 * throws Exception\InvalidConfigurationException
	 */
	public function validate() {
		if (!isset($this->source)) {
			throw new \EasyDeployWorkflows\Exception\InvalidConfigurationException('source not set');
		}
		if (!isset($this->target)) {
			throw new \EasyDeployWorkflows\Exception\InvalidConfigurationException('target not set');
		}
		return true;
	}
}