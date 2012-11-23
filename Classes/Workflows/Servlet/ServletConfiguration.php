<?php
namespace EasyDeployWorkflows\Workflows\Servlet;

use EasyDeployWorkflows\Workflows as Workflows;

class ServletConfiguration extends Workflows\AbstractWorkflowConfiguration {

	/**
	 * Version number of the target tomcat system.
	 *
	 * @var string
	 */
	protected $tomcatVersion = '';

	/**
	 * @var string
	 */
	protected $tomcatUsername = '';

	/**
	 * @var string
	 */
	protected $tomcatPassword = '';

	/**
	 * Servlet container path
	 *
	 * @var int
	 */
	protected $tomcatPort = 0;

	/**
	 * The path where the servlet is visible from outside. Eg /solr
	 *
	 * @var string
	 */
	protected $targetPath = '';

	/**
	 * @param string $tomcatVersion
	 * @return ServletConfiguration
	 */
	public function setTomcatVersion($tomcatVersion) {
		$this->tomcatVersion = $tomcatVersion;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getTomcatVersion() {
		return $this->tomcatVersion;
	}

	/**
	 * @param string $tomcatPassword
	 * @return ServletConfiguration
	 */
	public function setTomcatPassword($tomcatPassword) {
		$this->tomcatPassword = $tomcatPassword;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getTomcatPassword() {
		return $this->tomcatPassword;
	}

	/**
	 * @param string $tomcatUsername
	 * @return ServletConfiguration
	 */
	public function setTomcatUsername($tomcatUsername) {
		$this->tomcatUsername = $tomcatUsername;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getTomcatUsername()	{
		return $this->tomcatUsername;
	}

	/**
	 * @param int $tomcatPort
	 * @return ServletConfiguration
	 */
	public function setTomcatPort($tomcatPort) {
		$this->tomcatPort = $tomcatPort;

		return $this;
	}

	/**
	 * @return int
	 */
	public function getTomcatPort() {
		return $this->tomcatPort;
	}

	/**
	 * @param string $hostname
	 * @return EasyDeployWorkflows\Workflows\Servlet\ServletConfiguration
	 */
	public function addServletServer($hostName) {
		return $this->addServer($hostName,'servlet');
	}

	/**
	 * @return bool
	 */
	public function hasServletServers() {
		return count($this->getServletServers()) > 0;
	}

	/**
	 * @return array
	 */
	public function getServletServers() {
		return $this->getServers('servlet');
	}

	/**
	 * @param string $targetPath
	 * @return ServletConfiguration
	 */
	public function setTargetPath($targetPath) {
		$this->targetPath = $targetPath;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getTargetPath() {
		return $this->targetPath;
	}

	/**
	 * @return boolean
	 */
	public function isValid() {
		return $this->getTomcatVersion() != '' && $this->hasServletServers() && $this->tomcatPassword != '' && $this->tomcatPassword != '';
	}
}