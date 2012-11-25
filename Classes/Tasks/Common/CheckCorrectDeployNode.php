<?php

namespace EasyDeployWorkflows\Tasks\Common;

use EasyDeployWorkflows\Tasks;



class CheckCorrectDeployNode extends \EasyDeployWorkflows\Tasks\AbstractTask  {

	/**
	 * @param TaskRunInformation $taskRunInformation
	 * @return mixed
	 */
	public function run(\EasyDeployWorkflows\Tasks\TaskRunInformation $taskRunInformation) {
		$localServer = $this->getServer('localhost');

		if(!$taskRunInformation->getInstanceConfiguration()->isAllowedDeployServer($localServer->getHostname())) {
			throw new \Exception("Unallowed deploy server ".$localServer->getHostname());
		}

	}

	/**
	 * @return boolean
	 * throws Exception\InvalidConfigurationException
	 */
	public function validate() {
		return true;
	}
}