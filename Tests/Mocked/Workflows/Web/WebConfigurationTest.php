<?php

use EasyDeployWorkflows\Workflows\Web;

require_once EASYDEPLOY_WORKFLOW_ROOT . 'Classes/Autoloader.php';

class WebConfigurationTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var EasyDeployWorkflows\Workflows\Web\NFSWebConfiguration
	 */
	protected $configuration;

	/**
	 * @return void
	 */
	public function setUp() {
		$this->configuration = new EasyDeployWorkflows\Workflows\Web\NFSWebConfiguration();
	}

	/**
	 * @test
	 */
	public function canSetAndGetWebRoot() {
		$this->configuration->setWebRootFolder('foo');
		$this->assertEquals('foo', $this->configuration->getWebRootFolder(),'could not retrieve web root folder');
	}

	/**
	 * @test
	 */
	public function canChainSet() {
		$this->configuration->setWebRootFolder('foo')->setWebRootFolder('bar');
		$this->assertEquals('bar', $this->configuration->getWebRootFolder(),'Could not overwrite webroor');
	}

	/**
	 * @test
	 */
	public function addWebServer() {
		$defaultNodes = $this->configuration->getWebServers();
		$this->assertEquals($defaultNodes, array(), 'Webnodes not empty by default');

		$this->configuration
				->addWebServer('web1.hostname.com')
				->addWebServer('web2.hostname.com')
				->addWebServer('web3.hostname.com');

		$webNodes = $this->configuration->getWebServers();

		$this->assertEquals($webNodes, array(
			'web1.hostname.com','web2.hostname.com','web3.hostname.com'
		),'Unable to add web nodes');
	}

	/**
	 * @test
	 * @expectedException \InvalidArgumentException
	 */
	public function addWebServerThrowsExceptionForInvalidArgument() {
		$this->configuration->addWebServer(array(),'Configuration is not throwing exception on wrong argument');
	}

	/**
	 * @test
	 * @expectedException \InvalidArgumentException
	 */
	public function cantAddSameServerTwice() {
		$this->configuration->addWebServer('web1.hostname.com')->addWebServer('web1.hostname.com');
	}

	/**
	 * @test
	 */
	public function canSetIndexerStorageFolder() {
		$this->configuration->setIndexerDataFolder('/var/lib/indexer');
		$this->assertEquals($this->configuration->getIndexerDataFolder(),'/var/lib/indexer','Could not get indexer data folder');
	}

	/**
	 * @test
	 */
	public function setInstallSilent() {
		$this->assertFalse($this->configuration->getInstallSilent(),'Unexpected install silent default');
		$this->assertTrue($this->configuration->setInstallSilent(true)->getInstallSilent(),'Could not set install silent');
	}

	/**
	 * @test
	 */
	public function setDeploymentPackageSource() {
		$this->assertEquals($this->configuration->getDeploymentSource(),'');
	}

	/**
	 * @test
	 */
	public function setBackupMasterEnvironment() {
		$this->assertEquals($this->configuration->getBackupMasterEnvironment(),'');
		$environment = $this->configuration->setBackupMasterEnvironment('deploy')->getBackupMasterEnvironment();
		$this->assertEquals($environment, 'deploy','Could not set backup master environment');
	}

	/**
	 * @test
	 */
	public function setApacheGroup() {
		$this->assertEquals('www-data',$this->configuration->getApacheGroup(),'Unexpected default apache user group');
		$this->assertEquals('wwwrun',$this->configuration->setApacheGroup('wwwrun')->getApacheGroup(),'could not set different apache user group');
	}

	/**
	 * @test
	 */
	public function setBackupStorageRootFolder() {
		$this->assertEquals('', $this->configuration->getBackupStorageRootFolder(),'Unexpected backup storage root folder');
		$this->assertEquals('/var/lib', $this->configuration->setBackupStorageRootFolder('/var/lib')->getBackupStorageRootFolder());
	}

	/**
	 * @test
	 */
	public function setBackupStorageMinifiedRootFolder() {
		$this->assertEquals('', $this->configuration->getBackupStorageMinifiedRootFolder(),'Unexpected backup storage root folder');
		$this->assertEquals('/var/lib.tgz', $this->configuration->setBackupStorageMinifiedRootFolder('/var/lib.tgz')->getBackupStorageMinifiedRootFolder());
	}

	/**
	 * @test
	 */
	public function testIsBackupMasterEnvironment() {
		$this->assertFalse($this->configuration->isBackupMasterEnvironment('deploy'),'Deploy was detected as backup master environment for configuration without configured master environment');
		$this->assertTrue($this->configuration->setBackupMasterEnvironment('deploy')->isBackupMasterEnvironment('deploy'));
	}

	/**
	 * @test
	 */
	public function canAddIndexerServer() {
		$this->assertEquals(array(),$this->configuration->getIndexerServers(),'Could not retrieve indexer servers');
	}

}