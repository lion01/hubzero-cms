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

/**
 * Abstract model for forums
 */
class ForumModelAbstract extends \Hubzero\Model
{
	/**
	 * JUser
	 * 
	 * @var object
	 */
	protected $_creator = NULL;

	/**
	 * JParameter
	 * 
	 * @var object
	 */
	protected $_config = NULL;

	/**
	 * Return a formatted timestamp
	 * 
	 * @param      string $as What data to return
	 * @return     string
	 */
	public function created($as='')
	{
		switch (strtolower($as))
		{
			case 'date':
				return JHTML::_('date', $this->get('created'), JText::_('DATE_FORMAT_HZ1'));
			break;

			case 'time':
				return JHTML::_('date', $this->get('created'), JText::_('TIME_FORMAT_HZ1'));
			break;

			default:
				return $this->get('created');
			break;
		}
	}

	/**
	 * Get the creator of this entry
	 * 
	 * Accepts an optional property name. If provided
	 * it will return that property value. Otherwise,
	 * it returns the entire JUser object
	 *
	 * @param      string $rpoperty PRoperty to retrieve
	 * @return     mixed
	 */
	public function creator($property=null)
	{
		if (!isset($this->_creator) || !is_object($this->_creator))
		{
			$this->_creator = JUser::getInstance($this->get('created_by'));
		}
		if ($property && $this->_creator instanceof JUser)
		{
			return $this->_creator->get($property);
		}
		return $this->_creator;
	}

	/**
	 * Get a configuration value
	 * If no key is passed, it returns the configuration object
	 * 
	 * @param      string $key Config property to retrieve
	 * @return     mixed
	 */
	public function config($key=null)
	{
		if (!isset($this->_config))
		{
			$this->_config = JComponentHelper::getParams('com_forum');
		}
		if ($key)
		{
			return $this->_config->get($key);
		}
		return $this->_config;
	}
}

