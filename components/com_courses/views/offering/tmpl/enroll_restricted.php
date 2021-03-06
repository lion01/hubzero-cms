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
?>
	<div id="content-header"<?php if ($this->course->get('logo')) { echo ' class="with-identity"'; } ?>>
		<h2>
			<?php echo $this->escape(stripslashes($this->course->get('title'))); ?>
		</h2>
		<?php if ($this->course->get('logo')) { ?>
		<p class="course-identity">
			<img src="<?php echo JURI::base(true); ?>/site/courses/<?php echo $this->course->get('id'); ?>/<?php echo $this->course->get('logo'); ?>" alt="<?php echo JText::_('Course logo'); ?>" />
		</p>
		<?php } ?>
		<p id="page_identity">
			<a class="prev" href="<?php echo JRoute::_($this->course->link()); ?>">
				<?php echo JText::_('Course overview'); ?>
			</a>
			<strong>
				<?php echo JText::_('Offering:'); ?>
			</strong>
			<span>
				<?php echo $this->escape(stripslashes($this->course->offering()->get('title'))); ?>
			</span>
			<strong>
				<?php echo JText::_('Section:'); ?>
			</strong>
			<span>
				<?php echo $this->escape(stripslashes($this->course->offering()->section()->get('title'))); ?>
			</span>
		</p>
	</div><!-- #content-header -->

	<div class="main section enroll-restricted">
		<?php
			foreach ($this->notifications as $notification) {
				echo "<p class=\"{$notification['type']}\">{$notification['message']}</p>";
			}
		?>

		<form action="<?php echo JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->course->get('alias') . '&offering=' . $this->course->offering()->get('alias') . ($this->course->offering()->section()->get('alias') !== '__default' ? ':' . $this->course->offering()->section()->get('alias') : '') . '&task=enroll'); ?>" method="post" id="hubForm">
			<div class="explaination">
				<h3><?php echo JText::_('Code not working?'); ?></h3>
				<p><?php echo JText::_('It may be possible that the code has already been redeemed.'); ?></p>
			</div>
			<fieldset>
				<legend><?php echo JText::_('Redeem Coupon Code'); ?></legend>

				<p class="warning"><?php echo JText::_('This course has restricted enrollment and requires a coupon code.'); ?></p>

				<label for="field-code">
					<?php echo JText::_('Coupon Code'); ?> <span class="required"><?php echo JText::_('COM_COURSES_REQUIRED'); ?></span>
					<input type="text" name="code" id="field-code" size="35" value="" />
				</label>
			</fieldset>
			<div class="clear"></div>

			<input type="hidden" name="offering" value="<?php echo $this->escape($this->course->offering()->get('alias')); ?>" />
			<input type="hidden" name="gid" value="<?php echo $this->escape($this->course->get('alias')); ?>" />
			<input type="hidden" name="option" value="<?php echo $this->escape($this->option); ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->escape($this->controller); ?>" />
			<input type="hidden" name="task" value="enroll" />

			<p class="submit">
				<input type="submit" value="<?php echo JText::_('Redeem'); ?>" />
			</p>
		</form>
	</div><!-- /.main section -->