<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}
if (TYPO3_MODE == 'BE') {
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['GLOBAL']['cliKeys']['phpstorm_metadata'] = array(
		'EXT:phpstorm/Scripts/CommandLineLauncher.php',
		'_CLI_phpstorm'
	);
}
?>