<?php

/**
 * This file contains \QUI\Ci\Builds\PhpCpd
 */
namespace QUI\Ci\Builds;

use QUI;

/**
 * Class PhpCpd
 *
 * @package QUI\Ci\Builds
 */
class PhpCpd extends QUI\Ci\Build
{
    /**
     * constructor
     *
     * @param QUI\Ci\Project|Bool $Project - (optional) only needed for build and xml generation
     */
    public function __construct($Project = false)
    {
        $this->setAttributes(array(
            'name'        => 'phpcpd',
            'description' =>
                'Find duplicate code using PHPCPD and log result in XML format. '
                .'Intended for usage within a continuous integration environment.'
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
                unless="phpcpd.done"
                description="'. $this->getAttribute('name') .'">
            <exec executable="${toolsdir}/phpcpd">
                <arg path="${srcdir}" />
            </exec>

            <property name="phpcpd.done" value="true"/>
        </target>';
    }
}