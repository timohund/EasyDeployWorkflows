<?php

use EasyDeployWorkflows\Workflows\Servlet;

require_once EASYDEPLOY_WORKFLOW_ROOT . 'Classes/Autoloader.php';

class ServletConfigurationTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var EasyDeployWorkflows\Workflows\Servlet\ServletConfiguration
	 */
	protected $configuration;

	/**
	 * @return void
	 */
	public function setUp() {
		$this->configuration = new EasyDeployWorkflows\Workflows\Servlet\ServletConfiguration();
	}

	/**
	 * @test
	 */
	public function addServletServers() {
		$this->assertEquals(array(), $this->configuration->getServletServers());
		$this->assertEquals(array('tomcat.tld'),$this->configuration->addServletServer('tomcat.tld')->getServletServers());;
	}
}