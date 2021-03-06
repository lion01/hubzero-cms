<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for fixing some dated references to topics, rather than wiki
 **/
class Migration20131017133750ComWiki extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		$query  = "SELECT * FROM `#__wiki_page` AS wp,";
		$query .= " `#__wiki_version` AS wv";
		$query .= " WHERE wp.id = wv.pageid";
		$query .= " AND wp.pagename = 'MainPage' AND (wp.group_cn='' OR wp.group_cn IS NULL)";
		$query .= " ORDER BY wv.version DESC";
		$query .= " LIMIT 1;";

		$db->setQuery($query);
		$result = $db->loadObject();

		if ($result)
		{
			require_once JPATH_ROOT . DS . 'components' . DS . 'com_wiki' . DS . 'tables' . DS . 'revision.php';
			require_once JPATH_ROOT . DS . 'components' . DS . 'com_wiki' . DS . 'tables' . DS . 'page.php';

			$version = new WikiPageRevision($db);
			$version->loadByVersion($result->pageid, $result->version);

			$page = new WikiPage($db);
			$page->load($result->pageid);

			$hostname = php_uname('n');

			// No need to run this on nanoHUB
			if (stripos($hostname, 'nanohub') === false)
			{
				$pagetext = preg_replace('/(Topic)/', 'Wiki', $result->pagetext);
				$pagehtml = preg_replace('/(Topic)/', 'Wiki', $result->pagehtml);
				$pagetext = preg_replace('/(topic)/', 'wiki', $pagetext);
				$pagehtml = preg_replace('/(topic)/', 'wiki', $pagehtml);
				$version->save(array('pagetext'=>$pagetext, 'pagehtml'=>$pagehtml));

				$page->set('title', preg_replace('/(Topic)/', 'Wiki', $page->title));
				$page->store();
			}
		}
	}
}