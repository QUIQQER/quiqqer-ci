<?php

/**
 * This file contains \QUI\Ci\Cron
 */
namespace QUI\Ci;

use QUI;

/**
 * Class Cron
 *
 * @package QUI\Ci
 */
class Cron
{
    /**
     * Run the build process for a quiqqer-ci project
     *
     * @param Array             $params
     * @param \QUI\Cron\Manager $CronManager
     */
    static function runBuild($params, $CronManager)
    {
        if (!isset($params['ci-project'])) {
            return;
        }

        $Tool = new Console\Project();
        $Project = new QUI\Ci\Project($params['ci-project']);

        $Tool->build($Project);
    }

    /**
     * Run the build process for all quiqqer-ci projects
     *
     * @param Array             $params
     * @param \QUI\Cron\Manager $CronManager
     */
    static function runAll($params, $CronManager)
    {
        $Coordinator = new Coordinator();
        $projects = $Coordinator->getProjectlist();

        foreach ($projects as $project) {
            self::runAll(array(
                'ci-project' => $project
            ), $CronManager);
        }
    }
}