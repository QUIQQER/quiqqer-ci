<?php

/**
 * This file contains \QUI\Ci\Console
 */
namespace QUI\Ci;

use QUI;

/**
 * Class Project
 *
 * @package QuiqqerCi
 */
class Console extends QUI\System\Console\Tool
{
    /**
     * Internal CI Coordinator
     * @var Coordinator
     */
    protected $_Coordinator;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->setName('quiqqer:quiqqerci')
             ->setDescription('QUIQQER-CI Console')
             ->addArgument('list', 'List all project', false, true)
             ->addArgument('add', 'Add a new project [--add=git@]', false, true);

        $this->_Coordinator = new Coordinator();
    }

    /**
     * Execute the CI console
     */
    public function execute()
    {
        if ($this->getArgument('list')) {
            $this->_listProject();
            return;
        }

        if ($this->getArgument('add')) {
            $name = $this->_Coordinator->addProject($this->getArgument('add'));

            $this->writeLn('Project successfull added: '. $name, 'green');
            $this->writeLn();
            return;
        }
    }

    /**
     * List all CI projects
     */
    protected function _listProject()
    {
        $list = $this->_Coordinator->getProjectlist();

        $this->writeLn('CI Projects:', 'brown');
        $this->resetColor();

        for ($i = 0, $len = count($list); $i < $len; $i++) {
            $this->writeLn("- [{$i}] {$list[$i]}");
        }

        $this->writeLn();
        $this->writeLn();
    }
}
