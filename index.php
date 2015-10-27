<?php 
require_once('view/LayoutView.php');
require_once('controller/Run.php');
require_once('Settings.php');

// Errors.
error_reporting(E_ALL);
if (Settings::DISPLAY_ERRORS)
{
	ini_set('display_errors', 'On');
}
else {
	ini_set('display_errors', 'Off');
}

$run = new Run();
$lv = new LayoutView();

$run->start();
$lv->render($run->getGameHTML());