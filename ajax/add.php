<?php

/**
 * Add a ci project
 *
 * @param string $url - Path to the project url
 * @return string - folder of the new project
 */
function package_quiqqer_quiqqerci_ajax_add($projecturl)
{
    $Coordinator = new QUI\Ci\Coordinator();

    return $Coordinator ->addProject($projecturl);
}

QUI::$Ajax->register(
    'package_quiqqer_quiqqerci_ajax_add',
    array('projecturl'),
    'Permission::checkAdminUser'
);
