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
 * Table class for forum posts
 */
class CollectionsTableFollowing extends JTable
{
	/**
	 * int(11) Primary key
	 * 
	 * @var integer 
	 */
	var $id         = NULL;

	/**
	 * text
	 * 
	 * @var string
	 */
	var $follower_type = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer 
	 */
	var $follower_id = NULL;

	/**
	 * datetime(0000-00-00 00:00:00)
	 * 
	 * @var string  
	 */
	var $created    = NULL;

	/**
	 * text
	 * 
	 * @var string
	 */
	var $following_type = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer 
	 */
	var $following_id = NULL;

	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__collections_following', 'id', $db);
	}

	/**
	 * Load a record and bind to $this
	 * 
	 * @param      string $oid Record alias
	 * @return     boolean True on success
	 */
	public function load($oid=NULL, $following_type=null, $follower_id=null, $follower_type=null)
	{
		if ($oid === NULL) 
		{
			return false;
		}

		if (!$following_type && !$follower_id && !$follower_type)
		{
			return parent::load($oid);
		}

		$oid = trim($oid);

		$query = "SELECT * FROM $this->_tbl WHERE following_id=" . $this->_db->Quote($oid) . " AND following_type=" . $this->_db->Quote($following_type);
		if ($follower_id !== null)
		{
			$query .= " AND follower_id=" . $this->_db->Quote(intval($follower_id));
		}
		if ($follower_type !== null)
		{
			$query .= " AND follower_type=" . $this->_db->Quote(strtolower(trim($follower_type)));
		}

		$this->_db->setQuery($query);
		if ($result = $this->_db->loadAssoc()) 
		{
			return $this->bind($result);
		} 
		else 
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
	}

	/**
	 * Validate data
	 * 
	 * @return     boolean True if data is valid
	 */
	public function check()
	{
		$juser = JFactory::getUser();

		$this->follower_id = intval($this->follower_id);
		if (!$this->follower_id) 
		{
			//$this->setError(JText::_('Please provide a user ID'));
			//return false;
			$this->follower_type = 'member';
			$this->follower_id = $juser->get('id');
		}

		$this->following_id = intval($this->following_id);
		if (!$this->following_id) 
		{
			$this->setError(JText::_('Please provide a following ID'));
			return false;
		}

		$this->following_type = trim($this->following_type);
		if (!$this->following_type) 
		{
			$this->setError(JText::_('Please provide a following type'));
			return false;
		}

		if (!$this->id) 
		{
			$this->created = JFactory::getDate()->toSql();
		}

		return true;
	}

	/**
	 * Build a query based off of filters passed
	 * 
	 * @param      array $filters Filters to construct query from
	 * @return     string SQL
	 */
	private function _buildQuery($filters=array())
	{
		$query  = " FROM $this->_tbl AS f";

		$where = array();

		if (isset($filters['following_id']) && $filters['following_id']) 
		{
			$where[] = "f.following_id=" . $this->_db->Quote($filters['following_id']);
		}
		if (isset($filters['following_type']) && $filters['following_type']) 
		{
			$where[] = "f.following_type=" . $this->_db->Quote($filters['following_type']);
		}
		if (isset($filters['follower_id']) && $filters['follower_id']) 
		{
			$where[] = "f.follower_id=" . $this->_db->Quote($filters['follower_id']);
		}
		if (isset($filters['follower_type']) && $filters['follower_type']) 
		{
			$where[] = "f.follower_type=" . $this->_db->Quote($filters['follower_type']);
		}

		if (count($where) > 0)
		{
			$query .= " WHERE ";
			$query .= implode(" AND ", $where);
		}

		return $query;
	}

	/**
	 * Get a record count
	 * 
	 * @param      array $filters Filters to construct query from
	 * @return     integer
	 */
	public function count($filters=array())
	{
		$filters['limit'] = 0;

		$query = "SELECT COUNT(*) " . $this->_buildQuery($filters);

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Get records
	 * 
	 * @param      array $filters Filters to construct query from
	 * @return     array
	 */
	public function find($filters=array())
	{
		$query = "SELECT f.*,
			(SELECT COUNT(*) FROM #__collections_following AS fg WHERE fg.following_id=f.following_id AND fg.following_type=f.following_type) AS followers, 
			(SELECT COUNT(*) FROM #__collections_following AS fr WHERE fr.follower_id=f.following_id AND fr.follower_type=f.following_type) AS following";
		$query .= $this->_buildQuery($filters);

		if (!isset($filters['sort']) || !$filters['sort']) 
		{
			$filters['sort'] = 'f.created';
		}
		if (!isset($filters['sort_Dir']) || !$filters['sort_Dir']) 
		{
			$filters['sort_Dir'] = 'DESC';
		}
		$query .= " ORDER BY " . $filters['sort'] . " " . $filters['sort_Dir'];

		if (isset($filters['limit']) && $filters['limit'] != 0) 
		{
			$query .= ' LIMIT ' . intval($filters['start']) . ',' . intval($filters['limit']);
		}

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
}
