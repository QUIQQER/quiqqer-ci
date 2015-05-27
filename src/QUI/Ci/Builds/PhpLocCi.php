<?php

/**
 * This file contains \QUI\Ci\Builds\PhpLocCi
 */
namespace QUI\Ci\Builds;

use QUI;

/**
 * Class PhpLocCi
 *
 * @package QUI\Ci\Builds
 */
class PhpLocCi extends QUI\Ci\Build
{
    /**
     * constructor
     *
     * @param QUI\Ci\Project|Bool $Project - (optional) only needed for build and xml generation
     */
    public function __construct($Project = false)
    {
        $this->setAttributes(array(
            'name'        => 'phploc-ci',
            'description' =>
                'Measure project size using PHPLOC and log result in CSV and XML format. '
                .'Intended for usage within a continuous integration environment.',
            'depends'     => array('prepare')
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
                depends="prepare"
                description="'.$this->getAttribute('description').'"
        >
            <exec command="${toolsdir}/phploc --count-tests --log-csv ${builddir}/logs/phploc.csv --log-xml ${builddir}/logs/phploc.xml ${srcdir}" />
            <property name="phploc.done" value="true"/>
        </target>';
    }
}