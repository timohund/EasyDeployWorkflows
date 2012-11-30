<?php

if(!defined('EASYDEPLOY_WORKFLOW_ROOT')) {
	define('EASYDEPLOY_WORKFLOW_ROOT', realpath( dirname(__FILE__)) . '/../');
}


require_once EASYDEPLOY_WORKFLOW_ROOT . 'Classes/Autoloader.php';
require_once EASYDEPLOY_WORKFLOW_ROOT . 'Tests/Mocked/AbstractMockedTest.php';

?>