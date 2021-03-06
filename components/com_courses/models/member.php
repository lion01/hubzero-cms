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

require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_courses' . DS . 'tables' . DS . 'member.php');
require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_courses' . DS . 'tables' . DS . 'role.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'abstract.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'memberBadge.php');

/**
 * Courses model class for a course
 */
class CoursesModelMember extends CoursesModelAbstract
{
	/**
	 * JTable class name
	 * 
	 * @var string
	 */
	protected $_tbl_name = 'CoursesTableMember';

	/**
	 * Object scope
	 * 
	 * @var string
	 */
	protected $_scope = 'manager';

	/**
	 * CoursesModelMemberBadge
	 * 
	 * @var object
	 */
	private $_badge = NULL;

	/**
	 * Constructor
	 * 
	 * @param      integer $id  Resource ID or alias
	 * @param      object  &$db JDatabase
	 * @return     void
	 */
	public function __construct($uid, $cid=0, $oid=0, $sid=0)
	{
		$this->_db = JFactory::getDBO();

		$this->_tbl = new CoursesTableMember($this->_db);

		if (is_numeric($uid) || is_string($uid))
		{
			$this->_tbl->load($uid, $cid, $oid, $sid);
		}
		else if (is_object($uid))
		{
			$this->_tbl->bind($uid);

			$properties = $this->_tbl->getProperties();
			foreach (get_object_vars($uid) as $key => $property)
			{
				if (!array_key_exists($key, $properties)) // && in_array($property, self::$_section_keys))
				{
					$this->_tbl->set('__' . $key, $property);
				}
			}
		}
		else if (is_array($uid))
		{
			$this->_tbl->bind($uid);

			$properties = $this->_tbl->getProperties();
			foreach (array_keys($uid) as $key)
			{
				if (!array_key_exists($key, $properties)) // && in_array($property, self::$_section_keys))
				{
					$this->_tbl->set('__' . $key, $uid[$key]);
				}
			}
		}

		$paramsClass = 'JParameter';
		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$paramsClass = 'JRegistry';
		}

		//$permissions = clone(JComponentHelper::getParams('com_courses'));
		//$permissions->merge(new $paramsClass($this->get('role_permissions')));
		if (!$this->get('role_alias'))
		{
			$result = new CoursesTableRole($this->_db);
			if ($result->load($this->get('role_id')))
			{
				$properties = $result->getProperties();
				foreach ($result->getProperties() as $key => $property)
				{
					$this->_tbl->set('__role_' . $key, $property);
				}
			}
		}

		//$permissions = new $paramsClass($this->get('role_permissions'));
		//$permissions->merge(new $paramsClass($this->get('permissions')));

		/*if ($this->exists())
		{
			$permissions->set('access-view-offering', true);
		}*/

		//$this->set('permissions', $permissions);
	}

	/**
	 * Returns a reference to a wiki page object
	 *
	 * This method must be invoked as:
	 *     $inst = CoursesInstance::getInstance($alias);
	 *
	 * @param      string $pagename The page to load
	 * @param      string $scope    The page scope
	 * @return     object WikiPage
	 */
	static function &getInstance($uid=null, $cid=0, $oid=0, $sid=0)
	{
		static $instances;

		if (!isset($instances)) 
		{
			$instances = array();
		}

		if (!isset($instances[$oid . '_' . $uid])) 
		{
			$instances[$oid . '_' . $uid] = new CoursesModelMember($uid, $cid, $oid, $sid);
		}

		return $instances[$oid . '_' . $uid];
	}

	/**
	 * Get member badge
	 * 
	 * @return     obj
	 */
	public function badge()
	{
		if (!isset($this->_badge))
		{
			$this->_badge = CoursesModelMemberBadge::loadByMemberId($this->get('id'));
		}

		return $this->_badge; 
	}

	/**
	 * Delete an entry and associated data
	 * 
	 * @return     boolean True on success, false on error
	 */
	public function delete()
	{
		// Remove gradebook information

		return parent::delete();
	}

	/**
	 * Check a user's authorization
	 * 
	 * @param      string $action Action to check
	 * @return     boolean True if authorized, false if not
	 */
	public function access($action='', $item='offering')
	{
		if (!$action)
		{
			return $this->get('permissions');
		}
		return $this->get('permissions')->get('access-' . strtolower($action) . '-' . $item);
	}
}

