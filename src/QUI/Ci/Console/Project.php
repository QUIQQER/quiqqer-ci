<?php

/**
 * This file contains \QUI\Ci\Console\Project
 */
namespace QUI\Ci\Console;

use QUI;

/**
 * Class Project
 * Console tool for a CI Project. Set or enable project-ci settings
 *
 * @package QUI\Ci
 */
class Project extends QUI\System\Console\Tool
{
    /**
     * Internal CI Coordinator
     *
     * @var QUI\Ci\Coordinator
     */
    protected $_Coordinator;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->setName('quiqqer:quiqqerci-project')
             ->setDescription('QUIQQER-CI Project Console. Enable or disable project settings')
             ->addArgument('ci-project', 'Name or number of the project', 'cip')
             ->addArgument('disableBuild',
                 'Disable build. --disableBuild=[Name of the build]',
                 false,
                 true
             )
             ->addArgument('enableBuild',
                 'Enable build. --enableBuild=[Name of the build]', false, true)
             ->addArgument('list-build',
                 'List all available build scripts and the builds for the project',
                 false,
                 true
             )
             ->addArgument('run', 'Run the build', false, true);

        $this->_Coordinator = new QUI\Ci\Coordinator();
    }

    /**
     * Execute the CI console
     */
    public function execute()
    {
        $project = $this->getArgument('ci-project');
        $list = $this->_Coordinator->getProjectlist();

        if (is_numeric($project)) {
            if (isset($list[$project])) {
                $project = $list[$project];
            } else {
                throw new QUI\Exception('Project not found');
            }
        }

        $Project = new QUI\Ci\Project($project);


        if ($this->getArgument('enableBuild')) {

            $Project->enableBuild($this->getArgument('enableBuild'));
            $this->writeLn();

            return;
        }


        if ($this->getArgument('disableBuild')) {

            $Project->disableBuild($this->getArgument('disableBuild'));
            $this->writeLn();

            return;
        }


        if ($this->getArgument('list-build')) {

            $this->writeLn();

            $builds = $this->_Coordinator->getAvailableBuilds();
            $settings = $Project->getSettings();
            $buildSettings = $settings['builds'];

            $checkmark = "\342\234\223";
            $xmark = "\342\234\227";

            /* @var $Build \QUi\Ci\Build */
            foreach ($builds as $build => $Build) {

                if (in_array($build, $buildSettings)) {
                    $char = $checkmark;
                } else {
                    $char = $xmark;
                }

                $this->writeLn("{$char} ".$Build->getAttribute('name'));

                if ($Build->getAttribute('description')) {
                    $this->writeLn($Build->getAttribute('description'));
                }

                $this->writeLn();
            }

            $this->writeLn();
            $this->writeLn();

            return;
        }

        if ($this->getArgument('--run')) {
            $this->build($Project);
        }
    }

    /**
     * Run the build process
     *
     * @param QUI\Ci\Project $Project
     *
     * @throws QUI\Exception
     */
    public function build(QUI\Ci\Project $Project)
    {
        $this->writeLn('Run build for ', 'purple');
        $this->write($Project->getName());
        $this->writeLn('=================================');

        $this->writeLn();
        $this->writeLn('Generate build.xml', 'brown');
        $this->resetColor();

        $projectBasedir = $Project->getPath();
        $composerJson = array();

        $depends = array();
        $settings = $Project->getSettings();
        $projectBuilds = $settings['builds'];
        $availableBuilds = $this->_Coordinator->getAvailableBuilds();

        //
        // build depends
        //
        foreach ($projectBuilds as $build) {

            if (!isset($availableBuilds[$build])) {
                throw new QUI\Exception('Build '.$build.' not found', 404);
            }

            /* @var $Build \QUi\Ci\Build */
            $Build = $availableBuilds[$build];

            $depends[] = $Build->getAttribute('name');

            if ($Build->getAttribute('depends')) {
                $depends = array_merge($depends,
                    $Build->getAttribute('depends'));
            }
        }

        $depends = array_unique($depends);

        // composer
        if (file_exists($projectBasedir.'project/composer.json')) {
            $composerJson = json_decode(
                file_get_contents($projectBasedir.'project/composer.json'),
                true
            );
        }

        //
        // Builds XML
        //
        $projectBuildsXml = '';

        foreach ($depends as $depend) {

            $Build = $availableBuilds[$depend];

            $this->writeLn('- '.$Build->getAttribute('name'));

            $Build->setProject($Project);
            $Build->build();

            $this->write('...[ok]');

            $projectBuildsXml .= $Build->getXML()."\n";
        }

        $this->writeLn();


        //
        // XML generation - build.xml
        //
        $projectName = $Project->getName();
        $projectDesc = $composerJson['description'];

        $projectSrcdir
            = $projectBasedir.'project/'.$Project->getSetting('srcPath');

        $projectTestdir
            = $projectBasedir.'project/'.$Project->getSetting('phpunitPath');


        $buildXml
            = '<?xml version="1.0" encoding="UTF-8"?>
<!-- This file is generated by QUIQQER # '.date('Y/m/d H:i:s').' # -->
<project name="'.$projectName.'" default="build">

    <property name="projectName" value="'.$projectDesc.'"/>

    <property name="moduledir" value="'.$projectBasedir.'project"/>
    <property name="srcdir" value="'.$projectSrcdir.'"/>
    <property name="testdir" value="'.$projectTestdir.'"/>

    <property name="toolsdir" value="'.OPT_DIR.'bin"/>
    <property name="builddir" value="'.$projectBasedir.'build/"/>

    <target name="build" depends="prepare,'.implode(',', $depends).'" />

    <target name="clean"
            unless="clean.done"
            description="Cleanup build artifacts"
    >
        <delete dir="${builddir}/cache"/>
        <delete dir="${builddir}/logs"/>
        <delete dir="${builddir}/api"/>
        <delete dir="${builddir}/bin/docs"/>
        <delete dir="${builddir}/bin/coverage"/>

        <property name="clean.done" value="true"/>
    </target>

    <target name="prepare"
            unless="prepare.done"
            depends="clean"
            description="Prepare for build"
    >
        <mkdir dir="${builddir}/cache"/>
        <mkdir dir="${builddir}/logs"/>
        <mkdir dir="${builddir}/api"/>
        <mkdir dir="${builddir}/bin/docs"/>
        <mkdir dir="${builddir}/bin/coverage"/>

        <property name="prepare.done" value="true"/>
    </target>

    '.$projectBuildsXml.'
</project>';


        $Dom = new \DOMDocument('1.0');
        $Dom->preserveWhiteSpace = false;
        $Dom->loadXML($buildXml);
        $Dom->formatOutput = true;

        file_put_contents($projectBasedir.'build.xml', $Dom->saveXML());

        //
        // run the build process
        //
        $this->writeLn('Run build', 'brown');
        $this->writeLn();
        $this->resetColor();

        chdir($Project->getPath());
        system(OPT_DIR.'bin/phing');

        $this->writeLn();
    }
}
