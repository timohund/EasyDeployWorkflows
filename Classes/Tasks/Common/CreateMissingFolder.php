<?php

namespace EasyDeployWorkflows\Tasks\Common;

use EasyDeployWorkflows\Tasks;



class CreateMissingFolder extends \EasyDeployWorkflows\Tasks\AbstractServerTask  {

	/**
	 * @var string
	 */
	protected $folder;

	/**
	 * @param string $folder
	 */
	public function setFolder($folder)
	{
		$this->folder = $folder;
	}

	/**
	 * @param TaskRunInformation $taskRunInformation
	 * @return mixed
	 */
	protected function runOnServer(\EasyDeployWorkflows\Tasks\TaskRunInformation $taskRunInformation,\EasyDeploy_AbstractServer $server) {
		if (!$server->isDir($this->folder)) {
			$message = 'Expected Folder is not present! Try to create "'.$this->folder.'"';
			$this->out($message, self::MESSAGE_TYPE_INFO);

			$server->run('mkdir -p '.$this->folder);
			$server->run('chmod g+rws '.$this->folder);
		}

		if (!$server->isDir($this->folder)) {
			$message = 'Folder  "'.$this->folder.'" is not present! Could not create!';
			$this->out($message, self::MESSAGE_TYPE_ERROR);
			throw new \Exception('Folder on Node "'.$server->getHostname().'" is not present!');
		}

	}

	/**
	 * @return boolean
	 * throws Exception\InvalidConfigurationException
	 */
	public function validate() {
		if (!isset($this->folder)) {
			throw new \EasyDeployWorkflows\Exception\InvalidConfigurationException('Folder not set');
		}
		return true;
	}
}