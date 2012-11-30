<?php

use EasyDeployWorkflows\Workflows\Solr as Solr;


class SolrConfigurationTest extends AbstractMockedTest {

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