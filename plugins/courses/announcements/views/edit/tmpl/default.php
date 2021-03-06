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

$juser = JFactory::getUser();
?>
<div class="main section">
<?php if ($this->getError()) { ?>
	<p class="error"><?php echo implode('<br />', $this->getErrors()); ?></p>
<?php } ?>
	<form action="<?php echo JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->course->get('alias') . '&offering=' . $this->offering->get('alias') . '&active=announcements'); ?>" method="post" id="hubForm">
		<div class="explaination">
			<table class="wiki-reference">
				<caption><?php echo JText::_('PLG_COURSES_ANNOUNCEMENTS_WIKI_SYNTAX_REFERENCE'); ?></caption>
				<tbody>
					<tr>
						<td>'''bold'''</td>
						<td><b>bold</b></td>
					</tr>
					<tr>
						<td>''italic''</td>
						<td><i>italic</i></td>
					</tr>
					<tr>
						<td>__underline__</td>
						<td><span style="text-decoration:underline;">underline</span></td>
					</tr>
					<tr>
						<td>{{{monospace}}}</td>
						<td><code>monospace</code></td>
					</tr>
					<tr>
						<td>~~strike-through~~</td>
						<td><del>strike-through</del></td>
					</tr>
					<tr>
						<td>^superscript^</td>
						<td><sup>superscript</sup></td>
					</tr>
					<tr>
						<td>,,subscript,,</td>
						<td><sub>subscript</sub></td>
					</tr>
				</tbody>
			</table>
		</div><!-- /.aside -->
		<fieldset>
			<legend>
				<?php if ($this->model->get('id')) { ?>
						<?php echo JText::_('PLG_COURSES_ANNOUNCEMENTS_EDIT'); ?>
				<?php } else { ?>
						<?php echo JText::_('PLG_COURSES_ANNOUNCEMENTS_NEW'); ?>
				<?php } ?>
			</legend>

			<label for="field_content">
				<?php echo JText::_('PLG_COURSES_ANNOUNCEMENTS_FIELD_CONTENT'); ?> <span class="required"><?php echo JText::_('PLG_COURSES_ANNOUNCEMENTS_REQUIRED'); ?></span>
				<?php
				ximport('Hubzero_Wiki_Editor');
				echo Hubzero_Wiki_Editor::getInstance()->display('fields[content]', 'field_content', $this->escape(stripslashes($this->model->get('content'))), 'minimal no-footer', '35', '5');
				?>
			</label>

			<fieldset>
				<legend><?php echo JText::_('PLG_COURSES_ANNOUNCEMENTS_FIELD_PUBLISH_WINDOW'); ?></legend>
				<div class="grid">
					<div class="col span-half">
						<label for="field-publish_up" id="priority-publish_up">
							<?php echo JText::_('PLG_COURSES_ANNOUNCEMENTS_FIELD_START'); ?>
							<input class="datepicker" type="text" name="fields[publish_up]" id="field-publish_up" value="<?php echo $this->escape($this->model->get('publish_up')); ?>" /> 
							<span class="hint"><?php echo JText::_('PLG_COURSES_ANNOUNCEMENTS_PUBLISH_HINT'); ?></span>
						</label>
					</div>
					<div class="col span-half omega">
						<label for="field-publish_down" id="priority-publish_down">
							<?php echo JText::_('PLG_COURSES_ANNOUNCEMENTS_FIELD_END'); ?>
							<input class="datepicker" type="text" name="fields[publish_down]" id="field-publish_down" value="<?php echo $this->escape($this->model->get('publish_down')); ?>" /> 
							<span class="hint"><?php echo JText::_('PLG_COURSES_ANNOUNCEMENTS_PUBLISH_HINT'); ?></span>
						</label>
					</div>
				</div>
			</fieldset>

			<label for="field-priority" id="priority-label">
				<input class="option" type="checkbox" name="fields[priority]" id="field-priority" value="1"<?php if ($this->model->get('priority')) { echo ' checked="checked"'; } ?> /> 
				<?php echo JText::_('PLG_COURSES_ANNOUNCEMENTS_FIELD_PRIORITY'); ?>
			</label>
		</fieldset>
		<div class="clear"></div>

		<p class="submit">
			<input class="btn btn-success" type="submit" value="<?php echo JText::_('PLG_COURSES_ANNOUNCEMENTS_SUBMIT'); ?>" />
			<a class="btn btn-secondary" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->course->get('alias') . '&offering=' . $this->offering->get('alias') . '&active=announcements'); ?>">
				<?php echo JText::_('PLG_COURSES_ANNOUNCEMENTS_CANCEL'); ?>
			</a>
		</p>

		<input type="hidden" name="fields[id]" value="<?php echo $this->model->get('id'); ?>" />
		<input type="hidden" name="fields[state]" value="1" />
		<input type="hidden" name="fields[offering_id]" value="<?php echo $this->offering->get('id'); ?>" />
		<input type="hidden" name="fields[section_id]" value="<?php echo $this->offering->section()->get('id'); ?>" />

		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="gid" value="<?php echo $this->course->get('alias'); ?>" />
		<input type="hidden" name="offering" value="<?php echo $this->offering->get('alias') . ($this->offering->section()->get('alias') != '__default' ? ':' . $this->offering->section()->get('alias') : ''); ?>" />
		<input type="hidden" name="active" value="announcements" />
		<input type="hidden" name="action" value="save" />

		<?php echo JHTML::_('form.token'); ?>
	</form>
</div><!-- / .main section -->