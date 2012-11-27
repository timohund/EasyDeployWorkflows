<?php

namespace EasyDeployWorkflows\Tasks;

use EasyDeployWorkflows\Workflows;


/**
 * A task that is executed on one or many servers
 */
abstract class AbstractServerTask extends AbstractTask {

	/**
	 * @var array
	 */
	protected $servers;

	/**
	 * Adds a server on which this task should be executed
	 */
	public function addServer(\EasyDeploy_AbstractServer $server) {
		$this->servers[] = $server;
	}

	/**
	 * Adds a server on which this task should be executed
	 */
	public function addServerByName($server) {
		$this->addServer($this->getServer($server));
	}

	/**
	 * Adds servers on which this task should be executed
	 */
	public function addServersByName(array $servers) {
		foreach ($servers as $server) {
			$this->addServerByName($server);
		}
	}


	/**
	 * @param TaskRunInformation $taskRunInformation
	 * @return mixed
	 */
	public function run(TaskRunInformation $taskRunInformation) {
		$this->validate();
		if (empty($this->servers)) {
			$this->out('No servers to execute the task available',self::MESSAGE_TYPE_WARNING);
		}

		foreach ($this->servers as $server) {
			$this->runOnServer($taskRunInformation, $server);
		}
	}

	/**
	 * @param TaskRunInformation $taskRunInformation
	 * @return mixed
	 */
	abstract protected function runOnServer(TaskRunInformation $taskRunInformation,\EasyDeploy_AbstractServer $server);
}