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

// Menu
JToolBarHelper::title(JText::_('COM_MEMBERS_QUOTAS'), 'user.png');
JToolBarHelper::addNew();
JToolBarHelper::editList();
JToolBarHelper::custom('restoreDefault', 'restore', 'restore', 'Default');
?>

<script type="text/javascript">
	window.addEvent('domready', function() {
		var rows = $$('.quota-row');

		rows.each(function ( val, key ) {
			var id    = val.getElements('.row-id')[0].value;
			var usage = val.getElements('.usage-outer')[0];

			var req = new Request.JSON({
				url: 'index.php?option=com_members&controller=quotas&task=getQuotaUsage',
				onSuccess: function ( data ) {
					if (data.percent > 100) {
						data.percent = 100;
						usage.getChildren('.usage-inner').addClass('max');
					}
					usage.setStyle('display', 'block');
					usage.getPrevious('.usage-calculating').setStyle('display', 'none');
					usage.getChildren('.usage-inner').setStyle('width', data.percent+"%");
				},
				onError: function ( ) {
					usage.getPrevious('.usage-calculating').setStyle('display', 'none');
					usage.getNext('.usage-unavailable').setStyle('display', 'block');
				}
			}).get({
				'id' : id
			});
		});
	});
</script>

<style>
	.usage-unavailable {
		display: none;
	}
	.usage-outer {
		display: none;
		border: 1px solid #AAAAAA;
		margin: 5px 0 0 10px;
		height: 10px;
	}
	.usage-inner {
		background: green;
		width: 0%;
		height: 100%;
		box-shadow: inset 0 0 5px #F1F1F1;
	}
	.usage-inner.max {
		background: red;
	}
</style>

<div role="navigation" class="sub-navigation">
	<ul id="subsubmenu">
		<li><a href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>" class="active">Members</a></li>
		<li><a href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=displayClasses">Quota Classes</a></li>
		<li><a href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=import">Import</a></li>
	</ul>
</div>

<form action="index.php" method="post" name="adminForm" id="adminForm">
	<table class="adminlist" summary="<?php echo JText::_('COM_MEMBERS_QUOTAS_TABLE_SUMMARY'); ?>">
		<thead>
			<tr>
				<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->rows);?>);" /></th>
				<th><?php echo JText::_('COM_MEMBERS_QUOTA_USER_ID'); ?></th>
				<th><?php echo JText::_('COM_MEMBERS_QUOTA_USERNAME'); ?></th>
				<th><?php echo JText::_('COM_MEMBERS_QUOTA_NAME'); ?></th>
				<th><?php echo JText::_('COM_MEMBERS_QUOTA_CLASS'); ?></th>
				<th><?php echo JText::_('COM_MEMBERS_QUOTA_DISK_USAGE'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="6">
					<?php echo $this->pageNav->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
<?php
$k = 0;
for ($i=0, $n=count($this->rows); $i < $n; $i++)
{
	$row = &$this->rows[$i];
?>
			<tr class="<?php echo "row$k quota-row"; ?>">
				<td>
					<input class="row-id" type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $row->id; ?>" onclick="isChecked(this.checked);" />
				</td>
				<td>
					<a href="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=edit&amp;id[]=<? echo $row->id; ?>">
						<?php echo $this->escape($row->user_id); ?>
					</a>
				</td>
				<td>
					<a href="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=edit&amp;id[]=<? echo $row->id; ?>">
						<?php echo $this->escape($row->username); ?>
					</a>
				</td>
				<td>
					<?php echo $this->escape($row->name); ?>
				</td>
				<td>
					<?php echo ($row->class_alias) ? $this->escape($row->class_alias) : 'custom'; ?>
				</td>
				<td>
					<div class="usage-calculating">[calculating...]</div>
					<div class="usage-outer">
						<div class="usage-inner"></div>
					</div>
					<div class="usage-unavailable">unavailable</div>
				</td>
			</tr>
<?php
	$k = 1 - $k;
}
?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo JHTML::_('form.token'); ?>
</form>