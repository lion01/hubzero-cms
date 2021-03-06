<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2013 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2013 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_ROOT . DS . 'components' . DS . 'com_forum' . DS . 'models' . DS . 'adapters' . DS . 'abstract.php');

/**
 * Adapter class for a forum post link for course forum
 */
class ForumModelAdapterCourse extends ForumModelAdapterAbstract
{
	/**
	 * URL segments
	 * 
	 * @var string
	 */
	protected $_segments = array(
		'option' => 'com_courses',
	);

	/**
	 * Constructor
	 * 
	 * @param      integer $scope_id Scope ID (group, course, etc.)
	 * @return     void
	 */
	public function __construct($scope_id=0)
	{
		$offering = CoursesModelOffering::getInstance($this->get('scope_id'));
		$course = CoursesModelCourse::getInstance($offering->get('course_id'));

		$this->_segments['gid']      = $course->get('alias');
		$this->_segments['offering'] = $offering->get('alias') . ($offering->section()->get('alias') != '__default' ? ':' . $offering->section()->get('alias') : '');
		$this->_segments['active']   = 'discussions';
	}

	/**
	 * Generate and return various links to the entry
	 * Link will vary depending upon action desired, such as edit, delete, etc.
	 * 
	 * @param      string $type   The type of link to return
	 * @param      mixed  $params Optional string or associative array of params to append
	 * @return     string
	 */
	public function build($type='', $params=null)
	{
		$segments = $this->_segments;

		if ($this->get('section'))
		{
			$segments['unit'] = $this->get('section');
		}
		if ($this->get('category'))
		{
			$segments['b'] = $this->get('category');
		}
		if ($this->get('thread'))
		{
			$segments['c'] = $this->get('thread');
		}

		$anchor = '';

		// If it doesn't exist or isn't published
		switch (strtolower($type))
		{
			case 'base':
				return $this->_base . '?' . (string) $this->_build($this->_segments);
			break;

			case 'edit':
				if ($this->get('thread'))
				{
					$segments['c'] = $this->get('post');
				}
				$segments['task'] = 'edit';
			break;

			case 'delete':
				if ($this->get('thread'))
				{
					$segments['c'] = $this->get('post');
				}
				$segments['task'] = 'delete';
			break;

			case 'new':
			case 'newthread':
				if ($this->get('thread'))
				{
					unset($segments['c']);
				}
				$segments['task'] = 'new';
			break;

			case 'download':
				$segments['post'] = $this->get('post');
				$segments['file'] = '';
			break;

			case 'reply':
				$segments['reply'] = $this->get('post');
			break;

			case 'anchor':
				if ($this->get('post'))
				{
					$anchor = '#c' . $this->get('post');
				}
			break;

			case 'abuse':
				return 'index.php?option=com_support&task=reportabuse&category=forum&id=' . $this->get('post') . '&parent=' . $this->get('parent');
			break;

			case 'permalink':
			default:

			break;
		}

		if (is_string($params))
		{
			$params = str_replace('&amp;', '&', $params);

			if (substr($params, 0, 1) == '#')
			{
				$anchor = $params;
			}
			else
			{
				if (substr($params, 0, 1) == '?')
				{
					$params = substr($params, 1);
				}
				parse_str($params, $parsed);
				$params = $parsed;
			}
		}

		$segments = array_merge($segments, (array) $params);

		return $this->_base . '?' . (string) $this->_build($segments) . (string) $anchor;
	}
}
