<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for ...
 **/
class Migration20131016184016PlgGroupsAnnouncements extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		// add email column
		if (!$db->tableHasField('#__announcements', 'email'))
		{
			$query = "ALTER TABLE `#__announcements` ADD COLUMN `email` TINYINT(4) DEFAULT 0;";
		}
		
		// add sent column
		if (!$db->tableHasField('#__announcements', 'sent'))
		{
			$query .= "ALTER TABLE `#__announcements` ADD COLUMN `sent` TINYINT(4) DEFAULT 0;";
		}
		
		// add group announcements cron
		$query .= "INSERT INTO `#__cron_jobs` (`title`, `state`, `plugin`, `event`, `recurrence`)
				   VALUES ('Group Announcements', 1, 'groups', 'sendGroupAnnouncements', '*/5 * * * *');";
		
		if (!empty($query))
		{
			$db->setQuery($query);
			$db->query();
		}
	}

	/**
	 * Down
	 **/
	protected static function down($db)
	{
		// add email column
		if ($db->tableHasField('#__announcements', 'email'))
		{
			$query = "ALTER TABLE `#__announcements` DROP COLUMN `email`;";
		}
		
		// add sent column
		if ($db->tableHasField('#__announcements', 'sent'))
		{
			$query .= "ALTER TABLE `#__announcements` DROP COLUMN `sent`;";
		}
		
		// remove announcements cron
		$query .= "DELETE FROM `#__cron_jobs` WHERE event='sendGroupAnnouncements'";

		if (!empty($query))
		{
			$db->setQuery($query);
			$db->query();
		}
	}
}