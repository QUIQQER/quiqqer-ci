<?php

/**
 * Return data of the ci project
 *
 * @param string $folder - folder of the project
 * @param string $data - JSON Data
 * @return array
 */
function package_quiqqer_quiqqerci_ajax_save($folder, $data)
{
    $CiProject = new \QUI\Ci\Project($folder);
    $data = json_decode($data, true);

    $CiProject->setSettings($data['settings']);
    $CiProject->setBuilds($data['builds']);
    $CiProject->save();
}

QUI::$Ajax->register(
    'package_quiqqer_quiqqerci_ajax_save',
    array('folder', 'data'),
    'Permission::checkAdminUser'
);
