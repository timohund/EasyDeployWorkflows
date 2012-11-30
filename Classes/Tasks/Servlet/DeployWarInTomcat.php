<?php

namespace EasyDeployWorkflows\Tasks\Servlet;

use EasyDeployWorkflows\Tasks;

class DeployWarInTomcat extends \EasyDeployWorkflows\Tasks\AbstractServerTask  {

	/**
	 * Holds the tomcat version specific deployment commands
	 *
	 * @var string
	 */
	protected $deploymentCommands = array(
		'6' => 'curl --upload-file %s -u %s "http://%s:%s/manager/deploy?path=%s&update=true"',
		'7' => 'curl --upload-file %s -u %s "http://%s:%s/manager/text/deploy?path=%s&update=true"',
	);

	/**
	 * @var string
	 */
	protected $tomcatUser = '';

	/**
	 * @var string
	 */
	protected $tomcatPassword = '';

	/**
	 * @var string
	 */
	protected $tomcatPort = 0;

	/**
	 * @var string
	 */
	protected $tomcatPath = '';

	/**
	 * @var string
	 */
	protected $downloadWarFile = '';

	/**
	 * @var string
	 */
	protected $tmpWarFile = '';

	/**
	 * @var string
	 */
	protected $tomcatHostname = 'localhost';

	/**
	 * @var string
	 */
	protected $tomcatVersion = '6';

	/**
	 * @param string $tomcatPassword
	 */
	public function setTomcatPassword($tomcatPassword) {
		$this->tomcatPassword = $tomcatPassword;
	}

	/**
	 * @return string
	 */
	public function getTomcatPassword() {
		return $this->tomcatPassword;
	}

	/**
	 * @param string $tomcatPath
	 */
	public function setTomcatPath($tomcatPath) {
		$this->tomcatPath = $tomcatPath;
	}

	/**
	 * @return string
	 */
	public function getTomcatPath() {
		return $this->tomcatPath;
	}

	/**
	 * @param string $tomcatPort
	 */
	public function setTomcatPort($tomcatPort) {
		$this->tomcatPort = $tomcatPort;
	}

	/**
	 * @return string
	 */
	public function getTomcatPort() {
		return $this->tomcatPort;
	}

	/**
	 * @param string $tomcatUser
	 */
	public function setTomcatUser($tomcatUser) {
		$this->tomcatUser = $tomcatUser;
	}

	/**
	 * @return string
	 */
	public function getTomcatUser() {
		return $this->tomcatUser;
	}

	/**
	 * @param string $downloadWarFile
	 */
	public function setDownloadWarFile($downloadWarFile) {
		$this->downloadWarFile = $downloadWarFile;
	}

	/**
	 * @return string
	 */
	public function getDownloadWarFile() {
		return $this->downloadWarFile;
	}

	/**
	 * @param string $tmpWarFile
	 */
	public function setTmpWarFile($tmpWarFile) {
		$this->tmpWarFile = $tmpWarFile;
	}

	/**
	 * @return string
	 */
	public function getTmpWarFile() {
		return $this->tmpWarFile;
	}

	/**
	 * @param string $tomcatHostname
	 */
	public function setTomcatHostname($tomcatHostname) {
		$this->tomcatHostname = $tomcatHostname;
	}

	/**
	 * @return string
	 */
	public function getTomcatHostname() {
		return $this->tomcatHostname;
	}

	/**
	 * @param string $tomcatVersion
	 */
	public function setTomcatVersion($tomcatVersion) {
		$this->tomcatVersion = $tomcatVersion;
	}

	/**
	 * @return string
	 */
	public function getTomcatVersion() {
		return $this->tomcatVersion;
	}

	/**
	 * @param TaskRunInformation $taskRunInformation
	 * @return mixed
	 */
	protected function runOnServer(Tasks\TaskRunInformation $taskRunInformation, \EasyDeploy_AbstractServer $server) {
		$curlCommand 				= sprintf(
			$this->deploymentCommands[$this->getTomcatVersion()],
			$this->getTmpWarFile(),
			$this->getTomcatUser().':'.$this->getTomcatPassword(),
			$this->getTomcatHostname(),
			$this->getTomcatPort(),
			$this->getTomcatPath()
		);

		$server->run('rm -f '.$this->getTmpWarFile());
		$server->copyLocalFile($this->getDownloadWarFile(),$this->getTmpWarFile());
		$server->run($curlCommand);
	}

	/**
	 * @return boolean
	 * throws Exception\InvalidConfigurationException
	 */
	function validate() {

		if($this->getTomcatVersion() != '6' && $this->getTomcatVersion() != '7') {
			throw new \EasyDeployWorkflows\Exception\InvalidConfigurationException('Unsupported tomcat version '.htmlspecialchars(
				$this->getTomcatVersion()
			));
		}

		if($this->getTomcatPort() == 0) {
			throw new \EasyDeployWorkflows\Exception\InvalidConfigurationException('Please configure a valid tomcat port: '.
				htmlspecialchar($this->getTomcatPort())
			);
		}

		if(trim($this->getDownloadWarFile()) == '') {
			throw new \EasyDeployWorkflows\Exception\InvalidConfigurationException('Please configure a source war file location');
		}

		if(trim($this->getTmpWarFile()) == '') {
			throw new \EasyDeployWorkflows\Exception\InvalidConfigurationException('Please configure a tmp war file location');
		}

		if(trim($this->getTomcatUser()) == '' || trim($this->getTomcatPassword()) == '') {
			throw new \EasyDeployWorkflows\Exception\InvalidConfigurationException('Please configure tomcat username and password for deployment to tomcat');
		}
	}
}