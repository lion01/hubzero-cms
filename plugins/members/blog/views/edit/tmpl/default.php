<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

JPluginHelper::importPlugin( 'hubzero' );
$dispatcher = JDispatcher::getInstance();
$tf = $dispatcher->trigger( 'onGetMultiEntry', array(array('tags', 'tags', 'actags','',$this->entry->tags('string'))) );

if ($this->entry->get('publish_down') && $this->entry->get('publish_down') == '0000-00-00 00:00:00')
{
	$this->entry->set('publish_down', '');
}
?>
<ul id="page_options">
	<li>
		<a class="icon-archive archive btn" href="<?php echo JRoute::_('index.php?option=com_members&id=' . $this->member->get('uidNumber') . '&active=blog'); ?>">
			<?php echo JText::_('PLG_MEMBERS_BLOG_ARCHIVE'); ?>
		</a>
	</li>
</ul>

<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>
	
	<form action="<?php echo JRoute::_('index.php?option=' . $this->option . '&id=' . $this->member->get('uidNumber') . '&active=blog&task=save'); ?>" method="post" id="hubForm" class="full">
		<fieldset>
			<legend><?php echo JText::_('PLG_MEMBERS_BLOG_EDIT_DETAILS'); ?></legend>

			<label for="field-title"<?php if ($this->task == 'save' && !$this->entry->get('title')) { echo ' class="fieldWithErrors"'; } ?>>
				<?php echo JText::_('PLG_MEMBERS_BLOG_TITLE'); ?>
				<input type="text" name="entry[title]" id="field-title" size="35" value="<?php echo $this->escape(stripslashes($this->entry->get('title'))); ?>" />
			</label>
<?php if ($this->task == 'save' && !$this->entry->get('title')) { ?>
			<p class="error"><?php echo JText::_('PLG_MEMBERS_BLOG_ERROR_PROVIDE_TITLE'); ?></p>
<?php } ?>

			<label for="entrycontent">
				<?php echo JText::_('PLG_MEMBERS_BLOG_FIELD_CONTENT'); ?>
				<span class="syntax hint"><a class="tooltips popup" href="<?php echo JRoute::_('index.php?option=com_wiki&scope=&pagename=Help:WikiFormatting'); ?>" title="Syntax Reference :: <table class=&quot;wiki-reference&quot;>
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
							<td><span style=&quot;text-decoration:underline;&quot;>underline</span></td>
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
						<tr>
							<td colspan=&quot;2&quot;><a href=&quot;<?php echo JRoute::_('index.php?option=com_wiki&scope=&pagename=Help:WikiMacros#image'); ?>&quot;>[[Image(filename.jpg)]]</a> includes an image</td>
						</tr>
						<tr>
							<td colspan=&quot;2&quot;><a href=&quot;<?php echo JRoute::_('index.php?option=com_wiki&scope=&pagename=Help:WikiMacros#file'); ?>&quot;>[[File(filename.pdf)]]</a> includes a file</td>
						</tr>
					</tbody>
				</table>">Wiki formatting</a> is allowed.</span>
				<?php
				ximport('Hubzero_Wiki_Editor');
				echo Hubzero_Wiki_Editor::getInstance()->display('entry[content]', 'entrycontent', stripslashes($this->entry->get('content')), '', '50', '30');
				?>
			</label>
		<?php if ($this->task == 'save' && !$this->entry->get('content')) { ?>
			<p class="error"><?php echo JText::_('PLG_MEMBERS_BLOG_ERROR_PROVIDE_CONTENT'); ?></p>
		<?php } ?>

			<fieldset>
				<legend><?php echo JText::_('Uploaded files'); ?></legend>
				<div class="field-wrap">
					<iframe width="100%" height="260" name="filer" id="filer" src="<?php echo 'index.php?option=com_blog&controller=media&id=' . $this->member->get('uidNumber') . '&scope=member&tmpl=component'; ?>"></iframe>
				</div>
			</fieldset>

			<label>
				<?php echo JText::_('PLG_MEMBERS_BLOG_FIELD_TAGS'); ?>
			<?php if (count($tf) > 0) {
				echo implode("\n", $tf);
			} else { ?>
				<input type="text" name="tags" value="<?php echo $this->escape($this->entry->tags('string')); ?>" />
			<?php } ?>
				<span class="hint"><?php echo JText::_('PLG_MEMBERS_BLOG_FIELD_TAGS_HINT'); ?></span>
			</label>
			
			<div class="group">
				<label for="field-allow_comments">
					<input type="checkbox" class="option" name="entry[allow_comments]" id="field-allow_comments" value="1"<?php if ($this->entry->get('allow_comments') == 1) { echo ' checked="checked"'; } ?> /> 
					<?php echo JText::_('PLG_MEMBERS_BLOG_FIELD_ALLOW_COMMENTS'); ?>
				</label>

				<label for="field-state">
					<?php echo JText::_('PLG_MEMBERS_BLOG_FIELD_PRIVACY'); ?>
					<select name="entry[state]" id="field-state">
						<option value="1"<?php if ($this->entry->get('state') == 1) { echo ' selected="selected"'; } ?>><?php echo JText::_('PLG_MEMBERS_BLOG_FIELD_STATE_PUBLIC'); ?></option>
						<option value="2"<?php if ($this->entry->get('state') == 2) { echo ' selected="selected"'; } ?>><?php echo JText::_('PLG_MEMBERS_BLOG_FIELD_STATE_REGISTERED'); ?></option>
						<option value="0"<?php if ($this->entry->get('state') == 0) { echo ' selected="selected"'; } ?>><?php echo JText::_('PLG_MEMBERS_BLOG_FIELD_STATE_PRIVATE'); ?></option>
					</select>
				</label>
			</div>
			
			<div class="group">
				<label for="field-publish_up">
					<?php echo JText::_('PLG_MEMBERS_BLOG_PUBLISH_UP'); ?>
					<input type="text" name="entry[publish_up]" id="field-publish_up" size="35" value="<?php echo $this->escape(stripslashes($this->entry->get('publish_up'))); ?>" />
					<span class="hint"><?php echo JText::_('PLG_MEMBERS_BLOG_FIELD_PUBLISH_HINT'); ?></span>
				</label>

				<label for="field-publish_down">
					<?php echo JText::_('PLG_MEMBERS_BLOG_PUBLISH_DOWN'); ?>
					<input type="text" name="entry[publish_down]" id="field-publish_down" size="35" value="<?php echo $this->escape(stripslashes($this->entry->get('publish_down'))); ?>" />
					<span class="hint"><?php echo JText::_('PLG_MEMBERS_BLOG_FIELD_PUBLISH_HINT'); ?></span>
				</label>
			</div>
		</fieldset>
		<div class="clear"></div>

		<input type="hidden" name="id" value="<?php echo $this->escape($this->entry->get('created_by')); ?>" />
		<input type="hidden" name="entry[id]" value="<?php echo $this->escape($this->entry->get('id')); ?>" />
		<input type="hidden" name="entry[alias]" value="<?php echo $this->escape($this->entry->get('alias')); ?>" />
		<input type="hidden" name="entry[created]" value="<?php echo $this->escape($this->entry->get('created')); ?>" />
		<input type="hidden" name="entry[created_by]" value="<?php echo $this->escape($this->entry->get('created_by')); ?>" />
		<input type="hidden" name="entry[scope]" value="member" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="active" value="blog" />
		<input type="hidden" name="task" value="view" />
		<input type="hidden" name="action" value="save" />
		
		<p class="submit">
			<input class="btn btn-succes" type="submit" value="<?php echo JText::_('PLG_MEMBERS_BLOG_SAVE'); ?>" />
		<?php if ($this->entry->get('id')) { ?>
			<a class="btn btn-secondary" href="<?php echo JRoute::_($this->entry->link()); ?>">
				<?php echo JText::_('PLG_MEMBERS_BLOG_CANCEL'); ?>
			</a>
		<?php } ?>
		</p>
	</form>
</div><!-- / .section -->
