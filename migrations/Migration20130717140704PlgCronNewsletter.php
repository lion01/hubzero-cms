<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for ...
 **/
class Migration20130717140704PlgCronNewsletter extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		/**
		 * Forgot to re-add migration script after adding it to git 
		 * staging and making further changes. this will make sure 
		 * newsletter cron jobs exits.
		 * 
		 * @author    Christopher Smoak
		 */
		$query = "";
		
		//add newsletter cron jobs
		$query .= "INSERT INTO `#__cron_jobs` (`title`, `state`, `plugin`, `event`, `last_run`, `next_run`, `recurrence`, `created`, `created_by`, `modified`, `modified_by`, `active`, `ordering`, `params`)
					SELECT 'Process Newsletter Mailings', 0, 'newsletter', 'processMailings', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '*/5 * * * *', '2013-06-25 08:23:04', 1001, '2013-07-16 17:15:01', 0, 0, 0, 'newsletter_queue_limit=2\nsupport_ticketreminder_severity=all\nsupport_ticketreminder_group=\n\n'
					FROM DUAL WHERE NOT EXISTS (SELECT `title` FROM `#__cron_jobs` WHERE `title` = 'Process Newsletter Mailings');";
					
		$query .= "INSERT INTO `#__cron_jobs` (`title`, `state`, `plugin`, `event`, `last_run`, `next_run`, `recurrence`, `created`, `created_by`, `modified`, `modified_by`, `active`, `ordering`, `params`)
					SELECT 'Process Newsletter Opens & Click IP Addresses', 0, 'newsletter', 'processIps', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '*/5 * * * *', '2013-06-25 08:23:04', 1001, '2013-07-16 17:15:01', 0, 0, 0, ''
					FROM DUAL WHERE NOT EXISTS (SELECT `title` FROM `#__cron_jobs` WHERE `title` = 'Process Newsletter Opens & Click IP Addresses');";

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
		$query = "";
		
		//remove newsletter cron jobs 
		$query .= "DELETE FROM `#__cron_jobs` WHERE `title`='Process Newsletter Mailings';";
		$query .= "DELETE FROM `#__cron_jobs` WHERE `title`='Process Newsletter Opens & Click IP Addresses';";

		if (!empty($query))
		{
			$db->setQuery($query);
			$db->query();
		}
	}
}