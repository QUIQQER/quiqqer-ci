<?php

/**
 * This file contains \QUI\Ci\Builds\PhpDox
 */
namespace QUI\Ci\Builds;

use QUI;

/**
 * Class PhpDox
 *
 * @package QUI\Ci\Builds
 */
class PhpDox extends QUI\Ci\Build
{
    /**
     * constructor
     *
     * @param QUI\Ci\Project|Bool $Project - (optional) only needed for build and xml generation
     */
    public function __construct($Project = false)
    {
        parent::__construct($Project);

        $this->setAttributes(array(
            'name'        => 'phpdox',
            'title'       => 'PHP Class Documentation',
            'description' => 'Generate project documentation using phpDox',
            'depends'     => array('phploc-ci', 'phpcs-ci', 'phpmd', 'phpunit')
        ));
    }

    /**
     * Return the documentation link
     *
     * @return String|Bool
     */
    public function getLink()
    {
        if (!$this->_Project) {
            return '';
        }

        return $this->_Project->getUrlPath().'build/bin/docs';
    }

    /**
     * Build phpdox.xml
     */
    public function build()
    {
        if (!$this->_Project) {
            throw new QUI\Exception('No Project given. Could not build phpdox.xml');
        }

        $path = $this->_Project->getPath();
        $name = $this->_Project->getName();

        $phpdoxXml
            = '
<phpdox xmlns="http://xml.phpdox.net/config">

  <project name="'.$name.'" source="'.$path.'project/lib" workdir="${basedir}/build/api/xml">

    <collector backend="parser" />
    <generator output="${basedir}/build/bin/docs">

      <build engine="html" output="" />

      <enrich base="${basedir}/build">

        <source type="git">
            <git binary="/usr/bin/git" />
            <history enabled="true" limit="15" cache="logs/gitlog.xml" />
        </source>

        <source type="phploc">
          <file name="logs/phploc.xml" />
        </source>

        <source type="phpcs">
          <file name="logs/checkstyle.xml" />
        </source>

        <source type="pmd">
          <file name="logs/pmd.xml" />
        </source>

        <source type="phpunit">
            <coverage path="logs/coveragexml" />
        </source>

      </enrich>

    </generator>
  </project>

</phpdox>';

        file_put_contents($this->_Project->getPath().'phpdox.xml', $phpdoxXml);
    }

    /**
     * Return the build xml
     *
     * @return string
     */
    public function getXML()
    {
        return '
        <target name="phpdox"
            unless="phpdox.done"
            depends="phploc-ci,phpcs-ci,phpmd,phpunit"
            description="'.$this->getAttribute('description').'"
        >
            <delete dir="${builddir}/docs" />
            <exec command="${toolsdir}/phpdox -f ${project.basedir}/phpdox.xml" passthru="true" />
        </target>';
    }
}