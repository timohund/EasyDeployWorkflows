<?php

namespace EasyDeployWorkflows\Tasks\Common;

use EasyDeployWorkflows\Tasks;



class RunScript extends \EasyDeployWorkflows\Tasks\AbstractServerTask  {

	/**
	 * @var string
	 */
	protected $script;

	/**
	 * @var boolean
	 */
	protected $isOptional = false;

	/**
	 * @param boolean $isOptional
	 */
	public function setIsOptional($isOptional) {
		$this->isOptional = $isOptional;
	}

	/**
	 * @param string $folder
	 */
	public function setScript($script) {
		$this->script = $script;
	}

	/**
	 * @param TaskRunInformation $taskRunInformation
	 * @return mixed
	 */
	protected function runOnServer(\EasyDeployWorkflows\Tasks\TaskRunInformation $taskRunInformation,\EasyDeploy_AbstractServer $server) {

		if (!$server->isFile($this->script) && !$this->isOptional) {
			$message = 'Try to run script that not exists '.htmlspecialchars($this->script);
			throw new \EasyDeployWorkflows\Exception\FileNotFoundException($message);
		}

		if ($server->isFile($this->script)) {
			$this->out('Run Script: "'.$this->script.'"', self::MESSAGE_TYPE_INFO);
			$server->run($this->script, TRUE);
		}

	}

	/**
	 * @return boolean
	 * @throws \EasyDeployWorkflows\Exception\InvalidConfigurationException
	 */
	public function validate() {
		if (!isset($this->script)) {
			throw new \EasyDeployWorkflows\Exception\InvalidConfigurationException('Script not set');
		}

		return true;
	}
}