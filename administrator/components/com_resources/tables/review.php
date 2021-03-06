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
 * Resources class for reviews
 */
class ResourcesReview extends JTable
{
	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $resource_id = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $user_id     = NULL;

	/**
	 * decimal(2,1)
	 * 
	 * @var integer
	 */
	var $rating      = NULL;

	/**
	 * text
	 * 
	 * @var string
	 */
	var $comment     = NULL;

	/**
	 * datetime(0000-00-00 00:00:00)
	 * 
	 * @var string
	 */
	var $created     = NULL;

	/**
	 * int(3)
	 * 
	 * @var integer
	 */
	var $anonymous   = NULL;

	/**
	 * int(11) primary key
	 * 
	 * @var integer
	 */
	var $id          = NULL;

	/**
	 * Constructor
	 * 
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__resource_ratings', 'id', $db);
	}

	/**
	 * Validate data
	 * 
	 * @return     boolean True if valid, false if not
	 */
	public function check()
	{
		if (trim($this->rating) == '') 
		{
			$this->setError(JText::_('Your review must have a rating.'));
			return false;
		}
		return true;
	}

	/**
	 * Load a review for a specific user/resource combination
	 * 
	 * @param      integer $resourceid Resource ID
	 * @param      integer $userid     User ID
	 * @return     boolean True on success, False on error
	 */
	public function loadUserReview($resourceid, $userid)
	{
		$this->_db->setQuery("SELECT * FROM $this->_tbl WHERE resource_id=" . $this->_db->Quote($resourceid) . " AND user_id=" . $this->_db->Quote($userid) . " LIMIT 1");

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
	 * Load a rating for a specific user/resource combination
	 * 
	 * @param      integer $resourceid Resource ID
	 * @param      integer $userid     User ID
	 * @return     integer
	 */
	public function loadUserRating($resourceid, $userid)
	{
		$this->_db->setQuery("SELECT rating FROM $this->_tbl WHERE resource_id=" . $this->_db->Quote($resourceid) . " AND user_id=" . $this->_db->Quote($userid) . " LIMIT 1");
		return $this->_db->loadResult();
	}

	/**
	 * Get all ratings for a specific resource
	 * 
	 * @param      integer $resource_id Resource ID
	 * @return     array
	 */
	public function getRatings($resource_id=NULL)
	{
		$juser = JFactory::getUser();

		if (!$resource_id) 
		{
			$resource_id = $this->resource_id;
		}
		if (!$resource_id)
		{
			return false;
		}
		//$this->_db->setQuery("SELECT * FROM $this->_tbl WHERE resource_id=".$id." ORDER BY created DESC");
		$this->_db->setQuery("SELECT rr.*, rr.id as id, v.helpful AS vote, "
			."\n (SELECT COUNT(*) FROM #__vote_log AS v WHERE v.helpful='yes' AND v.category='review' AND v.referenceid=rr.id) AS helpful, "
			."\n (SELECT COUNT(*) FROM #__vote_log AS v WHERE v.helpful='no' AND v.category='review' AND v.referenceid=rr.id) AS nothelpful "
			."\n FROM $this->_tbl AS rr "
			."\n LEFT JOIN #__vote_log AS v ON v.referenceid=rr.id AND v.category='review' AND v.voter=" . $this->_db->Quote($juser->get('id')) . " "
			."\n WHERE rr.resource_id=" . $this->_db->Quote($resource_id) . "  ORDER BY rr.created DESC");
		return $this->_db->loadObjectList();
	}

	/**
	 * Load rating for a specific resource
	 * 
	 * @param      integer $id     Resource ID
	 * @param      integer $userid User ID
	 * @return     array
	 */
	public function getRating($id=NULL, $userid)
	{
		if (!$userid) 
		{
			$juser = JFactory::getUser();
			$userid = $juser->get('id');
		}

		if (!$id) 
		{
			$id = $this->resource_id;
		}
		if (!$id)
		{
			return false;
		}
		//$this->_db->setQuery("SELECT * FROM $this->_tbl WHERE resource_id=".$id." ORDER BY created DESC");
		$this->_db->setQuery("SELECT rr.*, rr.id as id, v.helpful AS vote, "
			."\n (SELECT COUNT(*) FROM #__vote_log AS v WHERE v.helpful='yes' AND v.category='review' AND v.referenceid=rr.id) AS helpful, "
			."\n (SELECT COUNT(*) FROM #__vote_log AS v WHERE v.helpful='no' AND v.category='review' AND v.referenceid=rr.id) AS nothelpful "
			."\n FROM $this->_tbl AS rr "
			."\n LEFT JOIN #__vote_log AS v ON v.referenceid=rr.id AND v.category='review' AND v.voter=" . $this->_db->Quote($userid) . " "
			."\n WHERE rr.id=" . $this->_db->Quote($id) . " ");
		return $this->_db->loadObjectList();
	}

	/**
	 * Get the vote for a specific item and user
	 * 
	 * @param      integer $id       Resource ID
	 * @param      string  $category Category
	 * @param      integer $uid      User ID
	 * @return     integer
	 */
	public function getVote($id, $category = 'review', $uid)
	{
		if (!$id) 
		{
			$id = $this->id;
		}

		if ($id === NULL or $uid === NULL) 
		{
			return false;
		}

		$query  = "SELECT v.helpful ";
		$query .= "FROM #__vote_log as v  ";
		$query .= "WHERE v.referenceid = " . $this->_db->Quote($id) . " AND v.category=" . $this->_db->Quote($category) . " AND v.voter=" . $this->_db->Quote($uid) . " LIMIT 1";
		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}
}

