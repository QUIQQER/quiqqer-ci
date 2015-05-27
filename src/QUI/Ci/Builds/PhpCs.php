<?php

/**
 * This file contains \QUI\Ci\Builds\PhpCs
 */
namespace QUI\Ci\Builds;

use QUI;

/**
 * Class PhpCs
 *
 * @package QUI\Ci\Builds
 */
class PhpCs extends QUI\Ci\Build
{
    /**
     * constructor
     *
     * @param QUI\Ci\Project|Bool $Project - (optional) only needed for build and xml generation
     */
    public function __construct($Project = false)
    {
        $this->setAttributes(array(
            'name'        => 'phpcs',
            'description' =>
                'Find coding standard violations and print human readable output.'
                .
                'Intended for usage on the command line before committing.'
        ));
    }

    /**
     * Return the build xml
     *
     * @return string
     */
    public function getXML()
    {
        return '<exec executable="${toolsdir}/phpcs" output="/dev/null">
            <arg value="--standard=PSR1,PSR2" />
            <arg value="--extensions=php" />
            <arg path="${srcdir}" />
            <arg path="${testdir}" />
        </exec>';
    }
}