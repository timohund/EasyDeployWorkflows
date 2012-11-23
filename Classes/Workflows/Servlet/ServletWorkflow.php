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
	public function deploy($releaseVersion) {
		$localServer 				= $this->getServer('localhost');
		$deploymentPackageSource	= $this->workflowConfiguration->getDeploymentSource();
		$deliveryFolder				= $this->instanceConfiguration->getDeliveryFolder();

		$downloadSource 			= sprintf($deploymentPackageSource, $releaseVersion);
		$this->downloader->download($localServer,$downloadSource,$deliveryFolder.'/'.$releaseVersion);

		$downloadedWarFile 			= $deliveryFolder.'/'.$releaseVersion.'/'.pathinfo($downloadSource,PATHINFO_BASENAME);
		$tmpWarLocation 			= '/tmp/'.pathinfo($downloadSource,PATHINFO_BASENAME);

		$tomcatAuthorization		= $this->workflowConfiguration->getTomcatUsername().':'.
									  $this->workflowConfiguration->getTomcatPassword();
		$tomcatPort					= $this->workflowConfiguration->getTomcatPort();
		$targetPath					= $this->workflowConfiguration->getTargetPath();

		$curlCommand 				= sprintf(self::CURL_DEPLOY_COMMAND, $tmpWarLocation,$tomcatAuthorization,$tomcatPort,$targetPath);

		foreach ($this->workflowConfiguration->getServletServers() as $servletServer) {
			$server = $this->getServer($servletServer);
			$server->copyLocalFile($downloadedWarFile, $tmpWarLocation);
			$server->run($curlCommand);
		}
	}
}