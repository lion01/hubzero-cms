<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for ...
 **/
class Migration20131106153123PlgYsearchCollections extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		self::addPluginEntry('ysearch', 'collections');
	}

	/**
	 * Down
	 **/
	protected static function down($db)
	{
		self::deletePluginEntry('ysearch', 'collections');
	}
}