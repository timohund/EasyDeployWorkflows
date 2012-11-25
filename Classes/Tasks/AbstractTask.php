<?php

namespace EasyDeployWorkflows\Tasks;

use EasyDeployWorkflows\Workflows;



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