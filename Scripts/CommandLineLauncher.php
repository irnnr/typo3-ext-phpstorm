<?php
namespace TYPO3\CMS\Phpstorm;
/***************************************************************
*  Copyright notice
*
*  (c) 2013 Ingo Renner <ingo@typo3.org>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

require_once(dirname(__FILE__) . '/../Classes/CompatibilityUtility.php');

/**
 * CommandLineLauncher
 *
 * @package TYPO3\CMS\Phpstorm
 */
class CommandLineLauncher {

	/**
	 * @var array Array with command line parameter
	 */
	protected $cliArguments = array();

	/**
	 * Main function of the command line launcher
	 */
	public function cli_main() {
		if (TYPO3_REQUESTTYPE & TYPO3_REQUESTTYPE_CLI && basename(PATH_thisScript) == 'cli_dispatch.phpsh') {
			// Convert cli arguments to an array
			foreach ($_SERVER['argv'] as $value) {
				if ($value[0] === '-' && (string)intval($value) !== (string)$value) {
					list($argument, $argumentValue) = explode('=', $value, 2);
					if (isset($this->cliArguments[$argument])) {
						$this->cliEcho('ERROR: Option ' . $argument . ' was used twice!', TRUE);
						die;
					}
					$this->cliArguments[$argument] = $argumentValue ? $argumentValue : '';
				}
			}
			unset($value);

			/** @var MetaDataFileGenerator $generator */
			$generator = CompatibilityUtility::makeInstance('TYPO3\\CMS\\Phpstorm\\MetaDataFileGenerator');
			$this->handleCliArguments($generator);
			$generator->run();

			$peakMemory = memory_get_peak_usage(TRUE);
			$this->cliEcho('Done, memory used: ' . CompatibilityUtility::formatSize($peakMemory) . LF);
		} else {
			die('This script must be included by the "CLI module dispatcher"' . LF);
		}
	}

	/**
	 * Sets generator properties according to CLI arguments
	 *
	 * @param \TYPO3\CMS\Phpstorm\MetaDataFileGenerator $generator Generator to configure depending on command line parameter
	 */
	protected function handleCliArguments(MetaDataFileGenerator $generator) {
		foreach ($this->cliArguments as $name => $value) {
			switch ($name) {
				case '--disableClassAliases':
					$generator->setIncludeAliases(FALSE);
					break;
			}
		}
	}

	/**
	 * Sends an output to stdOut according to the command line parameter
	 *
	 * @param string $string Content to output to stdOut
	 * @param boolean $force Weather force the output if it's disabled by command line parameter
	 *
	 * @return boolean
	 */
	protected function cliEcho($string, $force = FALSE) {
		if (!isset($this->cliArguments['-ss'])) {
			if ((!isset($this->cliArguments['-s']) && !isset($this->cliArguments['--silent'])) || $force) {
				echo $string;
				return TRUE;
			}
		}

		return FALSE;
	}
}

// This file is included directly, thus instantiate the class and run it
$launcher = new CommandLineLauncher;
$launcher->cli_main();

?>