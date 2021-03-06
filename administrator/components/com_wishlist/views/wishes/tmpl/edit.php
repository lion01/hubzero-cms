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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$canDo = WishlistHelper::getActions('list');

$text = ($this->task == 'edit' ? JText::_('COM_WISHLIST_EDIT') : JText::_('COM_WISHLIST_NEW'));

JToolBarHelper::title(JText::_('COM_WISHLIST') . ': ' . JText::_('COM_WISHLIST_WISH') . ': ' . $text, 'wishlist.png');
if ($canDo->get('core.edit')) 
{
	JToolBarHelper::apply();
	JToolBarHelper::save();
	JToolBarHelper::spacer();
}
JToolBarHelper::cancel();

jimport('joomla.html.editor');
$editor = JEditor::getInstance();

JHTML::_('behavior.tooltip');
?>
<script type="text/javascript">
var ownerassignees = new Array;
<?php
$i = 0;
if ($this->ownerassignees)
{
	foreach ($this->ownerassignees as $k => $items) 
	{
		foreach ($items as $v) 
		{
			echo 'ownerassignees[' . $i++ . "] = new Array( '$k','" . addslashes($v->id) . "','" . addslashes($v->name) . "' );\n\t\t";
		}
	}
}
?>

function submitbutton(pressbutton) 
{
	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}

	// do field validation
	if (document.getElementById('field-about').value == ''){
		alert(<?php echo JText::_('COM_WISHLIST_ERROR_MISSING_TEXT'); ?>);
	} else {
		submitform(pressbutton);
	}
}
</script>

<form action="index.php" method="post" name="adminForm" id="item-form">
	<div class="col width-60 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('COM_WISHLIST_DETAILS'); ?></span></legend>

			<table class="admintable">
				<tbody>
					<tr>
						<th class="key"><label for="field-wishlist"><?php echo JText::_('COM_WISHLIST_CATEGORY'); ?>:</label></th>
						<td>
							<select name="fields[wishlist]" id="field-wishlist" onchange="changeDynaList('fieldassigned', ownerassignees, document.getElementById('field-wishlist').options[document.getElementById('field-wishlist').selectedIndex].value, 0, 0);">
								<option value="0"<?php echo ($this->row->wishlist == 0) ? ' selected="selected"' : ''; ?>><?php echo JText::_('[none]'); ?></option>
<?php if ($this->lists) { ?>
	<?php foreach ($this->lists as $list) { ?>
								<option value="<?php echo $list->id; ?>"<?php echo ($this->row->wishlist == $list->id) ? ' selected="selected"' : ''; ?>><?php echo $this->escape(stripslashes($list->title)); ?></option>
	<?php } ?>
<?php } ?>
							</select>
						</td>
					</tr>
					<tr>
						<th class="key"><label for="field-subject"><?php echo JText::_('COM_WISHLIST_TITLE'); ?>:</label></th>
						<td><input type="text" name="fields[subject]" id="field-subject" size="30" maxlength="150" value="<?php echo $this->escape(stripslashes($this->row->subject)); ?>" /></td>
					</tr>
					<tr>
						<th class="key"><label for="field-about"><?php echo JText::_('COM_WISHLIST_DESCRIPTION'); ?>:</label></th>
						<td><textarea name="fields[about]" id="field-about" cols="35" rows="30"><?php echo $this->escape(stripslashes($this->row->about)); ?></textarea></td>
					</tr>
					<tr>
						<th class="key"><label for="field-tags"><?php echo JText::_('COM_WISHLIST_TAGS'); ?>:</label></th>
						<td><input type="text" name="fields[tags]" id="field-tags" size="30" maxlength="150" value="<?php echo $this->escape(stripslashes($this->tags)); ?>" /></td>
					</tr>
				</tbody>
			</table>
		</fieldset>

		<fieldset class="adminform">
			<legend><span><?php echo JText::_('COM_WISHLIST_PLAN'); ?></span></legend>

			<table class="admintable">
				<tbody>
					<?php if ($this->plan->id) { ?>
					<tr>
						<th class="key"><label for="plan-create_revision"><?php echo JText::_('PLAN_NEW_REVISION'); ?></label></th>
						<td>
							<input type="checkbox" class="option" name="plan[create_revision]" id="plan-create_revision" value="1" />
						</td>
					</tr>
					<?php } ?>
					<tr>
						<th class="key"><?php echo JText::_('COM_WISHLIST_DUE'); ?>:</th>
						<td>
							<label for="field-due-never">
								<input class="option" type="radio" name="fields[due]" id="field-due-never" value="0" <?php echo ($this->row->due == '' || $this->row->due == '0000-00-00 00:00:00') ? 'checked="checked"' : ''; ?> /> 
								<?php echo JText::_('COM_WISHLIST_DUE_NEVER'); ?>
							</label>
						</td>
						<th class="key"><?php echo JText::_('COM_WISHLIST_OR'); ?></th>
						<td>
							<label for="field-due-on">
								<input class="option" type="radio" name="fields[due]" id="field-due-on" value="0" <?php echo ($this->row->due != '' && $this->row->due != '0000-00-00 00:00:00') ? 'checked="checked"' : ''; ?> /> 
								<?php echo JText::_('COM_WISHLIST_DUE_ON'); ?>
							</label>
							<label for="field-due">
								<input class="option" type="text" name="fields[due]" id="field-due" size="10" maxlength="10" value="<?php echo $this->escape($this->row->due); ?>" />
							</label>
						</td>
					</tr>
					<tr>
						<th class="key"><label for="fieldassigned"><?php echo JText::_('COM_WISHLIST_ASSIGNED'); ?>:</label></th>
						<td colspan="3">
							<select name="fields[assigned]" id="fieldassigned">
<?php if ($this->assignees) { ?>
	<?php foreach ($this->assignees as $assignee) { ?>
								<option value="<?php echo $assignee->id; ?>"<?php echo ($this->row->assigned == $assignee->id) ? ' selected="selected"' : ''; ?>><?php echo $this->escape(stripslashes($assignee->name)); ?></option>
	<?php } ?>
<?php } ?>
							</select>
						</td>
					</tr>
					<tr>
						<th class="key"><label for="plan-pagetext"><?php echo JText::_('COM_WISHLIST_PAGETEXT'); ?>:</label></th>
						<td colspan="3">
							<textarea name="plan[pagetext]" id="plan-pagetext" cols="35" rows="30"><?php echo $this->escape(stripslashes($this->plan->pagetext)); ?></textarea>
						</td>
					</tr>
				</tbody>
			</table>
			
			<input type="hidden" name="plan[id]" id="plan-id" value="<?php echo $this->plan->id; ?>" />
			<input type="hidden" name="plan[wishid]" value="<?php echo $this->row->id; ?>" />
			<input type="hidden" name="plan[version]" value="<?php echo $this->plan->version; ?>" />
			<input type="hidden" name="plan[approved]" value="<?php echo $this->plan->approved; ?>" />
			<?php if (!$this->plan->id) { ?>
				<input type="hidden" name="plan[create_revision]" id="plan-create_revision" value="0" />
			<?php } ?>
		</fieldset>
	</div>
	<div class="col width-40 fltrt">
		<table class="meta" summary="<?php echo JText::_('Metadata'); ?>">
			<tbody>
				<tr>
					<th class="key"><?php echo JText::_('ID'); ?>:</th>
					<td>
						<?php echo $this->row->id; ?>
						<input type="hidden" name="fields[id]" id="field-id" value="<?php echo $this->row->id; ?>" />
					</td>
				</tr>
				<tr>
					<th class="key"><?php echo JText::_('Created'); ?>:</th>
					<td>
						<time datetime="<?php echo $this->row->proposed; ?>"><?php echo $this->row->proposed; ?></time>
						<input type="hidden" name="fields[proposed]" id="field-proposed" value="<?php echo $this->row->proposed; ?>" />
					</td>
				</tr>
				<tr>
					<th class="key"><?php echo JText::_('Created by'); ?>:</th>
					<td>
						<?php 
						$editor = JUser::getInstance($this->row->proposed_by);
						echo ($editor) ? $this->escape(stripslashes($editor->get('name'))) : JText::_('unknown'); 
						?>
						<input type="hidden" name="fields[proposed_by]" id="field-proposed_by" value="<?php echo $this->row->proposed_by; ?>" />
					</td>
				</tr>
				<tr>
					<th class="key"><?php echo JText::_('Ranking'); ?>:</th>
					<td>
						<?php echo $this->row->ranking; ?>
						<input type="hidden" name="fields[ranking]" id="field-ranking" value="<?php echo $this->row->ranking; ?>" />
					</td>
				</tr>
			</tbody>
		</table>
		
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('COM_WISHLIST_PARAMETERS'); ?></span></legend>

			<table class="admintable">
				<tbody>
					<tr>
						<th class="key"><label for="field-anonymous"><?php echo JText::_('COM_WISHLIST_ANONYMOUS'); ?>:</label></th>
						<td><input type="checkbox" name="fields[anonymous]" id="field-anonymous" value="1" <?php echo $this->row->anonymous ? 'checked="checked"' : ''; ?> /></td>
					</tr>
					<tr>
						<th class="key"><label for="field-private"><?php echo JText::_('COM_WISHLIST_PRIVATE'); ?>:</label></th>
						<td><input type="checkbox" name="fields[private]" id="field-private" value="1" <?php echo $this->row->private ? 'checked="checked"' : ''; ?> /></td>
					</tr>
					<tr>
						<th class="key"><label for="field-points"><?php echo JText::_('COM_WISHLIST_POINTS'); ?>:</label></th>
						<td><input type="text" name="fields[points]" id="field-points" value="<?php echo $this->escape($this->row->points); ?>" /></td>
					</tr>
					<tr>
						<th class="key"><label for="field-status"><?php echo JText::_('COM_WISHLIST_STATUS'); ?>:</label></th>
						<td>
							<select name="fields[status]" id="field-status">
								<option value="0"<?php echo ($this->row->status == 0) ? ' selected="selected"' : ''; ?>><?php echo JText::_('new/pending'); ?></option>
								<option value="1"<?php echo ($this->row->status == 1) ? ' selected="selected"' : ''; ?>><?php echo JText::_('granted'); ?></option>
								<option value="2"<?php echo ($this->row->status == 2) ? ' selected="selected"' : ''; ?>><?php echo JText::_('deleted'); ?></option>
								<option value="3"<?php echo ($this->row->status == 3) ? ' selected="selected"' : ''; ?>><?php echo JText::_('rejected'); ?></option>
								<option value="3"<?php echo ($this->row->status == 4) ? ' selected="selected"' : ''; ?>><?php echo JText::_('withdrawn'); ?></option>
							</select>
						</td>
					</tr>
					<tr>
						<th class="key"><label for="field-accepted"><?php echo JText::_('COM_WISHLIST_ACCEPTED'); ?>:</label></th>
						<td><input type="checkbox" name="fields[accepted]" id="field-accepted" value="1" <?php echo $this->row->accepted ? 'checked="checked"' : ''; ?> /></td>
					</tr>
				</tbody>
			</table>
		</fieldset>
	</div>
	<div class="clr"></div>
	
	<?php /*if (version_compare(JVERSION, '1.6', 'ge')) { ?>
		<?php if ($canDo->get('core.admin')): ?>
			<div class="col width-100 fltlft">
				<fieldset class="panelform">
					<legend><span><?php echo JText::_('COM_WISHLIST_FIELDSET_RULES'); ?></span></legend>
					<?php echo $this->form->getLabel('rules'); ?>
					<?php echo $this->form->getInput('rules'); ?>
				</fieldset>
			</div>
			<div class="clr"></div>
		<?php endif; ?>
	<?php }*/ ?>
	
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="wishlist" value="<?php echo $this->wishlist; ?>" />
	<input type="hidden" name="task" value="save" />
	
	<?php echo JHTML::_('form.token'); ?>
</form>
