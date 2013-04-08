<?php
namespace TYPO3\CMS\Phpstorm;

use TYPO3\CMS\Core\Utility\GeneralUtility;

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
class CommandLineLauncher extends \TYPO3\CMS\Core\Controller\CommandLineController {

	public function cli_main() {
		if (TYPO3_REQUESTTYPE & TYPO3_REQUESTTYPE_CLI && basename(PATH_thisScript) == 'cli_dispatch.phpsh') {
			$generator = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Phpstorm\MetaDataFileGenerator');
			$this->handleCliArguments($generator);
			$generator->run();

			$peakMemory = memory_get_peak_usage(TRUE);
			$this->cli_echo('Done with ' . \TYPO3\CMS\Core\Utility\GeneralUtility::formatSize($peakMemory) . ' Memory usage.' . LF);
		} else {
			die('This script must be included by the "CLI module dispatcher"' . LF);
		}
	}

	/**
	 * @param \TYPO3\CMS\Phpstorm\MetaDataFileGenerator $generator
	 */
	protected function handleCliArguments($generator) {
		foreach ($this->cli_args as $name => $value) {
			switch ($name) {
				case '--disableClassAliases':
					$generator->setIncludeAliases(FALSE);
					break;
				case '--extensions':
					$extensions = GeneralUtility::trimExplode($value);
					foreach ($extensions as $extensions) {
						$generator->appendExtension($extension);
					}
					break;
			}
		}
	}
}

// This file is included directly, thus instantiate the class and run it
$adminObj = new \TYPO3\CMS\Phpstorm\CommandLineLauncher;
$adminObj->cli_main();
?>