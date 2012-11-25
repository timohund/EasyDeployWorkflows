<?php

namespace EasyDeployWorkflows;

require_once dirname(__FILE__) . '/Exception/InvalidConfigurationException.php';


interface ValidateableInterface {

	/**
	 * @return boolean
	 */
	public function isValid();

	/**
	 * @return boolean
	 * throws \EasyDeployWorkflows\Exception\InvalidConfigurationException
	 */
	public function validate();

}