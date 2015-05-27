<?php

/**
 * This file contains \QUI\Ci\Build
 */
namespace QUI\Ci;

use QUI;

/**
 * Class Build - Parentclass for a build
 *
 * @package QUI\Ci
 */
abstract class Build extends QUI\QDOM
{
    /**
     * constructor
     *
     * @param QUI\Ci\Project|Bool $Project - (optional) only needed for build and xml generation
     */
    public function __construct($Project=false)
    {
        $this->_Project = $Project;
    }

    /**
     * @return String|Bool
     */
    public function getName()
    {
        return $this->getAttribute('name');
    }

    /**
     * @return String|Bool
     */
    public function getDescription()
    {
        return $this->getAttribute('description');
    }

    /**
     * Set the project for the build
     *
     * @param Project $Project
     */
    public function setProject(QUI\Ci\Project $Project)
    {
        $this->_Project = $Project;
    }

    /**
     * Build method
     * This method are called at the xml build, if a build need some extra xml files or settings
     *
     * @throws QUI\Exception
     */
    public function build()
    {
        if (is_null($this->_Project)) {
            throw new QUI\Exception('Could not build project. No quiqqer-ci Project given.');
        }


    }

    /**
     * Return the buil xml
     *
     * @return String
     *
     * @throws QUI\Exception
     */
    public function getXML()
    {
        if (is_null($this->_Project)) {
            throw new QUI\Exception('Could not build xml. No quiqqer-ci Project given.');
        }

        return '';
    }
}
