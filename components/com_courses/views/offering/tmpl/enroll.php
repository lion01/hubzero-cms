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

//get objects
$config 	= JFactory::getConfig();
$database 	= JFactory::getDBO();

$base = 'index.php?option=' . $this->option . '&controller=course&gid=' . $this->course->get('alias');
?>
	<div id="content-header">
		<h2>
			<?php echo $this->escape(stripslashes($this->course->get('title'))); ?>
		</h2>
	</div>
	<div id="content-header-extra">
		<ul>
			<li>
				<a class="browse btn" href="<?php echo JRoute::_($base); ?>">
					<?php echo JText::_('Course Overview'); ?>
				</a>
			</li>
		</ul>
	</div>

	<div class="main section">
Congrats!
	</div><!-- /.innerwrap -->