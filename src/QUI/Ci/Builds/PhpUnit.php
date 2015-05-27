<?php

/**
 * This file contains \QUI\Ci\Builds\PhpUnit
 */
namespace QUI\Ci\Builds;

use QUI;

/**
 * Class PhpUnit
 *
 * @package QUI\Ci\Builds
 */
class PhpUnit extends QUI\Ci\Build
{
    /**
     * constructor
     *
     * @param QUI\Ci\Project|Bool $Project - (optional) only needed for build and xml generation
     */
    public function __construct($Project = false)
    {
        $this->setAttributes(array(
            'name'        => 'phpunit',
            'description' => 'Run test suite'
        ));
    }

    /**
     * Return the build xml
     *
     * @return string
     */
    public function getXML()
    {
        return '<target
            name="'.$this->getAttribute('name').'"
            description="'.$this->getAttribute('name').'"

        >
            <delete dir="${builddir}/logs/coverage" />
            <mkdir dir="${builddir}/logs/coverage" />

            <exec command="${toolsdir}/phpunit --bootstrap ${testdir}/bootstrap.php --coverage-html ${builddir}/coverage --coverage-xml ${builddir}/logs/coveragexml ${testdir}" />
        </target>';
    }
}