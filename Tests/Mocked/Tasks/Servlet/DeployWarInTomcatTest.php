<?php

use EasyDeployWorkflows\Tasks as Tasks;
use EasyDeployWorkflows\Workflows as Workflows;

class DeployWarInTomcatTest extends AbstractMockedTest {

	/**
	 * @test
	 */
	public function canDeployWarFile() {
			//this dependency is only needed because other tasks require the web workflow
			/** @var $taskRunInformation  EasyDeployWorkflows\Tasks\TaskRunInformation */
		$taskRunInformation = new Tasks\TaskRunInformation();


		$task = new Tasks\Servlet\DeployWarInTomcat();
		$task->setTomcatUser('fred');
		$task->setTomcatPassword('feuerstein');
		$task->setTomcatPort(8090);
		$task->setTomcatPath('/Tracker');
		$task->setDownloadWarFile('/download/tracker.war');
		$task->setTmpWarFile('/tmp/tracker.war');


			/** @var $tomcatMock  EasyDeploy_RemoteServer */
		$tomcatMock	 = $this->getMock('EasyDeploy_RemoteServer',array(),array(),'',false);
		$tomcatMock->expects($this->any())->method('run')->will($this->returnCallback(
			function($command) {
				$isValidCommand = in_array(
					$command, array(
						'rm -f /tmp/tracker.war',
						'curl --upload-file /tmp/tracker.war -u fred:feuerstein "http://localhost:8090/manager/deploy?path=/Tracker&update=true"'
					)
				);

				if(!$isValidCommand) {
					$this->fail('Try to execute unexpected command during tomcat deployment '.$command);
				}
			}
		));
		$tomcatMock->expects($this->once())->method('copyLocalFile')->will(
			$this->returnCallback(
				function($source, $target) {
						//delivery folder foo
					$isExpectedSource = $source == '/download/tracker.war';
						//tmp source
					$isExpectedTarget = $target == '/tmp/tracker.war';

					$this->assertTrue($isExpectedSource,'Unexpected copy source for deployed servlet: '.$source);
					$this->assertTrue($isExpectedTarget,'Unexpected copy target for deployed servlet: '.$target);
				}
			)
		);

		$task->addServer($tomcatMock);
		$task->run($taskRunInformation);
	}
}