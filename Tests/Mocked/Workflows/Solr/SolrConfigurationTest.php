<?php

use EasyDeployWorkflows\Workflows\Solr as Solr;

require_once EASYDEPLOY_WORKFLOW_ROOT . 'Classes/Autoloader.php';

class SolrConfigurationTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var $configuration Solr\SolrConfiguration
	 */
	protected $configuration;

	/**
	 * @return void
	 */
	public function setUp() {
		$this->configuration = new Solr\SolrConfiguration();
	}

	/**
	 * @test
	 */
	public function setInstancePath() {
		$this->configuration->setInstancePath('/opt/solr');
		$this->assertEquals('/opt/solr', $this->configuration->getInstancePath(),'Unexpected instance path');
	}

	/**
	 * @test
	 */
	public function setRestartCommand() {
		$this->assertEquals($this->configuration->getRestartCommand(),'','unexpected default restart script');
		$this->configuration->setRestartCommand('/etc/init.d/solr restart');
		$this->assertEquals('/etc/init.d/solr restart',$this->configuration->getRestartCommand(),'unexpected restart command');
	}
}