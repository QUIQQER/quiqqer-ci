<?php

/**
 * Return the ci project list
 *
 * @return array
 */
function package_quiqqer_quiqqerci_ajax_list()
{
    $Coordinator = new \QUI\Ci\Coordinator();
    return $Coordinator->getProjectlist();
}

QUI::$Ajax->register(
    'package_quiqqer_quiqqerci_ajax_list',
    false,
    'Permission::checkAdminUser'
);
