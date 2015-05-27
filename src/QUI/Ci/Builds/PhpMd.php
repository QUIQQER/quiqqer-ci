<?php

/**
 * This file contains \QUI\Ci\Builds\PhpMd
 */
namespace QUI\Ci\Builds;

use QUI;

/**
 * Class PhpMd
 *
 * @package QUI\Ci\Builds
 */
class PhpMd extends QUI\Ci\Build
{
    /**
     * constructor
     *
     * @param QUI\Ci\Project|Bool $Project - (optional) only needed for build and xml generation
     */
    public function __construct($Project = false)
    {
        $this->setAttributes(array(
            'name'        => 'phpmd',
            'description' => ''
        ));
    }

    /**
     * Return the build xml
     *
     * @return string
     */
    public function getXML()
    {
        return '<target name="phpmd">
            <exec command="${toolsdir}/phpmd ${srcdir} xml codesize,unusedcode,naming --reportfile \'${builddir}/logs/pmd.xml\'"
                passthru="true"
            />
        </target>';
    }
}
