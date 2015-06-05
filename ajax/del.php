<?php

/**
 * Remove / Delete a ci project
 *
 * @param string $folder - Folder of the project
 */
function package_quiqqer_quiqqerci_ajax_del($folder)
{
    $Coordinator = new QUI\Ci\Coordinator();
    $Coordinator ->deleteProject($folder);
}

QUI::$Ajax->register(
    'package_quiqqer_quiqqerci_ajax_del',
    array('folder'),
    'Permission::checkAdminUser'
);
