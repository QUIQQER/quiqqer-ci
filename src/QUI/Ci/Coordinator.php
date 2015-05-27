<?php

/**
 * This file contains \QUI\Ci\Coordinator
 */
namespace QUI\Ci;

use PDepend\Util\Log;
use QUI;

/**
 * Class Coordinator
 *
 * @package QuiqqerCi
 */
class Coordinator
{
    /**
     * Return the path for the ci projects
     *
     * @return string
     */
    static public function getCiPath()
    {
        return USR_DIR.'quiqqer-ci/';
    }

    /**
     * Return the available build scripts
     * What can be done?
     *
     * @return Array
     */
    static public function getAvailableBuilds()
    {
        $dir = dirname(__FILE__).'/';
        $builds = QUI\Utils\System\File::readDir($dir.'Builds/');
        $list = array();

        foreach ($builds as $build) {

            $build = '\\QUI\\Ci\\Builds\\'.str_replace('.php', '', $build);

            if (!class_exists($build)) {
                continue;
            }

            $Build = new $build();

            $list[$Build->getAttribute('name')] = $Build;
        }

        return $list;
    }

    /**
     * Project methods
     */

    /**
     * Add a new project
     *
     * @param String $projectUrl - URL of the project (eq: git@***.git)
     *
     * @return String - Name of the project
     *
     * @throws QUI\Exception
     */
    public function addProject($projectUrl)
    {
        if (strpos($projectUrl, 'git@') === 0) {
            return $this->_addGitProject($projectUrl);
        }

        throw new QUI\Exception('This URL is not supported');
    }

    /**
     * @param $project
     */
    public function removeProject($project)
    {

    }

    /**
     * Return the installed projects / libs
     *
     * @return array
     */
    public function getProjectlist()
    {
        return QUI\Utils\System\File::readDir(self::getCiPath());
    }

    /**
     * Add a git repository to the project list
     *
     * @param $giturl
     *
     * @return String
     *
     * @throws QUI\Exception
     */
    protected function _addGitProject($giturl)
    {
        $giturl = str_replace('git@', 'http://', $giturl);
        $giturl = str_replace(':', '/', $giturl);
        $giturl = str_replace('http///', 'http://', $giturl);

        $url = parse_url($giturl);
        $url['path'] = trim($url['path'], '/');

        $projectName = str_replace(array('/', '.'), '_', $url['path']);
        $projectPath = $this->getCiPath().$projectName;

        QUI\Utils\System\File::mkdir($projectPath);

        if (is_dir($projectPath.'/project/')) {
            throw new QUI\Exception('Project exist. Could not clone');
        }

        chdir($projectPath);

        // clone git
        system('git clone '.$giturl.' project');

        return $projectName;
    }
}
