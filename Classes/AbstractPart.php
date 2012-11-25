<?php

namespace EasyDeployWorkflows;

use EasyDeployWorkflows\Workflows;

abstract class AbstractPart {

	/**
	 * @var string
	 */
	const MESSAGE_TYPE_WARNING = "MESSAGE_TYPE_WARNING";

	/**
	 * @var string
	 */
	const MESSAGE_TYPE_ERROR = "MESSAGE_TYPE_ERROR";

	/**
	 * @var string
	 */
	const MESSAGE_TYPE_INFO = "MESSAGE_TYPE_INFO";

	/**
	 * @param string $message
	 * @param string $type
	 */
	protected function out($message, $type=self::MESSAGE_TYPE_INFO) {
		if (class_exists('EasyDeploy_Utils')) {
			$this->outWithEasyDeploy($message, $type);
		}
		else {
			echo $message.PHP_EOL;
		}

	}

	/**
	 * @param $message
	 * @param $type
	 */
	private function outWithEasyDeploy($message, $type) {
		$transformedType = \EasyDeploy_Utils::MESSAGE_TYPE_INFO;
		if ($type == self::MESSAGE_TYPE_WARNING) {
			$transformedType = \EasyDeploy_Utils::MESSAGE_TYPE_WARNING;
		}
		if ($type == self::MESSAGE_TYPE_ERROR) {
			$transformedType = \EasyDeploy_Utils::MESSAGE_TYPE_ERROR;
		}
		echo \EasyDeploy_Utils::formatMessage($message,$transformedType).PHP_EOL;
	}

	/**
	 * @param string $serverName
	 * @return \EasyDeploy_LocalServer|\EasyDeploy_RemoteServer
	 */
	protected function getServer($serverName) {
		if ($serverName == 'localhost') {
			return new \EasyDeploy_LocalServer($serverName);
		}

		return new \EasyDeploy_RemoteServer($serverName);
	}

	/**
	 * @param $string
	 * @return mixed
	 */
	protected function replaceConfigurationMarkers($string, \EasyDeployWorkflows\Workflows\AbstractConfiguration $workflowConfiguration, \EasyDeployWorkflows\Workflows\InstanceConfiguration $instanceConfiguration) {
		$string = str_replace('###releaseversion###',$workflowConfiguration->getReleaseVersion(),$string);
		$string = str_replace('###environment###',$instanceConfiguration->getEnvironmentName(),$string);
		$string = str_replace('###projectname###',$instanceConfiguration->getProjectName(),$string);
		return $string;
	}

}