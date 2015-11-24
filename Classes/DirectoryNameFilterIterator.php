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
 * Class to exclude directories from \RecursiveDirectoryIterator
 *
 * @package TYPO3\CMS\Phpstorm
 */
class DirectoryNameFilterIterator extends \FilterIterator {

	protected $filter = array();

	/**
	 * Constructor function to initialize the object.
	 *
	 * @param \RecursiveIterator $iterator
	 * @param mixed $filter
	 *
	 * @return void
	 */
	public function __construct(\Iterator $iterator, $filter) {
		$this->filter = (array) $filter;

		parent::__construct($iterator);
	}

	/**
	 * Checks the path of the current item against filter
	 *
	 * @return boolean
	 */
	public function accept() {
		$currentObject = $this->current();
		if ($currentObject instanceof \SplFileInfo) {
			foreach ($this->filter as $pathToExclude) {
				if (strpos(trim(str_replace('\\', '/', $currentObject->getPath()), '/') . '/', trim(str_replace('\\', '/', $pathToExclude), '/') . '/') !== FALSE) {
					return FALSE;
				}
			};
			unset($pathToExclude);

			return TRUE;
		}

		return FALSE;
	}

}

?>