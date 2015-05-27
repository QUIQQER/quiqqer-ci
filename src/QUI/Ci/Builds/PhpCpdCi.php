<?php

/**
 * This file contains \QUI\Ci\Builds\PhpCpdCi
 */
namespace QUI\Ci\Builds;

use QUI;

/**
 * Class PhpCpdCi
 *
 * @package QUI\Ci\Builds
 */
class PhpCpdCi extends QUI\Ci\Build
{
    /**
     * constructor
     *
     * @param QUI\Ci\Project|Bool $Project - (optional) only needed for build and xml generation
     */
    public function __construct($Project = false)
    {
        $this->setAttributes(array(
            'name'        => 'phpcpd-ci',
            'description' =>
                'Find duplicate code using PHPCPD and log result in XML format. '
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
            unless="phpcpd.done"
            depends="prepare"
            description="'.$this->getAttribute('name').'"
        >
            <exec command="${toolsdir}/phpcpd --log-pmd ${builddir}/logs/pmd-cpd.xml ${srcdir}"
                  passthru="true"
            />

            <property name="phpcpd.done" value="true"/>
        </target>';
    }
}