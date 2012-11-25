<?php

namespace EasyDeployWorkflows;

spl_autoload_register(__NAMESPACE__ .'\Autoloader::autoload');

/**
 * spl autoloader for EasyDeployWorkflows classes
 */
class Autoloader {
	/**
	 * spl autoloader
	 * @param $name classname
	 */
	static public function autoload($name) {
		#echo $name.' - '.PHP_EOL;
		if (strpos($name,'EasyDeployWorkflows') === 0) {
			$classPath = substr($name,strlen(__NAMESPACE__));
			$classPath = str_replace('\\',DIRECTORY_SEPARATOR,$classPath).'.php';
			require_once dirname(__FILE__).$classPath;
		}
	}
}