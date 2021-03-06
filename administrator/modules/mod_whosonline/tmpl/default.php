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

defined( '_JEXEC' ) or die( 'Restricted access' );

//get whos online summary
$siteUserCount  = 0;
$adminUserCount = 0;
foreach ($this->rows as $row)
{
	if ($row->client_id == 0)
	{
		$siteUserCount++;
	}
	else
	{
		$adminUserCount++;
	}
}
?>

<form method="post" action="index.php?option=com_users">
	<table class="whosonline-summary">
		<thead>
			<tr>
				<th width="50%" scope="col"><?php echo JText::_( 'Site' ); ?></th>
				<th scope="col"><?php echo JText::_( 'Administrator' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class="front-end"><?php echo $siteUserCount; ?></td>
				<td class="back-end"><?php echo $adminUserCount; ?></td>
			</tr>
		</tbody>
	</table>
	
	<table class="adminlist whosonline-list">
		<thead>
			<tr>
				<td class="title">
					<strong><?php echo JText::_( 'User' ); ?></strong>
				</td>
				<td class="title">
					<strong><?php echo JText::_( 'Location' ); ?></strong>
				</td>
				<td class="title">
					<strong><?php echo JText::_( 'Last Activity' ); ?></strong>
				</td>
			</tr>
		</thead>
		<tbody>
			<?php if(count($this->rows) > 0) : ?>
				<?php foreach ($this->rows as $k => $row) : ?>
					<?php if (($k+1) <= $this->params->get('display_limit', 25)) : ?>
						<tr>
							<td>
								<?php
									//are we authorized to edit users
									$editAuthorized = $this->juser->authorize( 'com_users', 'manage' );
								
									//get user object
									$juser = JUser::getInstance( $row->username );
								
									//display link if we are authorized
									if ($editAuthorized)
									{
										$editLink = 'index.php?option=com_users&amp;task=edit&amp;cid[]='. $row->userid;
										echo '<a href="' . $editLink . '" title="' . JText::_( 'Edit User' ) . '">' . $juser->get('name') . ' [' . $juser->get('username') . ']' . '</a>';
									}
									else
									{
										echo $juser->get('name') . ' [' . $juser->get('username') . ']';
									}
								?>
							</td>
							<td>
								<?php
									$clientInfo = JApplicationHelper::getClientInfo( $row->client_id );
									echo ucfirst( $clientInfo->name );
								?>
							</td>
							<td>
								<?php echo JText::sprintf( '%.1f hours ago', (time() - $row->time)/3600.0 ); ?>
							</td>
						</tr>
					<?php endif; ?>
				<?php endforeach; ?>
				<tr>
					<td colspan="4" class="view-all">
						<a href="index.php?option=com_members&amp;controller=whosonline">&lsaquo; View all &rsaquo;</a>
					</td>
				</tr>
			<?php else : ?>
				<tr>
					<td colspan="4">
						<?php echo JText::_('Currently there are no users online.'); ?>
					</td>
				</tr>
			<?php endif; ?>
		</tbody>
	</table>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="client" value="" />
	<input type="hidden" name="cid[]" id="cid_value" value="" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
