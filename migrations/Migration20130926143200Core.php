<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for removing Joomla templates
 **/
class Migration20130926143200Core extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		$joomlas = array('atomic', 'bluestork', 'beez_20', 'hathor', 'beez5');
		$templates = array();

		if ($db->tableExists('#__extensions'))
		{
			$query = "SELECT * FROM `#__template_styles` WHERE `home` = 0;";
			$db->setQuery($query);
			$result = $db->loadObjectList();

			$templates = array();
			foreach ($result as $r)
			{
				if (in_array($r->template, $joomlas))
				{
					$templates[] = $r->template;
				}
			}

			$query = "DELETE FROM #__extensions WHERE `type`='template' AND `element` IN ('" . implode("','", $templates) . "')";
			$db->setQuery($query);
			$db->query();
		}

		if ($db->tableExists('#__template_styles'))
		{
			if (empty($templates))
			{
				$templates = $joomlas;
			}
			$query = "DELETE FROM #__template_styles WHERE `template` IN ('" . implode("','", $templates) . "') AND `home`=0";
			$db->setQuery($query);
			$db->query();
		}
	}

	/**
	 * Down
	 **/
	protected static function down($db)
	{
		if ($db->tableExists('#__template_styles'))
		{
			$query = "INSERT INTO `#__template_styles` VALUES 
				(2, 'bluestork', '1', '0', 'Bluestork - Default', '{\"useRoundedCorners\":\"1\",\"showSiteName\":\"0\"}'),
				(3, 'atomic', '0', '0', 'Atomic - Default', '{}'),
				(4, 'beez_20', 0, 0, 'Beez2 - Default', '{\"wrapperSmall\":\"53\",\"wrapperLarge\":\"72\",\"logo\":\"images\\/joomla_black.gif\",\"sitetitle\":\"Joomla!\",\"sitedescription\":\"Open Source Content Management\",\"navposition\":\"left\",\"templatecolor\":\"personal\",\"html5\":\"0\"}'),
				(5, 'hathor', '1', '0', 'Hathor - Default', '{\"showSiteName\":\"0\",\"colourChoice\":\"\",\"boldText\":\"0\"}'),
				(6, 'beez5', 0, 0, 'Beez5 - Default', '{\"wrapperSmall\":\"53\",\"wrapperLarge\":\"72\",\"logo\":\"images\\/sampledata\\/fruitshop\\/fruits.gif\",\"sitetitle\":\"Joomla!\",\"sitedescription\":\"Open Source Content Management\",\"navposition\":\"left\",\"html5\":\"0\"}');";

			$db->setQuery($query);
			$db->query();

			// Insert all templates from extensions
			$query = "SELECT * FROM `#__extensions` WHERE `type` = 'template';";
			$db->setQuery($query);
			$result = $db->loadObjectList();

			foreach ($result as $r)
			{
				$query = "SELECT * FROM `#__template_styles` WHERE `template` = '{$r->element}';";
				$db->setQuery($query);
				if ($db->loadResult())
				{
					continue;
				}

				$query = "INSERT INTO `#__template_styles` (`template`, `client_id`, `home`, `title`, `params`) VALUES ('{$r->element}', '{$r->client_id}', '0', '".ucfirst($r->element)."', '{}');";
				$db->setQuery($query);
				$db->query();
			}
		}
	}
}