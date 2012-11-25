What is EasyDeployWorkflows?
=====================

EasyDeployWorkflows offers a concept to develop Deployment Workflows on Top of EasyDeploy


Concept
-------------

Each component of your Application is deployed using a Workflow.
A workflow needs 3 Values to run:
* A Workflow Configuration (Specific Object for that Workflow)
* A InstanceConfiguration (Holding common Data about environment and the project)
* A releaseVersion

The idea ist, that the Workflows can be reused for deploying different projects to different environments.
Managing the differences between the environments comes down to manage the WorkflowConfiguration



Deployment Scripts Example
------------------------------

We recommend this structure:
* deploy.php (your central deployment script)
* EasyDeploy (EasyDeploy Submodule)
* EasyDeployWorkflows (EasyDeployWorkflows submodule)
* Configuration
* * [Projectname]
* * * [Instancename].php


The deploy.php triggers your deployment:
::
	<?php
	require_once dirname(__FILE__) . '/EasyDeployWorkflows/Classes/Autoloader.php';
    require_once dirname(__FILE__) . '/EasyDeploy/Classes/Utils.php';
    EasyDeploy_Utils::includeAll();

    $WebDeploymentWorkflow = $workflowFactory->createByConfigurationVariable($project,$environment,$releaseVersion, 'webWorkflowConfiguration');
    $WebDeploymentWorkflow->deploy($releaseVersion);



Configuration Example
------------------------------

::
	<?php
	$instanceConfiguration = new \EasyDeployWorkflows\Workflows\InstanceConfiguration();
    $instanceConfiguration
    	->setDeliveryFolder('/home/systemstorage/###projectname###/deliveries/###releaseversion###/')
    	->setProjectName('saascluster');



    $webWorkflowConfiguration = new \EasyDeployWorkflows\Workflows\Web\NFSWebConfiguration();
    $webWorkflowConfiguration
    	->setWebRootFolder('/var/www/###projectname###/###environment###')
    	->setBackupMasterEnvironment('production')
    	->setBackupStorageRootFolder('/home/systemstorage/systemstorage/saascluster/backup/')
    	->setDeploymentSource('https://username:password@yourContinuousDeploymentServer/artifacts/ProjectsArtifactRepository/preparedReleases/###releaseversion###/application.tar.gz')
    	->setInstallSilent(true);

