<?php

use EasyDeployWorkflows\Workflows\Servlet as Servlet;
use EasyDeployWorkflows\Workflows as Workflows;

require_once EASYDEPLOY_WORKFLOW_ROOT.'Classes/Workflows/AbstractConfiguration.php';
require_once EASYDEPLOY_WORKFLOW_ROOT.'Classes/Workflows/AbstractWorkflow.php';
require_once EASYDEPLOY_WORKFLOW_ROOT . 'Classes/Workflows/Servlet/ServletConfiguration.php';
require_once EASYDEPLOY_WORKFLOW_ROOT . 'Classes/Workflows/Servlet/ServletWorkflow.php';

class ServletWorkflowTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var ServletWorkflow
	 */
	protected $workflow;

	/**
	 *@var Servlet\ServletConfiguration
	 */
	protected $workflowConfiguration;

	/**
	 * @var InstanceConfiguration
	 */
	protected $instanceConfiguration;

	/**
	 * @var
	 */
	protected $downloaderMock = null;
	/**
	 *
	 * @test
	 * @return void
	 */
	public function canDeployToTwoTomcatServers() {
		$this->workflowConfiguration = new Servlet\ServletConfiguration();
		$this->instanceConfiguration = new Workflows\InstanceConfiguration();

		$this->workflowConfiguration
				->setInstallSilent(false)
				->addServletServer('solr.company.com')
				->setTomcatPort(8080)
				->setTomcatUsername('foo')
				->setTomcatPassword('bar')
				->setTomcatVersion('6.0.12')
				->setDeploymentPackageSource('/home/homer.simpson');

		$this->instanceConfiguration
				->setProjectName('nasa')
				->addAllowedDeployServer('localhost')
				->setEnvironmentName('deploy')
				->setDeliveryFolder('/home/download');

		$this->downloaderMock = $this->getMock('EasyDeploy_Helper_Downloader',array(),array(),'',false);

		/**
		 * @var $this->workflow ServletWorkflow
		 */
		$this->workflow = $this->getMock(
			'EasyDeployWorkflows\Workflows\Servlet\ServletWorkflow',
			array('getServer'),
			array($this->instanceConfiguration, $this->workflowConfiguration),
			''
		);

		$this->workflow->injectDownloader($this->downloaderMock);

		//todo

	}

}