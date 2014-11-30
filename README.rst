
============
EXT:phpstorm
============

Description
===========

PhpStorm 6.0.1 EAP-129.196 introduced support for a meta data file -
.phpstorm.meta.php - to help its code completion when factory methods are used.

This is a TYPO3 extension to generate that file.

Supported factory methods are:

* t3lib_div::makeInstance
* TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance
* TYPO3\CMS\Extbase\Object\ObjectManager::create
* TYPO3\CMS\Extbase\Object\ObjectManager::get

Usage
=====

The extension provides a cli command to execute the generation of the meta data
sfile.

First, add a TYPO3 backend user named "_cli_phpstorm", password doesn't matter
and it also doesn't need any permissions, just needs to exist.

Now, in your TYPO3 installation's root directory execute the following command::

	typo3/cli_dispatch.phpsh phpstorm_metadata

Note, this will take some time since all the PHP files in your installation
will be analyzed.

Commandline Arguments
=====================

--disableClassAliases
---------------------

TYPO3 CMS 6.0 introduced namespaces and cleaned up a lot of class names. To
provide backwardscompatibility with extensions that do not use the new names
yet a class alias map has been created. By default that alias map is included.
Use --disableClassAliases to prevent including those aliasses.

-s, --silent
------------

Silent operation, will only output errors and important messages.

-ss
---

Super silent, will not even output errors or important messages.
