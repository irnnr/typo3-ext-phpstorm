# EXT:phpstorm

## Description

PhpStorm 6.0.1 EAP-129.196 introduced support for a meta data file -
.phpstorm.meta.php - to help its code completion when factory methods are used.

This is a TYPO3 extension to generate that file.

Supported factory methods are:

* t3lib_div::makeInstance
* TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance
* TYPO3\CMS\Extbase\Object\ObjectManager::create
* TYPO3\CMS\Extbase\Object\ObjectManager::get

## Usage

The extension provides a cli command to execute the generation of the meta data
sfile.

In your TYPO3 installation's root directory call the following command:

	typo3/cli_dispatch.phpsh phpstorm_metadata

Note, this will take some time since all the PHP files in your installation
will be analyzed.