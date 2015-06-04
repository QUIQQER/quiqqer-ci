<?php

/**
 * Return the ci project list
 *
 * @return array
 */
function package_quiqqer_quiqqerci_ajax_list()
{
    $Coordinator = new QUI\Ci\Coordinator();
    $list = $Coordinator->getProjectlist();
    $result = array();

    foreach ($list as $project) {

        $CiProject = new QUI\Ci\Project($project);

        $result[] = array(
            'folder'      => $project,
            'name'        => $CiProject->getName(),
            'description' => $CiProject->getDescription(),
            'path'        => $CiProject->getPath(),
            'settings'    => $CiProject->getSettings(),
        );
    }

    return $result;
}

QUI::$Ajax->register(
    'package_quiqqer_quiqqerci_ajax_list',
    false,
    'Permission::checkAdminUser'
);
