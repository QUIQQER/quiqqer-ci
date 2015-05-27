<?php

/**
 * This file contains \QUI\Ci\Console\Project
 */
namespace QUI\Ci\Console;

use QUI;
use Symfony\Component\Console\Helper\TableStyle;

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
        $this->setName('quiqqer:quiqqer-ci-project')
             ->setDescription('QUIQQER-CI Project Console. Enable or disable project settings')
             ->addArgument('ci-project', 'Name or number of the project', 'cip')
             ->addArgument('disable',
                 'Disable build. --disable=[Name of the build]', false, true)
             ->addArgument('enable',
                 'Enable build. --enable=[Name of the build]', false, true)
             ->addArgument('list-build',
                 'List all available build scripts and the builds for the project',
                 false, true);

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


        if ($this->getArgument('enable')) {

            $Project->enableSetting($this->getArgument('enable'));
            $this->writeLn();

            return;
        }


        if ($this->getArgument('disable')) {

            $Project->disableSetting($this->getArgument('disable'));
            $this->writeLn();

            return;
        }


        if ($this->getArgument('list-build')) {

            $Table = new Table();

            $builds = $this->_Coordinator->getAvailableBuilds();
            $settings = $Project->getSettings();

            $checkmark = "\342\234\223";
            $xmark = "\342\234\227";

            $this->writeLn();
            $output=array();
            foreach ($builds as $build => $Build) {

                if (in_array($build, $settings)) {
                    $char = $checkmark;
                } else {
                    $char = $xmark;
                }

                $output[] = array(
                    "{$char} ".$Build->getAttribute('name'),
                    $Build->getAttribute('description')
                );

//                $this->writeLn(
//                    "  {$char} ".$Build->getAttribute('name').' ('
//                    .$Build->getAttribute('description').')'
//                );
            }

            $Table->setRows($output);
            $Table->render();

            $this->writeLn();
            $this->writeLn();

            return;
        }
    }
}
