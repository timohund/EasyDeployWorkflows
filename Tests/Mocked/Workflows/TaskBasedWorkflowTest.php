<?php

class TaskBasedWorkflowTest extends AbstractMockedTest {

	/**
	 * @var \EasyDeployWorkflows\Workflows\TaskBasedWorkflow
	 */
	protected $stepBasedWorkflow;

	/**
	 * @return void
	 */
	public function setUp() {
		$this->instanceConfigurationMock = $this->getMock('EasyDeployWorkflows\Workflows\InstanceConfiguration');
		$this->workflowConfigurationMock = $this->getMock('EasyDeployWorkflows\Workflows\AbstractWorkflowConfiguration');
		$this->stepBasedWorkflow = $this->getMock('EasyDeployWorkflows\Workflows\TaskBasedWorkflow',array('out'),array($this->instanceConfigurationMock, $this->workflowConfigurationMock));
	}

	/**
	 * @test
	 */
	public function canCallDeploy() {
		$this->stepBasedWorkflow->deploy();
	}

	/**
	 * @test
	 */
	public function canAssignValidStep() {
		$step = $this->getMock('EasyDeployWorkflows\Tasks\Common\CreateMissingFolder',array('run','validate'));
		$this->stepBasedWorkflow->addTask('teststep', $step);
		$step->expects($this->once())->method('run');
		$this->stepBasedWorkflow->deploy();
	}

	/**
	 * @test
	 */
	public function canThrowExceptionOnDuplicateStepAssignment() {
		$this->setExpectedException('EasyDeployWorkflows\Workflows\Exception\DuplicateStepAssignmentException');
		$step = $this->getMock('EasyDeployWorkflows\Tasks\Common\CreateMissingFolder',array('validate','run'));
		$step->expects($this->once())->method('validate')->will($this->returnValue(true));
		$this->stepBasedWorkflow->addTask('teststep', $step);
		$this->stepBasedWorkflow->addTask('teststep', $step);

	}

	/**
	 * @
	 */
	public function canThrowExceptionOnInValidStepAssignment() {
		$this->setExpectedException('\EasyDeployWorkflows\Exception\InvalidConfigurationException');
		$step = $this->getMock('EasyDeployWorkflows\Tasks\Common\CreateMissingFolder',array('validate','run'));
		$step->expects($this->once())->method('validate')->will($this->throwException(new \EasyDeployWorkflows\Exception\InvalidConfigurationException() ));
		$this->stepBasedWorkflow->addTask('teststep', $step);
	}
}