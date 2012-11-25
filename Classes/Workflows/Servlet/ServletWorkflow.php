<?php

namespace EasyDeployWorkflows\Workflows\Servlet;

use EasyDeployWorkflows\Workflows as Workflows;

class ServletWorkflow extends Workflows\AbstractWorkflow {

	/**
	 * @var \EasyDeployWorkflows\Workflows\Servlet\ServletConfiguration
	 */
	protected $workflowConfiguration;

	/**
	 * @var string
	 */
	const CURL_DEPLOY_COMMAND = 'curl --upload-file %s -u %s "http://localhost:%s/manager/deploy?path=%s&update=true"';

	/**
	 * @param string $releaseVersion
	 * @return mixed|void
	 */
	public function deploy() {
		$localServer 				= $this->getServer('localhost');

		$deliveryFolder				= $this->replaceMarkers($this->instanceConfiguration->getDeliveryFolder());
		$downloadSource = $this->replaceMarkers($this->workflowConfiguration->getDeploymentSource());
		$this->downloader->download($localServer,$downloadSource,$deliveryFolder);

		$downloadedWarFile 			= $deliveryFolder.pathinfo($downloadSource,PATHINFO_BASENAME);
		$tmpWarLocation 			= '/tmp/'.pathinfo($downloadSource,PATHINFO_BASENAME);

		$tomcatAuthorization		= $this->workflowConfiguration->getTomcatUsername().':'.
									  $this->workflowConfiguration->getTomcatPassword();
		$tomcatPort					= $this->workflowConfiguration->getTomcatPort();
		$targetPath					= $this->workflowConfiguration->getTargetPath();

		$curlCommand 				= sprintf(self::CURL_DEPLOY_COMMAND, $tmpWarLocation,$tomcatAuthorization,$tomcatPort,$targetPath);

		foreach ($this->workflowConfiguration->getServletServers() as $servletServer) {
			$server = $this->getServer($servletServer);
			$server->run('rm -f '.$tmpWarLocation);
			$server->copyLocalFile($downloadedWarFile, $tmpWarLocation);
			$server->run($curlCommand);
		}
	}
}