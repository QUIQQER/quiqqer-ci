<?php

/**
 * This file contains \QUI\Ci\Builds\Lint
 */
namespace QUI\Ci\Builds;

use QUI;

/**
 * Class Lint
 *
 * @package QUI\Ci\Builds
 */
class Lint extends QUI\Ci\Build
{
    /**
     * constructor
     *
     * @param QUI\Ci\Project|Bool $Project - (optional) only needed for build and xml generation
     */
    public function __construct($Project=false)
    {
        $this->setAttributes(array(
            'name'        => 'lint',
            'description' => 'Check the syntax of PHP files'
        ));
    }

    /**
     * Return the build xml
     * @return string
     */
    public function getXML()
    {
        return '
        <target name="lint" description="'.$this->getAttribute('description').'">
            <mkdir dir="${builddir}/cache" />
            <phplint cachefile="${builddir}/cache/phplint.cache">
                <fileset dir="${srcdir}">
                    <include name="**/*.php"/>
                </fileset>
                <fileset dir="${testdir}">
                    <include name="**/*.php"/>
                </fileset>
            </phplint>
        </target>';
    }
}