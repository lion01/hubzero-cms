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
?>
<div id="content-header" class="full">
	<h2><?php echo $this->title; ?></h2>
</div>

<div id="introduction" class="section">
	<div class="aside">
		<h3><?php echo JText::_('COM_TAGS_QUESTIONS'); ?></h3>
		<ul>
			<li>
				<a class="popup" href="<?php echo JRoute::_('index.php?option=com_help&component=tags&page=index'); ?>">
					<?php echo JText::_('Need Help?'); ?>
				</a>
			</li>
		</ul>
	</div><!-- / .aside -->
	<div class="subject">
		<div class="two columns first">
			<h3><?php echo JText::_('COM_TAGS_WHAT_ARE_TAGS'); ?></h3>
			<p><?php echo JText::_('COM_TAGS_WHAT_ARE_TAGS_EXPLANATION'); ?></p>
		</div>
		<div class="two columns second">
			<h3><?php echo JText::_('COM_TAGS_HOW_DO_TAGS_WORK'); ?></h3>
			<p><?php echo JText::_('COM_TAGS_HOW_DO_TAGS_WORK_EXPLANATION'); ?></p>
		</div>
		<div class="clear"></div>
	</div><!-- / .subject -->
	<div class="clear"></div>
</div><!-- / #introduction.section -->

<div class="section">
	
	<div class="four columns first">
		<h2><?php echo JText::_('COM_TAGS_FIND_CONTENT_WITH_TAG'); ?></h2>
	</div><!-- / .four columns first -->
	<div class="four columns second third fourth">
		<div class="two columns first">
			<form action="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=view'); ?>" method="get" class="search">
				<fieldset>
					<p>
						<label for="actags">Enter tags:</label>
						<?php 
						JPluginHelper::importPlugin('hubzero');
						$dispatcher =& JDispatcher::getInstance();
						$tf = $dispatcher->trigger('onGetMultiEntry', array(array('tags', 'tag', 'actags','','')));

						if (count($tf) > 0) {
							echo $tf[0];
						} else { ?>
							<input type="text" name="tag" id="actags" value="" />
						<?php } ?>
						<input type="submit" value="<?php echo JText::_('COM_TAGS_SEARCH'); ?>" />
					</p>
				</fieldset>
			</form>
		</div><!-- / .two columns first -->
		<div class="two columns second">
			<div>
				<p>Using more than one tag will perform an "AND" operation. For example, if you enter "circuits" and "devices", it will find all content tagged with <strong>both</strong> tags.</p>
			</div><!-- / .browse -->
		</div><!-- / .two columns second -->
	</div><!-- / .four columns second third fourth -->
	<div class="clear"></div>

	<div class="four columns first">
		<h2><?php echo JText::_('COM_TAGS_RECENTLY_USED'); ?></h2>
	</div><!-- / .four columns first -->
	<div class="four columns second third fourth">
		<div class="block">
		<?php
		$cloud = $this->cloud->render('html', array(
			'limit'    => 25,
			'admin'    => 0,
			'sort'     => 'taggedon',
			'sort_Dir' => 'DESC',
			'by'       => 'user'
		), true);
		if ($cloud)
		{
			echo $cloud;
		}
		else 
		{
			echo '<p class="warning">' . JText::_('COM_TAGS_NO_TAGS') . '</p>' . "\n";
		}
		?>
		</div><!-- / .block -->
	</div><!-- / .four columns second third fourth -->
	<div class="clear"></div>

	<div class="four columns first">
		<h2><?php echo JText::_('COM_TAGS_TOP_100'); ?></h2>
	</div><!-- / .four columns first -->
	<div class="four columns second third fourth">
		<div class="block">
		<?php
		$cloud = $this->cloud->render('html', array(
			'limit'    => 100,
			'admin'    => 0,
			'sort'     => 'total',
			'sort_Dir' => 'DESC',
			'by'       => 'user'
		), true);
		if ($cloud)
		{
			echo $cloud;
		}
		else 
		{
			echo '<p class="warning">' . JText::_('COM_TAGS_NO_TAGS') . '</p>' . "\n";
		}
		?>
		</div><!-- / .block -->
	</div><!-- / .four columns second third fourth -->
	<div class="clear"></div>

	<div class="four columns first">
		<h2><?php echo JText::_('COM_TAGS_FIND_A_TAG'); ?></h2>
	</div><!-- / .four columns first -->
	<div class="four columns second third fourth">
		<div class="two columns first">
			<form action="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=browse'); ?>" method="get" class="search">
				<fieldset>
					<p>
						<label for="tsearch">Keyword or phrase:</label>
						<input type="text" name="search" id="tsearch" value="" />
						<input type="submit" value="<?php echo JText::_('COM_TAGS_SEARCH'); ?>" />
					</p>
				</fieldset>
			</form>
		</div><!-- / .two columns first -->
		<div class="two columns second">
			<div class="browse">
				<p><a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=browse'); ?>"><?php echo JText::_('COM_TAGS_BROWSE_LIST'); ?></a></p>
			</div><!-- / .browse -->
		</div><!-- / .two columns second -->
	</div><!-- / .four columns second third fourth -->
	<div class="clear"></div>

</div><!-- / .section -->
