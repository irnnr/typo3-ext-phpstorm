<?php
namespace TYPO3\CMS\Phpstorm;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Stefan Aebischer <typo3@pixtron.ch>
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

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * This class filters out all the files which are not modified since $changedSince.
 *
 * @package TYPO3\CMS\Phpstorm
 */
class RecursiveFileModTimeFilterIterator extends \RecursiveFilterIterator {

	/**
	 * @var integer
	 */
	protected $changedSince;

	/**
	 * @param \RecursiveIterator $iterator
	 * @param integer $changedSince Timestamp of last changes
	 */
	public function __construct(\RecursiveIterator $iterator, $changedSince) {
		$this->changedSince = $changedSince;
		parent::__construct($iterator);
	}

	/**
	 * @return bool
	 */
	public function accept() {
		return ($this->isDir() || ($this->getMTime() >= $this->changedSince));
	}

	/**
	 * @return RecursiveFileModTimeFilterIterator
	 */
	public function getChildren() {
		return new static($this->getInnerIterator()->getChildren(), $this->changedSince);
	}
}

?>