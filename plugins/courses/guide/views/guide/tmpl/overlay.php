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

defined('_JEXEC') or die('Restricted access');

JPluginHelper::importPlugin('courses');
$plugins = JDispatcher::getInstance()->trigger('onCourseAreas', array());

$course_plugin_access = array();
foreach ($plugins as $plugin)
{
	$course_plugin_access[$plugin['name']] = $plugin['default_access'];
}
?>
<div id="guide-overlay" class="guide-wrap" data-action="<?php echo JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->course->get('alias') . '&offering=' . $this->offering->get('alias') . ($this->offering->section()->get('alias') != '__default' ? ':' . $this->offering->section()->get('alias') : '') . '&active=' . $this->plugin . '&unit=mark'); ?>">
	<div class="guide-content">

		<div class="grid">
			<div class="col span-half">
				<div class="guide-nav">
					<ul>
						<?php
						foreach ($plugins as $k => $cat)
						{
							//do we want to show category in menu?
							if ($cat['display_menu_tab'])
							{
								if (!$this->course->offering()->access('manage', 'section') 
								 && isset($course_plugin_access[$cat['name']]) 
								 && $course_plugin_access[$cat['name']] == 'managers')
								{
									continue;
								}
							}
							?>
							<li>
								<strong class="<?php echo $cat['name']; ?>"><?php echo $cat['title']; ?></strong> <span><?php echo JText::_('PLG_COURSES_' . strtoupper($cat['name']) . '_BLURB'); ?></span>
							</li>
							<?php
						}
						?>
					</ul>
				</div>
			</div>
			<div class="col span-half omega">
				<div class="guide-about">
					<h3><?php echo JText::_('Welcome to the course!'); ?></h3>
					<p><?php echo JText::_('We\'ve tried to organize things to group related content and make it easier to find what you need. Feel free to explore the various menu options.'); ?></p>
					<p><?php echo JText::sprintf('You can always get back to the %s by clicking the link found under the title of this course.', '<a href="' . JRoute::_('index.php?option=com_courses&gid=' . $this->course->get('alias')) . '">Course overview</a>'); ?></p>
					<p class="guide-dismiss">
						<?php echo JText::_('Click anywhere to dismiss this guide and get started!'); ?>
					</p>
				</div>

				<div class="guide-onemorething">
					<p><?php echo JText::_('Oh, and one more thing:'); ?></p>
					<p class="guide-luck"><?php echo JText::_('Good Luck!'); ?></p>
				</div>
			</div>
		</div>

	</div><!-- / .guide-content -->
</div><!-- / .guide-wrap -->