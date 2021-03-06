<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Module class for com_support ticket data
 */
class modSupportTickets extends Hubzero_Module
{
	/**
	 * Display module contents
	 * 
	 * @return     void
	 */
	public function display()
	{
		include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_support' . DS . 'tables' . DS . 'query.php');
		include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_support' . DS . 'tables' . DS . 'ticket.php');

		$juser = JFactory::getUser();

		$this->database = JFactory::getDBO();

		$jconfig = JFactory::getConfig();
		$this->offset = $jconfig->getValue('config.offset');

		$type = JRequest::getVar('type', 'submitted');
		$this->type  = ($type == 'automatic') ? 1 : 0;

		$this->group = JRequest::getVar('group', '');

		$this->year  = JRequest::getInt('year', strftime("%Y", time()+($this->offset*60*60)));

		$st = new SupportTicket($this->database);

		$opened = array();
		$my = array();

		$sq = new SupportQuery($this->database);
		$types = array(
			'common' => $sq->getCommon(),
			'mine'   => $sq->getMine()
		);
		// Loop through each grouping
		foreach ($types as $key => $queries)
		{
			if (!is_array($queries) || count($queries) <= 0)
			{
				$one = new stdClass;
				$one->count = 0;
				$one->id = 0;
				$two = new stdClass;
				$two->count = 0;
				$two->id = 0;
				$three = new stdClass;
				$three->count = 0;
				$three->id = 0;
				$types[$key] = $queries = array(
					$one,
					$two,
					$three
				);
			}
			// Loop through each query in a group
			foreach ($queries as $k => $query)
			{
				if ($query->id)
				{
					// Build the query from the condition set
					if (!$query->query)
					{
						$query->query = $sq->getQuery($query->conditions);
					}
					// Get a record count
					$types[$key][$k]->count = $st->getCount($query->query);
				}
			}
		}
		$this->opened = $types['common'];
		$this->my = $types['mine'];

		// Get avgerage lifetime
		$this->lifetime = $st->getAverageLifeOfTicket($this->type, $this->year, $this->group);

		$document = JFactory::getDocument();
		$document->addStyleSheet(JURI::base(true) . '/modules/' . $this->module->module . '/' . $this->module->module . '.css');

		// Get the view
		require(JModuleHelper::getLayoutPath($this->module->module));
	}
}
