<?php

use EasyDeployWorkflows\Workflows\Servlet;

class ServletConfigurationTest extends AbstractMockedTest {

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