<?php

/**
 * This file contains \QUI\Ci\Builds\PhpLoc
 */
namespace QUI\Ci\Builds;

use QUI;

/**
 * Class PhpLoc
 *
 * @package QUI\Ci\Builds
 */
class PhpLoc extends QUI\Ci\Build
{
    /**
     * constructor
     *
     * @param QUI\Ci\Project|Bool $Project - (optional) only needed for build and xml generation
     */
    public function __construct($Project = false)
    {
        $this->setAttributes(array(
            'name'        => 'phploc',
            'description' =>
                'Measure project size using PHPLOC and print human readable output. '
                .'Intended for usage on the command line.'
        ));
    }

    /**
     * Return the build xml
     *
     * @return string
     */
    public function getXML()
    {
        return '
        <target name="'.$this->getAttribute('name').'"
                unless="phploc.done"
                description="'.$this->getAttribute('description').'">
            <exec command="${toolsdir}/phploc --count-tests ${srcdir}" passthru="true" />
            <property name="phploc.done" value="true"/>
        </target>';
    }
}