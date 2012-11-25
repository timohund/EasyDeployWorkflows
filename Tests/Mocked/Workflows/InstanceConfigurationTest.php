<?php

use EasyDeployWorkflows\Workflows;

require_once EASYDEPLOY_WORKFLOW_ROOT . 'Classes/Autoloader.php';

class InstanceConfigurationTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var EasyDeployWorkflows\Workflows\InstanceConfiguration
	 */
	protected $instanceConfiguration;

	/**
	 * @return void
	 */
	public function setUp() {
		$this->instanceConfiguration = new EasyDeployWorkflows\Workflows\InstanceConfiguration();
	}

	/**
	 * @test
	 */
	public function testAddAllowedDeployServer() {
		$this->assertEquals(array(), $this->instanceConfiguration->getAllowedDeployServers(),'unexpected default allowed deploy servers');
		$this->instanceConfiguration->addAllowedDeployServer('localhost');
		$this->assertEquals(array('localhost'),$this->instanceConfiguration->getAllowedDeployServers(),'Unable to retrieve passed allowed deployment servers');
	}

	/**
	 * @test
	 */
	public function testHasAllowedDeployServers() {
		$this->assertFalse($this->instanceConfiguration->hasAllowedDeployServers(),'Empty configuration should not have an allowed deploy server');
		$this->instanceConfiguration->addAllowedDeployServer('www.google.de');
		$this->assertTrue($this->instanceConfiguration->hasAllowedDeployServers());
	}

	/**
	 * @test
	 */
	public function testIsAllowedDeploymentServer() {
		$this->assertFalse($this->instanceConfiguration->isAllowedDeployServer('www.google.de'));
	}
}