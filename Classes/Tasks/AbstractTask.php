<?php

namespace EasyDeployWorkflows\Tasks;

use EasyDeployWorkflows\Workflows;


/**
 * A task is something that encapsulates a certain part of todo
 */
abstract class AbstractTask extends \EasyDeployWorkflows\AbstractPart implements \EasyDeployWorkflows\ValidateableInterface {


	/**
	 * @return boolean
	 */
	public function isValid() {
		try {
			$this->validate();
		}catch(\EasyDeployWorkflows\Exception\InvalidConfigurationException $e) {
			return false;
		}
		return true;
	}

	/**
	 * @return boolean
	 * throws Exception\InvalidConfigurationException
	 */
	abstract function validate();

	/**
	 * @param TaskRunInformation $taskRunInformation
	 * @return mixed
	 */
	abstract public function run(TaskRunInformation $taskRunInformation);

}