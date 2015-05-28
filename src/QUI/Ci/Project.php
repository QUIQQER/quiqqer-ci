<?php

/**
 * This file contains \QUI\Ci\Project
 */
namespace QUI\Ci;

use QUI;

/**
 * Class Project
 *
 * @package QUI\Ci
 */
class Project extends QUI\QDOM
{
    /**
     * @var Array
     */
    protected $_builds = array();

    /**
     * File for the xml settings
     *
     * @var string
     */
    protected $_settingFile;

    /**
     * @var \DOMDocument
     */
    protected $_Settings;

    /**
     * Project path
     *
     * @var String
     */
    protected $_path;

    /**
     * constructor
     *
     * @param String $name - name of the project
     *
     * @throws QUI\Exception
     */
    public function __construct($name)
    {
        $this->_path = Coordinator::getCiPath().$name.'/';
        $this->_settingFile = $this->_path.'settings.xml';

        if (!is_dir($this->_path)) {
            throw new QUI\Exception('Project doesn exist', 404);
        }

        if (!file_exists($this->_settingFile)) {
            file_put_contents($this->_settingFile, '<quiqqer-ci></quiqqer-ci>');
        }

        $this->_Settings = QUI\Utils\XML::getDomFromXml($this->_settingFile);
        $this->_builds = $this->_getBuildsBySettings();
        $this->setAttribute('name', $name);

        if (file_exists($this->getPath().'project/composer.json')) {
            $composerJson = json_decode(
                file_get_contents($this->getPath().'project/composer.json'),
                true
            );

            $this->setAttribute('composer', $composerJson);
            $this->setAttribute('name', $composerJson['name']);
            $this->setAttribute('description', $composerJson['description']);
        }
    }

    /**
     * Return the project name
     *
     * @return String
     */
    public function getName()
    {
        return $this->getAttribute('name');
    }

    /**
     * Return the project description
     *
     * @return String
     */
    public function getDescription()
    {
        return $this->getAttribute('description');
    }

    /**
     * Return the project path
     *
     * @return String
     */
    public function getPath()
    {
        return $this->_path;
    }

    /**
     * Return the project path
     *
     * @return String
     */
    public function getUrlPath()
    {
        return str_replace(USR_DIR, URL_USR_DIR, $this->getPath());
    }

    /**
     * Return the Readme
     *
     * @return string
     */
    public function getReadme()
    {
        $projectPath = $this->getPath().'project/';

        $files = array('README.md', 'README.txt', 'README');

        foreach ($files as $file) {
            if (file_exists($projectPath.$file)) {
                return file_get_contents($projectPath.$file);
            }

            if (file_exists($projectPath.mb_strtolower($file))) {
                return file_get_contents($projectPath.mb_strtolower($file));
            }
        }

        return '';
    }

    /**
     * Return the project builds
     *
     * @return Array
     */
    public function getBuilds()
    {
        $result = array();
        $builds = Coordinator::getAvailableBuilds();

        foreach ($this->_builds as $build) {
            if (!isset($builds[$build])) {
                continue;
            }

            $builds[$build]->setProject($this);

            $result[$build] = $builds[$build];
        }

        return $result;
    }

    /**
     * create setting xml files
     */
    public function save()
    {
        $xml = '<quiqqer-ci>';

        foreach ($this->_builds as $build) {
            $xml .= '<build>'.$build.'</build>';
        }

        $xml .= '</quiqqer-ci>';

        $Dom = new \DOMDocument('1.0');
        $Dom->preserveWhiteSpace = false;
        $Dom->loadXML($xml);
        $Dom->formatOutput = true;

        file_put_contents($this->_settingFile, $Dom->saveXML());
    }

    /**
     * Enable a build for the project
     *
     * @param String $build - name of the build
     *
     * @return Bool
     *
     * @throws QUI\Exception
     */
    public function enableSetting($build)
    {
        $available = Coordinator::getAvailableBuilds();

        if (in_array($build, $this->_builds)) {
            return true;
        }

        if (!isset($available[$build])) {
            throw new QUI\Exception('Build not exist');
        }

        $this->_builds[] = $build;
        $this->save();

        return true;
    }

    /**
     * Disable a build for the project
     *
     * @param String $build - name of the build
     *
     * @return Bool
     *
     * @throws QUI\Exception
     */
    public function disableSetting($build)
    {
        $available = Coordinator::getAvailableBuilds();

        if (!in_array($build, $this->_builds)) {
            return true;
        }

        if (!isset($available[$build])) {
            throw new QUI\Exception('Build not exist');
        }

        $key = array_search($build, $this->_builds);

        if ($key !== false) {
            unset($this->_builds[$key]);
        }

        $this->save();

        return true;
    }

    /**
     * Return the builds of the project
     *
     * @return Array
     */
    public function getSettings()
    {
        return $this->_builds;
    }

    /**
     * Return the specified builds
     *
     * @return array
     */
    protected function _getBuildsBySettings()
    {
        $builds = $this->_Settings->getElementsByTagName('build');
        $result = array();

        foreach ($builds as $Build) {
            $result[] = trim($Build->nodeValue);
        }

        return $result;
    }
}
