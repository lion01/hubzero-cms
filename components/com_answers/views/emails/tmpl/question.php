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
defined('_JEXEC') or die( 'Restricted access' );

$juri =& JURI::getInstance();

$message  = JText::_('COM_ANSWERS_EMAIL_AUTO_GENERATED') . "\n";
$message .= '----------------------------' . "\n";
$message .= strtoupper(JText::_('COM_ANSWERS_QUESTION')) . ' #' . $this->row->get('id') . "\n";
$message .= strtoupper(JText::_('COM_ANSWERS_SUMMARY')) . ': ' . $this->row->get('subject') . "\n";
$message .= strtoupper(JText::_('COM_ANSWERS_CREATED')) . ': ' . $this->row->get('created') . "\n";
$message .= '----------------------------' . "\n\n";
$message .= 'A new question #' . $this->row->get('id') . ' has been posted by: ';
$message .= ($this->row->get('anonymous')) ? 'Anonymous' . "\n" : $this->juser->get('name') . "\n\n";
$message .= 'To view the full question and take actions, go to: ' . "\n";
$message .= rtrim($juri->base(), '/') . '/' . ltrim(JRoute::_($this->row->link()), '/') . "\n";

echo $message;
