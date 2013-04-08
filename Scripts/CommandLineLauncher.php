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

use TYPO3\CMS\Core\Controller\CommandLineController;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * CommandLineLauncher
 *
 * @package TYPO3\CMS\Phpstorm
 */
class CommandLineLauncher extends CommandLineController {

	/**
	 * "main"
	 *
	 */
	public function cli_main() {
		if (TYPO3_REQUESTTYPE & TYPO3_REQUESTTYPE_CLI && basename(PATH_thisScript) == 'cli_dispatch.phpsh') {
			$generator = GeneralUtility::makeInstance('TYPO3\CMS\Phpstorm\MetaDataFileGenerator');
			$this->handleCliArguments($generator);
			$generator->run();

			$peakMemory = memory_get_peak_usage(TRUE);
			$this->cli_echo('Done, used ' . GeneralUtility::formatSize($peakMemory) . ' Memory.' . LF);
		} else {
			die('This script must be included by the "CLI module dispatcher"' . LF);
		}
	}

	/**
	 * Sets generator properties according to CLI arguments
	 *
	 * @param \TYPO3\CMS\Phpstorm\MetaDataFileGenerator $generator
	 */
	protected function handleCliArguments(MetaDataFileGenerator $generator) {
		foreach ($this->cli_args as $name => $value) {
			switch ($name) {
				case '--disableClassAliases':
					$generator->setIncludeAliases(FALSE);
					break;
			}
		}
	}
}

// This file is included directly, thus instantiate the class and run it
$launcher = new CommandLineLauncher;
$launcher->cli_main();

?>