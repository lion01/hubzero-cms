<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for modules table changes
 **/
class Migration20130718000011ComModules extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		$query = "ALTER TABLE `#__modules` ENGINE = InnoDB;\n";
		$query .= "ALTER TABLE `#__modules_menu` ENGINE = InnoDB;";
		$db->setQuery($query);
		$db->query();

		if ($db->tableHasField('#__modules', 'numnews'))
		{
			$query = "ALTER TABLE `#__modules` DROP `numnews`;";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__modules', 'control'))
		{
			$query = "ALTER TABLE `#__modules` DROP `control`;";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__modules', 'iscore'))
		{
			$query = "ALTER TABLE `#__modules` DROP `iscore`;";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasField('#__modules', 'note') && $db->tableHasField('#__modules', 'title'))
		{
			$query = "ALTER TABLE `#__modules` ADD COLUMN `note` VARCHAR(255) NOT NULL DEFAULT '' AFTER `title`;";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasField('#__modules', 'language') && $db->tableHasField('#__modules', 'client_id'))
		{
			$query = "ALTER TABLE `#__modules` ADD COLUMN `language` CHAR(7) NOT NULL AFTER `client_id`;";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasKey('#__modules', 'idx_language') && $db->tableHasField('#__modules', 'language'))
		{
			$query = "ALTER TABLE `#__modules` ADD INDEX `idx_language` (`language`);";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__modules', 'position'))
		{
			$query = "ALTER TABLE `#__modules` CHANGE COLUMN `position` `position` VARCHAR(50) NOT NULL DEFAULT '';";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__modules', 'title'))
		{
			$query = "ALTER TABLE `#__modules` CHANGE `title` `title` varchar(100) NOT NULL DEFAULT '';";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__modules', 'params'))
		{
			$query = "ALTER TABLE `#__modules` CHANGE COLUMN `params` `params` TEXT NOT NULL;";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__modules', 'checked_out'))
		{
			$query = "ALTER TABLE `#__modules` CHANGE COLUMN `checked_out` `checked_out` INT(10) UNSIGNED NOT NULL DEFAULT '0';";
			$db->setQuery($query);
			$db->query();
		}
		if ($db->tableHasField('#__modules', 'access'))
		{
			$query = "ALTER TABLE `#__modules` CHANGE COLUMN `access` `access` INT(10) UNSIGNED NOT NULL DEFAULT '0';";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasField('#__modules', 'publish_up') && $db->tableHasField('#__modules', 'checked_out_time'))
		{
			$query = "ALTER TABLE `#__modules` ADD COLUMN `publish_up` datetime NOT NULL default '0000-00-00 00:00:00' AFTER `checked_out_time`;";
			$db->setQuery($query);
			$db->query();
		}
		if (!$db->tableHasField('#__modules', 'publish_down') && $db->tableHasField('#__modules', 'publish_up'))
		{
			$query = "ALTER TABLE `#__modules` ADD COLUMN `publish_down` datetime NOT NULL default '0000-00-00 00:00:00' AFTER `publish_up`;";
			$db->setQuery($query);
			$db->query();
		}

		$query = "UPDATE `#__modules` SET `module` = 'mod_menu' WHERE `module` = 'mod_mainmenu';";
		$db->setQuery($query);
		$db->query();

		// Add modules_menu entry for hubmenu, submenu, title, and toolbar
		$query = "SELECT `id` FROM `#__modules` WHERE `position` = 'menu';";
		$db->setQuery($query);
		$ids[] = $db->loadResult();
		$query = "SELECT `id` FROM `#__modules` WHERE `position` = 'submenu';";
		$db->setQuery($query);
		$ids[] = $db->loadResult();
		$query = "SELECT `id` FROM `#__modules` WHERE `position` = 'title';";
		$db->setQuery($query);
		$ids[] = $db->loadResult();
		$query = "SELECT `id` FROM `#__modules` WHERE `position` = 'toolbar';";
		$db->setQuery($query);
		$ids[] = $db->loadResult();

		foreach ($ids as $id)
		{
			$query = "INSERT INTO `#__modules_menu` VALUES ({$id}, 0);";
			$db->setQuery($query);
			$db->query();
		}
	}
}