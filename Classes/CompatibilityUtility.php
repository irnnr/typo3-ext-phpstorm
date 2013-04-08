<?php
namespace TYPO3\CMS\Phpstorm;
/***************************************************************
*  Copyright notice
*
*  (c) 2013 Nicole Cordes <typo3@cordes.co>
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

/**
 * Class to support t3lib and namespaced classes
 *
 * @package TYPO3\CMS\Phpstorm
 */
final class CompatibilityUtility {

	/**
	 * @param string $className
	 *
	 * @return object
	 */
	static public function makeInstance($className) {
		if (class_exists('TYPO3\\CMS\\Core\\Utility\\GeneralUtility')) {
			return \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance($className);
		} else {
			// Require namespaced file as the autoloader can not handle them in lower versions
			$classToExplode = str_replace('\\\\', '\\', $className);
			$explodedClassName = explode('\\', $classToExplode);
			if (count($explodedClassName) > 1) {
				// Remove vendor name
				array_shift($explodedClassName);
				if ($explodedClassName[0] === 'CMS') {
					// Remove CMS part
					array_shift($explodedClassName);
				}
				$extensionName = strtolower(array_shift($explodedClassName));
				$requiredFile = \t3lib_extMgm::extPath($extensionName) . 'Classes/' . implode('/', $explodedClassName) . '.php';
				if (@file_exists($requiredFile)) {
					require_once($requiredFile);
				}
			}
			return \t3lib_div::makeInstance($className);
		}
	}

	/**
	 * @param integer $sizeInBytes
	 * @param string $labels
	 *
	 * @return string
	 */
	static public function formatSize($sizeInBytes, $labels = '') {
		if (class_exists('TYPO3\\CMS\\Core\\Utility\\GeneralUtility')) {
			return \TYPO3\CMS\Core\Utility\GeneralUtility::formatSize($sizeInBytes, $labels);
		} else {
			return \t3lib_div::formatSize($sizeInBytes, $labels);
		}
	}

	/**
	 * @return array
	 */
	static public function getLoadedExtensionListArray() {
		if (class_exists('TYPO3\\CMS\\Core\\Utility\\ExtensionManagementUtility')) {
			return \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getLoadedExtensionListArray();
		} else {
			$extensionList = $GLOBALS['TYPO3_CONF_VARS']['EXT']['extListArray'];
			return \t3lib_div::trimExplode(',', $extensionList, TRUE);
		}
	}

	/**
	 * @param string $extensionName
	 * @param string $fileName
	 * @return string
	 */
	static public function extPath($extensionName, $fileName = '') {
		if (class_exists('TYPO3\\CMS\\Core\\Utility\\ExtensionManagementUtility')) {
			return \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($extensionName, $fileName);
		} else {
			return \t3lib_extMgm::extPath($extensionName, $fileName);
		}
	}

}

?>