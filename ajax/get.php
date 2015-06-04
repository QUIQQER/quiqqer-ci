<?php

/**
 * Return data of the ci project
 *
 * @param string $folder - folder of the project
 * @return array
 */
function package_quiqqer_quiqqerci_ajax_get($folder)
{
    $CiProject = new \QUI\Ci\Project($folder);

    return array(
        'name'        => $CiProject->getName(),
        'description' => $CiProject->getDescription(),
        'path'        => $CiProject->getPath(),
        'settings'    => $CiProject->getSettings(),
    );
}

QUI::$Ajax->register(
    'package_quiqqer_quiqqerci_ajax_get',
    array('folder'),
    'Permission::checkAdminUser'
);
