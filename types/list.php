<?php

$Coordinator = new \QUI\Ci\Coordinator();
$list = $Coordinator->getProjectlist();
$ciProjectlist = array();

foreach ($list as $entry) {
    $ciProjectlist[] = new \QUI\Ci\Project($entry);
}


$Engine->assign('ciProjectlist', $ciProjectlist);