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

$this->filters['sort'] = '';
?>
<div id="content-header">
	<h2><?php echo $this->title; ?></h2>
</div><!-- / #content-header -->
<div id="content-header-extra">
	<ul id="useroptions">
		<li class="last">
			<a class="icon-tag tag btn" href="<?php echo JRoute::_('index.php?option=' . $this->option); ?>">
				<?php echo JText::_('COM_TAGS_MORE_TAGS'); ?>
			</a>
		</li>
	</ul>
</div><!-- / #content-header-extra -->
<div class="clear"></div>

<form action="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=browse'); ?>" method="get">
	<div class="main section">
		<div class="aside">
			<div class="container">
			<?php /*if ($this->config->get('access-edit-tag') || $this->config->get('access-delete-tag')) { ?>
				
			<?php } else {*/ ?>
				<p>
					<?php echo JText::_('COM_TAGS_BROWSE_EXPLANATION'); ?>
				</p>
				<p class="help">
					<strong><?php echo JText::_('COM_TAGS_WHATS_AN_ALIAS'); ?></strong>
					<br /><?php echo JText::_('COM_TAGS_ALIAS_EXPLANATION'); ?>
				</p>
			<?php /*} 
				<p class="info">
					<?php echo JText::_('COM_TAGS_BROWSE_TAGGED_NOTE'); ?>
				</p> */ ?>
			</div><!-- / .container -->
		</div><!-- / .aside -->
		<div class="subject">

			<div class="container data-entry">
				<input class="entry-search-submit" type="submit" value="<?php echo JText::_('COM_TAGS_SEARCH'); ?>" />
				<fieldset class="entry-search">
					<label for="entry-search-text">
						<?php echo JText::_('COM_TAGS_SEARCH_TAGS'); ?>
					</label>
					<input type="text" name="search" id="entry-search-text" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo JText::_('COM_TAGS_SEARCH_ENTER_TAGS'); ?>" />
				</fieldset>
			</div><!-- / .container -->

			<div class="container">
				<ul class="entries-menu">
					<li>
						<?php
							$cls = ($this->filters['sortby'] == 'total') ? ' class="active"' : '';
							$url = JRoute::_('index.php?option='.$this->option.'&task=browse&sortby=total&search='.urlencode($this->filters['search']).'&limit='.JRequest::getVar("limit", 25).'&limitstart='.JRequest::getVar("limitstart", 0)); 
						?>
						<a <?php echo $cls; ?> href="<?php echo $url; ?>" title="<?php echo JText::_('COM_TAGS_BROWSE_SORT_POPULARITY_TITLE'); ?>">
							<?php echo JText::_('COM_TAGS_BROWSE_SORT_POPULARITY'); ?>
						</a>
					</li>
					<li> 
						<?php
							$cls = ($this->filters['sortby'] == '' || $this->filters['sortby'] == 'raw_tag') ? ' class="active"' : '';
							$url = JRoute::_('index.php?option='.$this->option.'&task=browse&sortby=raw_tag&search='.urlencode($this->filters['search']).'&limit='.JRequest::getVar("limit", 25).'&limitstart='.JRequest::getVar("limitstart", 0));
						?>
						<a<?php echo $cls; ?> href="<?php echo $url; ?>" title="<?php echo JText::_('COM_TAGS_BROWSE_SORT_ALPHA_TITLE'); ?>">
							<?php echo JText::_('COM_TAGS_BROWSE_SORT_ALPHA'); ?>
						</a>
					</li>
				</ul>

				<table class="entries" id="taglist">
					<?php
					if (!$this->filters['limit'])
					{
						$this->filters['limit'] = $this->total;
					}
					$s = ($this->total > 0) ? $this->filters['start']+1 : $this->filters['start'];
					$e = ($this->total > ($this->filters['start'] + $this->filters['limit'])) ? ($this->filters['start'] + $this->filters['limit']) : $this->total;
					//$e = ($this->filters['limit'] > $this->total) ? $this->filters['start'] + $this->filters['limit'] : $this->filters['start'] + $this->filters['limit'];
					?>
					<thead>
						<tr>
						<?php if ($this->config->get('access-edit-tag') || $this->config->get('access-delete-tag')) { ?>
							<th colspan="2"><?php echo JText::_('COM_TAGS_COL_ACTION'); ?></th>
						<?php } ?>
							<th>
								<?php
								if ($this->filters['search'] != '') {
									echo JText::sprintf('COM_TAGS_BROWSE_SEARCH_FOR_IN', $this->escape($this->filters['search']), JText::_('COM_TAGS'));
								} else {
									echo JText::_('COM_TAGS');
								}
								?>
								<span>(<?php echo $s . '-' . $e; ?> of <?php echo $this->total; ?>)</span>
							</th>
						<?php //if ($this->config->get('access-edit-tag') || $this->config->get('access-delete-tag')) { ?>
							<th><?php echo JText::_('COM_TAGS_COL_ALIAS'); ?></th>
						<?php /*} 
							<th><?php echo JText::_('COM_TAGS_COL_NUMBER_TAGGED'); ?></th>
							*/ ?>
						</tr>
					</thead>
					<tbody>
				<?php
				if ($this->rows->total()) 
				{
					$cls = 'even';
					foreach ($this->rows as $row)
					{
						$cls = ($cls == 'even' ? 'odd' : 'even');
				?>
						<tr class="<?php echo $cls . ($row->get('admin') ? ' admin' : ''); ?>">
					<?php if ($this->config->get('access-edit-tag') || $this->config->get('access-delete-tag')) { ?>
							<td>
						<?php if ($this->config->get('access-delete-tag')) { ?>
								<a class="icon-delete delete delete-tag" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=delete&id[]=' . $row->get('id') . '&search=' . urlencode($this->filters['search']) . '&sortby=' . $this->filters['sortby'] . '&limit=' . $this->filters['limit'] . '&limitstart=' . $this->filters['start']); ?>">
									<?php echo JText::_('COM_TAGS_DELETE_TAG'); ?>
								</a>
						<?php } ?>
							</td>
							<td>
						<?php if ($this->config->get('access-edit-tag')) { ?>
								<a class="icon-edit edit" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=edit&id=' . $row->get('id') . '&search=' . urlencode($this->filters['search']) . '&sortby=' . $this->filters['sortby'] . '&limit=' . $this->filters['limit'] . '&limitstart=' . $this->filters['start']); ?>" title="<?php echo JText::_('COM_TAGS_EDIT_TAG'); ?> &quot;<?php echo $this->escape(stripslashes($row->get('raw_tag'))); ?>&quot;">
									<?php echo JText::_('COM_TAGS_EDIT'); ?>
								</a>
						<?php } ?>
							</td>
					<?php } ?>
							<td>
								<a class="entry-title" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&tag=' . $row->get('tag')); ?>">
									<?php echo $this->escape(stripslashes($row->get('raw_tag'))); ?>
								</a>
							</td>
					<?php //if ($this->config->get('access-edit-tag') || $this->config->get('access-delete-tag')) { ?>
							<td>
								<?php echo $row->get('substitutes') ? $this->escape(stripslashes($row->substitutes('string'))) : '<span>' . JText::_('COM_TAGS_NONE') . '</span>'; ?>
							</td>
					<?php //}
							/* <td>
								<?php echo $this->escape($row->get('total')); ?>
							</td> */ ?>
						</tr>
				<?php
					}
				}
				?>
					</tbody>
				</table>
				<?php
					$this->pageNav->setAdditionalUrlParam('search', $this->filters['search']);
					$this->pageNav->setAdditionalUrlParam('sortby', $this->filters['sortby']);
					echo $this->pageNav->getListFooter();
				?>
				<div class="clearfix"></div>
				<input type="hidden" name="sortby" value="<?php echo $this->escape($this->filters['sortby']); ?>" />
			</div><!-- / .container -->
		</div><!-- / .main subject -->
		<div class="clear"></div>
	</div><!-- / .main section -->
</form>
