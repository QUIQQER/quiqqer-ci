<?php

/**
 * This file contains \QUI\Ci\Builds\PhpCsCi
 */
namespace QUI\Ci\Builds;

use QUI;

/**
 * Class PhpCsCi
 *
 * @package QUI\Ci\Builds
 */
class PhpCsCi extends QUI\Ci\Build
{
    /**
     * constructor
     *
     * @param QUI\Ci\Project|Bool $Project - (optional) only needed for build and xml generation
     */
    public function __construct($Project = false)
    {
        $this->setAttributes(array(
            'name'        => 'phpcs-ci',
            'description' =>
                'Find coding standard violations using PHP_CodeSniffer and print human readable output. '
                .'Intended for usage on the command line before committing.',
            'depends' => array('prepare')
        ));
    }

    /**
     * Return the build xml
     *
     * @return string
     */
    public function getXML()
    {
        return '<target name="'.$this->getAttribute('name').'"
                description="'.$this->getAttribute('description').'"
                depends="prepare"
                >
            <exec command="${toolsdir}/phpcs --report=checkstyle --report-file=${builddir}/logs/checkstyle.xml --standard=PSR1,PSR2 --extensions=php ${srcdir}"
                  passthru="true"
            />
        </target>';
    }
}