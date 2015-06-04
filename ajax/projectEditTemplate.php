<?php

/**
 * Return the project edit template
 *
 * @return string
 */
function package_quiqqer_quiqqerci_ajax_projectEditTemplate()
{
    $Engine = QUI::getTemplateManager()->getEngine(true);
    $builds = QUI\Ci\Coordinator::getAvailableBuilds();

    $Engine->assign(array(
        'builds' => $builds
    ));

    return $Engine->fetch(dirname(__FILE__).'/projectEditTemplate.html');
}

QUI::$Ajax->register(
    'package_quiqqer_quiqqerci_ajax_projectEditTemplate',
    array('folder'),
    'Permission::checkAdminUser'
);
