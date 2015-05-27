<?php

/**
 * This file contains \QUI\Ci\Builds\PhpCbf
 */
namespace QUI\Ci\Builds;

use QUI;

/**
 * Class PhpCbf
 *
 * @package QUI\Ci\Builds
 */
class PhpCbf extends QUI\Ci\Build
{
    /**
     * constructor
     *
     * @param QUI\Ci\Project|Bool $Project - (optional) only needed for build and xml generation
     */
    public function __construct($Project = false)
    {
        $this->setAttributes(array(
            'name'        => 'phpcbf',
            'description' =>
                'Fix coding standard violations using PHP_CodeSniffer. '
                .'Intended for usage on the command line before committing.'
        ));
    }

    /**
     * Return the build xml
     *
     * @return string
     */
    public function getXML()
    {
        return '<target name="phpcbf"
            description="'.$this->getAttribute('description').'"
        >
            <exec command="${toolsdir}/phpcbf ${srcdir} ${testdir} --standard=PSR1,PSR2 -n" passthru="true" />
        </target>';
    }
}
