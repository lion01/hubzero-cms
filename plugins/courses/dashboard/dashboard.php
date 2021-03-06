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

jimport('joomla.plugin.plugin');

/**
 * Courses Plugin class for course members
 */
class plgCoursesDashboard extends JPlugin
{
	/**
	 * Constructor
	 * 
	 * @param      object &$subject Event observer
	 * @param      array  $config   Optional config values
	 * @return     void
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);

		$this->loadLanguage();
	}

	/**
	 * Return the alias and name for this category of content
	 * 
	 * @return     array
	 */
	public function &onCourseAreas()
	{
		$area = array(
			'name' => $this->_name,
			'title' => JText::_('PLG_COURSES_' . strtoupper($this->_name)),
			'default_access' => $this->params->get('plugin_access', 'managers'), //$this->params->get('plugin_access', 'managers'),
			'display_menu_tab' => true
		);
		return $area;
	}

	/**
	 * Return data on a course view (this will be some form of HTML)
	 * 
	 * @param      object  $course      Current course
	 * @param      string  $option     Name of the component
	 * @param      string  $authorized User's authorization level
	 * @param      integer $limit      Number of records to pull
	 * @param      integer $limitstart Start of records to pull
	 * @param      string  $action     Action to perform
	 * @param      array   $access     What can be accessed
	 * @param      array   $areas      Active area(s)
	 * @return     array
	 */
	public function onCourse($config, $course, $offering, $action='', $areas=null)
	{
		// The output array we're returning
		$arr = array(
			'html'     => '',
			'metadata' => ''
		);

		//get this area details
		$this_area = $this->onCourseAreas();

		// Check if our area is in the array of areas we want to return results for
		if (is_array($areas)) 
		{
			if (!in_array($this_area['name'], $areas)) 
			{
				return $arr;
			}
		}
		else if ($areas != $this_area['name'])
		{
			return $arr;
		}

		// Set some variables so other functions have access
		$this->action = $action;
		$this->option = JRequest::getVar('option', 'com_courses');
		$this->course = $course;
		$this->offering = $offering;

		// Only perform the following if this is the active tab/plugin
		$this->config = $config;

		//Create user object
		$juser = JFactory::getUser();

		// Set the page title
		$document = JFactory::getDocument();
		$document->setTitle($document->getTitle() . ': ' . JText::_('PLG_COURSES_' . strtoupper($this->_name)));

		$pathway = JFactory::getApplication()->getPathway();
		$pathway->addItem(
			JText::_('PLG_COURSES_' . strtoupper($this->_name)), 
			'index.php?option=' . $this->option . '&gid=' . $this->course->get('alias') . '&offering=' . $this->offering->get('alias') . '&active=' . $this->_name
		);

		$arr['html'] .= $this->_overview();

		// Return the output
		return $arr;
	}

	/**
	 * Display a list of all announcements
	 * 
	 * @return     string HTML
	 */
	private function _overview()
	{
		Hubzero_Document::addPluginStylesheet('courses', $this->_name);
		// Get course members based on their status
		// Note: this needs to happen *after* any potential actions ar performed above
		ximport('Hubzero_Plugin_View');
		$view = new Hubzero_Plugin_View(
			array(
				'folder'  => 'courses',
				'element' => $this->_name,
				'name'    => 'overview'
			)
		);

		$view->option   = $this->option;
		$view->course   = $this->course;
		$view->offering = $this->offering;
		$view->params   = $this->params;

		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$view->setError($error);
			}
		}

		return $view->loadTemplate();
	}
}

