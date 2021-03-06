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
defined('_JEXEC') or die( 'Restricted access' );

$text = ($this->task == 'edit' ? JText::_('EDIT') : JText::_('NEW'));

JToolBarHelper::title(JText::_('COM_MEMBERS_QUOTAS').': <small><small>[ '. $text.' ]</small></small>', 'user.png');
JToolBarHelper::apply();
JToolBarHelper::save();
JToolBarHelper::cancel();
?>

<script type="text/javascript">
	function submitbutton(pressbutton)
	{
		var form = document.adminForm;

		if (pressbutton == 'cancel') {
			submitform( pressbutton );
			return;
		}

		submitform( pressbutton );
	}

	window.addEvent('domready', function() {
		$$('#class_id').addEvent('change', function ( e ) {
			e.stop();

			var req = new Request.JSON({
				url: 'index.php?option=com_members&controller=quotas&task=getClassValues',
				onSuccess: function ( data ) {
					Object.each(data, function ( val, key ) {
						var item = $$('#field-'+key);
						item.set("value", val);

						if (e.target.options[e.target.selectedIndex].text == 'custom') {
							item.removeProperty("readonly");
						} else {
							item.setProperty("readonly", true);
						}
					});
				}
			}).get({
				'class_id' : e.target.value
			});
		});
	});
</script>

<?php if ($this->getError()) : ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php endif; ?>

<form action="index.php" method="post" name="adminForm" id="item-form">
	<div class="col width-60 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('COM_MEMBERS_QUOTA_LEGEND'); ?></span></legend>

			<input type="hidden" name="fields[id]" value="<?php echo $this->row->id; ?>" />
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
			<input type="hidden" name="task" value="save" />

			<table class="admintable">
				<tbody>
					<?php if (!$this->row->id) : ?>
						<tr>
							<td class="key"><label for="field-user_id">User:</label></td>
							<td>
								<input type="text" name="fields[user_id]" id="field-user_id" value="" size="35" />
								<span class="hint">Enter a username or user ID</span>
							</td>
						</tr>
					<?php else : ?>
						<input type="hidden" name="fields[user_id]" id="field-user_id" value="<?php echo $this->row->user_id; ?>" />
					<?php endif; ?>
					<tr>
						<td class="key"><label for="field-alias"><?php echo JText::_('COM_MEMBERS_QUOTA_CLASS'); ?>:</label></td>
						<td><?php echo $this->classes; ?></td>
					</tr>
					<tr>
						<td class="key"><label for="field-soft_blocks"><?php echo JText::_('COM_MEMBERS_QUOTA_SOFT_BLOCKS'); ?>:</label></td>
						<td><input <?php echo ($this->row->class_id) ? 'readonly' : ''; ?> type="text" name="fields[soft_blocks]" id="field-soft_blocks" value="<?php echo $this->escape(stripslashes($this->row->soft_blocks)); ?>" /></td>
					</tr>
					<tr>
						<td class="key"><label for="field-hard_blocks"><?php echo JText::_('COM_MEMBERS_QUOTA_HARD_BLOCKS'); ?>:</label></td>
						<td><input <?php echo ($this->row->class_id) ? 'readonly' : ''; ?> type="text" name="fields[hard_blocks]" id="field-hard_blocks" value="<?php echo $this->escape(stripslashes($this->row->hard_blocks)); ?>" /></td>
					</tr>
					<tr>
						<td class="key"><label for="field-soft_files"><?php echo JText::_('COM_MEMBERS_QUOTA_SOFT_FILES'); ?>:</label></td>
						<td><input <?php echo ($this->row->class_id) ? 'readonly' : ''; ?> type="text" name="fields[soft_files]" id="field-soft_files" value="<?php echo $this->escape(stripslashes($this->row->soft_files)); ?>" /></td>
					</tr>
					<tr>
						<td class="key"><label for="field-hard_files"><?php echo JText::_('COM_MEMBERS_QUOTA_HARD_FILES'); ?>:</label></td>
						<td><input <?php echo ($this->row->class_id) ? 'readonly' : ''; ?> type="text" name="fields[hard_files]" id="field-hard_files" value="<?php echo $this->escape(stripslashes($this->row->hard_files)); ?>" /></td>
					</tr>
				</tbody>
			</table>
		</fieldset>
	</div>
	<div class="col width-40 fltrt">
		<table class="meta">
			<tbody>
				<tr>
					<th><?php echo JText::_('COM_MEMBERS_QUOTA_ID'); ?></th>
					<td><?php echo $this->row->user_id; ?></td>
				</tr>
				<tr>
					<th><?php echo JText::_('COM_MEMBERS_QUOTA_USERNAME'); ?></th>
					<td><?php echo $this->row->username; ?></td>
				</tr>
				<tr>
					<th><?php echo JText::_('COM_MEMBERS_QUOTA_NAME'); ?></th>
					<td><?php echo $this->row->name; ?></td>
				</tr>
			</tbody>
		</table>
		<table class="meta">
			<tbody>
				<tr>
					<th><?php echo JText::_('COM_MEMBERS_QUOTA_SPACE'); ?></th>
					<td><?php echo $this->du['info']['space'] / 1024; ?> blocks (<?php echo $this->du['percent']; ?>%)</td>
				</tr>
				<tr>
					<th><?php echo JText::_('COM_MEMBERS_QUOTA_FILES'); ?></th>
					<td><?php echo $this->du['info']['files']; ?></td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="clr"></div>
	<?php echo JHTML::_('form.token'); ?>
</form>