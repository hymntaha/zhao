<?php

require_once('class.GitHubHook.php');

$hook = new GitHubHook;
$hook->enableDebug();
$hook->addBranch('master', 'web1', '/var/www/bravo');
$hook->deploy();
