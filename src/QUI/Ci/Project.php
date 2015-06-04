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
     * Internal build settings
     *
     * @var Array
     */
    protected $_builds = array();

    /**
     * Internal settings
     *
     * @var Array
     */
    protected $_settings;

    /**
     * File for the xml settings
     *
     * @var string
     */
    protected $_settingFile;

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
        if (empty($name)) {
            throw new QUI\Exception('Project doesn\'t exist', 404);
        }

        $this->_path = Coordinator::getCiPath().$name.'/';
        $this->_settingFile = $this->_path.'settings.xml';

        if (!is_dir($this->_path)) {
            throw new QUI\Exception('Project doesn\'t exist', 404);
        }

        if (!file_exists($this->_settingFile)) {
            file_put_contents($this->_settingFile, '<quiqqer-ci></quiqqer-ci>');
        }

        $this->setAttribute('name', $name);

        $Settings = QUI\Utils\XML::getDomFromXml($this->_settingFile);

        $this->_builds = $this->_getBuildsBySettings($Settings);
        $this->_settings = $this->_getSettingsBySettings($Settings);

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

            $Build = $builds[$build];

            /* @var $Build \QUI\Ci\Build */
            $Build->setProject($this);
            $result[$build] = $builds[$build];
        }

        return $result;
    }

    /**
     * create setting xml files
     */
    public function save()
    {
        $QuiqqerCi = new \DOMElement('quiqqer-ci');
        $Builds = new \DOMElement('builds');
        $Settings = new \DOMElement('settings');

        foreach ($this->_builds as $build) {
            $Builds->appendChild(
                new \DOMElement('build', $build)
            );
        }

        $QuiqqerCi->appendChild($Builds);


        foreach ($this->_settings as $setting => $value) {
            $Setting = new \DOMElement('setting', $setting);
            $Setting->setAttribute('name', $value);
            $Settings->appendChild($Setting);
        }

        $QuiqqerCi->appendChild($Builds);


        $Dom = new \DOMDocument('1.0');
        $Dom->preserveWhiteSpace = false;
        $Dom->formatOutput = true;

        $Dom->appendChild($QuiqqerCi);

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
    public function enableBuild($build)
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
    public function disableBuild($build)
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
        return array(
            'builds'   => $this->_builds,
            'settings' => $this->_settings
        );
    }

    /**
     * Parse the settings for the specified settings
     *
     * @param \DOMDocument $Settings
     *
     * @return array
     */
    protected function _getSettingsBySettings(\DOMDocument $Settings)
    {
        $settings = $Settings->getElementsByTagName('setting');
        $result = array();

        /* @var $Setting \DOMElement */
        foreach ($settings as $Setting) {
            $result[$Setting->getAttribute('name')] = trim($Setting->nodeValue);
        }

        return $result;
    }

    /**
     * Parse the settings for the specified builds
     *
     * @param \DOMDocument $Settings
     *
     * @return array
     */
    protected function _getBuildsBySettings(\DOMDocument $Settings)
    {
        $builds = $Settings->getElementsByTagName('build');
        $result = array();

        foreach ($builds as $Build) {
            $result[] = trim($Build->nodeValue);
        }

        return $result;
    }
}
