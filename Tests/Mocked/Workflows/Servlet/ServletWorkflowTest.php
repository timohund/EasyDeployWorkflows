<?php

use EasyDeployWorkflows\Workflows\Servlet as Servlet;
use EasyDeployWorkflows\Workflows as Workflows;

require_once EASYDEPLOY_WORKFLOW_ROOT . 'Classes/Autoloader.php';

class ServletWorkflowTest extends PHPUnit_Framework_TestCase {

	/**
	 *
	 * @test
	 * @return void
	 */
	public function canDeployToTwoTomcatServers() {
		$workflowConfiguration = new Servlet\ServletConfiguration();
		$instanceConfiguration = new Workflows\InstanceConfiguration();

		$workflowConfiguration
				->addServletServer('solr1.company.com')
				->addServletServer('solr2.company.com')
				->setTomcatPort(8080)
				->setTomcatUsername('foo')
				->setTomcatPassword('bar')
				->setTomcatVersion('6.0.12')
				->setDeploymentSource('/home/homer.simpson/###releaseversion###/somedownloadpackage.tar.gz')
				->setInstallSilent(false)
				->setReleaseVersion('4711');

		$instanceConfiguration
				->setProjectName('nasa')
				->addAllowedDeployServer('localhost')
				->setEnvironmentName('deploy')
				->setDeliveryFolder('/home/download/###releaseversion###');

			/** @var $workflow  EasyDeployWorkflows\Workflows\Servlet\ServletWorkflow */
		$workflow = $this->getMock(
			'EasyDeployWorkflows\Workflows\Servlet\ServletWorkflow',
			array('getServer'),
			array($instanceConfiguration, $workflowConfiguration),
			''
		);

		$localServerMock	 = $this->getMock('EasyDeploy_LocalServer',array(),array(),'',false);
		$solr1ServerMock	 = $this->getMock('EasyDeploy_RemoteServer',array('copyLocalFile','run'),array(),'',false);
		$solr2ServerMock	 = $this->getMock('EasyDeploy_RemoteServer',array('copyLocalFile','run'),array(),'',false);

		$workflow->expects($this->exactly(3))->method('getServer')->will($this->returnCallback(
			function($hostName) use ($localServerMock, $solr1ServerMock, $solr2ServerMock)  {
				if($hostName == 'localhost') {
					return $localServerMock;
				} elseif($hostName == 'solr1.company.com') {
					return $solr1ServerMock;
				} elseif($hostName == 'solr2.company.com') {
					return $solr2ServerMock;
				}
			}
		));

			//will the download be triggered with the expected arguments ?
		$downloaderMock = $this->getMock('EasyDeploy_Helper_Downloader',array('download'),array(),'',false);
		$downloaderMock->expects($this->once())->method('download')->with(
			$localServerMock,'/home/homer.simpson/4711/somedownloadpackage.tar.gz','/home/download/4711/'
		);

		$workflow->injectDownloader($downloaderMock);


			//does the deploy service execute the expected commands on the remote solr servers?
		$solr1ServerMock->expects($this->at(2))->method('run')->with('curl --upload-file /tmp/somedownloadpackage.tar.gz -u foo:bar "http://localhost:8080/manager/deploy?path=&update=true"');
		$solr2ServerMock->expects($this->at(2))->method('run')->with('curl --upload-file /tmp/somedownloadpackage.tar.gz -u foo:bar "http://localhost:8080/manager/deploy?path=&update=true"');
		$workflow->deploy();
	}

}