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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

//set the title
JToolBarHelper::title('<a href="index.php?option='.$this->option.'">' . JText::_( 'Newsletter Templates' ) . '</a>', 'template.png');

//add toolbar buttons
JToolBarHelper::addNew();
JToolBarHelper::editList();
JToolBarHelper::custom('duplicate', 'copy', '', 'Copy');
JToolBarHelper::deleteList('Are you sure you want to delete the selected Newsletter Template(s)?', 'delete');
JToolBarHelper::spacer();
JToolBarHelper::preferences($this->option, '550');
?>
<?php
	if ($this->getError())
	{
		echo '<p class="error">' . $this->getError() . '</p>';
	}
?>
<form action="index.php" method="post" name="adminForm">
	<table class="adminlist">
		<thead>
			<tr>
				<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->templates); ?>);" /></th>
				<th><?php echo JText::_('Template'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php if (count($this->templates) > 0) : ?>
				<?php foreach($this->templates as $k => $template) : ?>
					<tr>
						<td width="30">
							<input type="checkbox" name="id[]" id="cb<?php echo $k;?>" value="<?php echo $template->id; ?>" onclick="isChecked(this.checked);" />
						</td>
						<td>
							<?php echo $template->name; ?>
							<?php if (!$template->editable) : ?>
								<br />
								<span class="hint"><?php echo JText::_('This template is not editable or deletable. Copy it to create a new template.'); ?></span>
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
			<?php else : ?>
				<tr>
					<td colspan="2">
						<?php echo JText::_('Currently there are no Newsletter Templates.'); ?> 
						<a onclick="javascript:submitbutton('add')" href="#"><?php echo JText::_('Click here to create a new one!'); ?></a>
					</td>
				</tr>
			<?php endif; ?>
		</tbody>
	</table>
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="add" />
	<input type="hidden" name="boxchecked" value="0" />
</form>	