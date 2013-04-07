# EXT:phpstorm

## Description

PhpStorm 6.0.1 EAP-129.196 introduced support for a meta data file to help its
code completion when factory methods are used.

This is a TYPO3 extension to generate that file, .phpstorm.meta.php.

## Usage

The extension provides cli command to execute the generation of the meta data
sfile.

In your TYPO3 installation's root directory call the following command:

	typo3/cli_dispatch.phpsh phpstorm_metadata

Note, this will take a some time since all the PHP files in your installations
will be analyzed.