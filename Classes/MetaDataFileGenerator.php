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

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;


/**
 * PhpStorm uses a meta data file to help with code completion when factory
 * methods are used. This class generates such a file fo TYPO3.
 *
 * @package TYPO3\CMS\Phpstorm
 */
class MetaDataFileGenerator {

	/**
	 * TYPO3's factory methods.
	 *
	 * @var array
	 */
	protected $factoryMethods = array(
		't3lib_div::makeInstance',
		'TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance',
		'TYPO3\CMS\Extbase\Object\ObjectManager::create',
		'TYPO3\CMS\Extbase\Object\ObjectManager::get'
	);

	/**
	 * @var string The file into which the data is written
	 */
	protected $outFile = '';

	/**
	 * @var boolean Whether to include the class aliases (old class names)
	 */
	protected $includeAliases = TRUE;


	/**
	 * Constructor, sets the default value for the output file
	 *
	 */
	public function __construct() {
		$this->outFile = PATH_site . '.phpstorm.meta.php';
	}

	/**
	 * Allows to set whether to include class aliases or not.
	 *
	 * @param boolean $include Include class aliases (old class names)?
	 */
	public function setIncludeAliases($include) {
		$this->includeAliases = $include;
	}

	/**
	 * Gathers class information and eventually creates the PhpStorm meta data
	 * file.
	 *
	 */
	public function run() {
		$classes = $this->getClassesFromFiles(PATH_site);
		if ($this->includeAliases) {
			$classAliases = $this->getCoreAndExtensionClassAliases();
			$classes = array_merge($classes, $classAliases);
			unset($classAliases);
		}
		$classes = array_unique($classes);

		$this->generateMetaDataFile($classes);
	}

	/**
	 * Generates the PhpStorm meta data file and writes it into the TYPO3
	 * installation's root directory (PATH_site) using
	 * the filename '.phpstorm.meta.php'.
	 *
	 * @param array $classes Array of class names found in the installation
	 */
	protected function generateMetaDataFile(array $classes) {
		$metaDataFile = fopen($this->outFile, 'w');
		$metaDataMap = <<< PHP_STORM_META
<?php
namespace PHPSTORM_META {

	/** @noinspection PhpUnusedLocalVariableInspection */
	/** @noinspection PhpIllegalArrayKeyTypeInspection */
	\$STATIC_METHOD_TYPES = [

PHP_STORM_META;
		fwrite($metaDataFile, $metaDataMap);

		foreach ($this->factoryMethods as $factoryMethod) {
			fwrite($metaDataFile, "		\\$factoryMethod('') => [\n");
			foreach ($classes as $class) {
				fwrite($metaDataFile, "			'$class' instanceof \\$class,\n");
				if (strstr($class, '\\')) {
					$className = str_replace('\\', '\\\\', $class);
					fwrite($metaDataFile, "			'$className' instanceof \\$class,\n");
				}
			}
			fwrite($metaDataFile, "		],\n");
		}

		$metaDataMap = <<< PHP_STORM_META
	];
}
?>
PHP_STORM_META;

		fwrite($metaDataFile, $metaDataMap);
		fclose($metaDataFile);
	}

	/**
	 * Gets class aliases for backwards compatibility. Note though, that these
	 * will be gone with TYPO3 CMS version 6.2
	 *
	 * @return array An array of classes and their new class names
	 */
	protected function getCoreAndExtensionClassAliases() {
		$aliasToClassNameMapping = array();
		foreach (ExtensionManagementUtility::getLoadedExtensionListArray() as $extensionKey) {
			try {
				$extensionClassAliasMap = ExtensionManagementUtility::extPath($extensionKey, 'Migrations/Code/ClassAliasMap.php');
				if (file_exists($extensionClassAliasMap)) {
					$aliasToClassNameMapping = array_merge($aliasToClassNameMapping, require $extensionClassAliasMap);
				}
			} catch (\BadFunctionCallException $e) {
			}
		}

		$classesAndAliases = array_merge(
			array_keys($aliasToClassNameMapping),
			array_values($aliasToClassNameMapping)
		);

		return $classesAndAliases;
	}

	/**
	 * Walks over every php file in the installation and finds the classes in
	 * each file. Also takes care of properly prefixing the class with the
	 * file's namespace.
	 *
	 * @return array An array of classes found in PHP files in the installation
	 */
	protected function getClassesFromFiles() {
		$scanRootPath = PATH_site;
		$classes      = array();

		$iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($scanRootPath, \FilesystemIterator::FOLLOW_SYMLINKS));
		$files = new \RegexIterator($iterator, '/^.+\.php$/', \RecursiveRegexIterator::GET_MATCH);

		foreach ($files as $file) {
			$file = $file[0];
			$fileContent = file_get_contents($file);
			$tokens = token_get_all($fileContent);
			$tokenCount = count($tokens);

			$namespace = $this->getFileNamespace($file);

			for ($i = 2; $i < $tokenCount; $i++) {
				if (is_array($tokens[$i])
					&& $tokens[$i - 2][0] == T_CLASS
					&& $tokens[$i - 1][0] == T_WHITESPACE
					&& $tokens[$i][0] == T_STRING
				) {
					$className = $tokens[$i][1];
					$classes[] = $namespace . $className;
					break;
				}
			}
		}

		return $classes;
	}

	/**
	 * Reads a file and assumes to find the namespace on the second line (the
	 * first line after the PHP open tag).
	 *
	 * @param string $file file path
	 * @return string namespace that was found, otherwise empty string
	 */
	protected function getFileNamespace($file) {
		$fileHandle = fopen($file, "r");
		$lines = array();
		while (!feof($fileHandle)) {
			$buffer = fgets($fileHandle, 4096);
			$lines[] = $buffer;

			if (count($lines) == 2) {
				break;
			}
		}
		fclose ($fileHandle);

		$matches = NULL;
		$matched = preg_match('/^namespace[ \t]+(.*);$/', $lines[1], $matches);

		return ($matched ? $matches[1] . '\\' : '');
	}

}

?>