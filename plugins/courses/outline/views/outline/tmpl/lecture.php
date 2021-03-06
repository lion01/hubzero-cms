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
error_reporting(E_ALL);
	@ini_set('display_errors', '1');
$juser = JFactory::getUser();

ximport('Hubzero_User_Profile');
ximport('Hubzero_User_Profile_Helper');

$units = $this->course->offering()->units();
$unit = $this->course->offering()->unit($this->unit);

if (!$unit)
{
	JError::raiseError(404, JText::_('uh-oh'));
}

$aggroups = $unit->assetgroups();

$lecture = $unit->assetgroup($this->group);
if (!$lecture)
{
	JError::raiseError(404, JText::_('uh-oh'));
}

$base    = 'index.php?option=' . $this->option . '&controller=' . $this->controller . '&gid=' . $this->course->get('alias') . '&offering=' . $this->course->offering()->get('alias') . ($this->course->offering()->section()->get('alias') != '__default' ? ':' . $this->course->offering()->section()->get('alias') : '') . '&active=outline';
$altBase = 'index.php?option=' . $this->option . '&controller=' . $this->controller . '&gid=' . $this->course->get('alias') . '&offering=' . $this->course->offering()->get('alias') . ($this->course->offering()->section()->get('alias') != '__default' ? ':' . $this->course->offering()->section()->get('alias') : '');
$current = $unit->assetgroups()->key();

if (!$this->course->offering()->access('view')) { ?>
	<p class="info"><?php echo JText::_('Access to the "Syllabus" section of this course is restricted to members only. You must be a member to view the content.'); ?></p>
<?php } else { ?>

	<?php if ($this->course->offering()->access('manage')) { ?>
		<?php if (!$lecture->isPublished()) { ?>
		<div class="asset-status unpublished">
			<span><?php echo JText::_('This lecture is <strong>unpublished</strong>.'); ?></span>
		</div>
		<?php } ?>
		<?php if ($lecture->isDraft()) { ?>
		<div class="asset-status draft">
			<span><?php echo JText::_('This lecture is in <strong>draft</strong> mode.'); ?></span>
		</div>
		<?php } ?>
		<?php if ($lecture->isPublished() && !$lecture->isAvailable()) { ?>
		<div class="asset-status pending">
			<span><?php echo JText::sprintf('This lecture is <strong>scheduled</strong> to be available at %s.', $lecture->get('publish_up')); ?></span>
		</div>
		<?php } ?>
	<?php } ?>

	<div class="video container">
		<div class="video-wrap">
			<div class="video-player-wrap">
			<?php
			$used = 0;
			if ($lecture->assets()->total())
			{
				// Render video
				foreach ($lecture->assets() as $a)
				{
					if ($a->get('type') == 'video' && ($a->isPublished() || (!$a->isPublished() && $this->course->offering()->access('manage'))))
					{
						// Prefer published assets
						if ($used)
						{
							if (!$used_asset->isPublished() && $a->isPublished())
							{
								$used       = $a->get('id');
								$used_title = $a->get('title');
								$used_asset = $a;
							}
						}
						else
						{
							$used       = $a->get('id');
							$used_title = $a->get('title');
							$used_asset = $a;
						}
					}
				}

				if ($used)
				{
					echo $used_asset->render($this->course);

					if ($this->course->offering()->access('manage')) 
					{ 
						?>
						<?php if (!$used_asset->isPublished()) { ?>
						<div class="asset-status unpublished">
							<span><?php echo JText::_('This asset is <strong>unpublished</strong>.'); ?></span>
						</div>
						<?php } ?>
						<?php if ($used_asset->isDraft()) { ?>
						<div class="asset-status draft">
							<span><?php echo JText::_('This asset is in <strong>draft</strong> mode.'); ?></span>
						</div>
						<?php } ?>
						<?php if ($used_asset->isPublished() && !$used_asset->isAvailable()) { ?>
						<div class="asset-status pending">
							<span><?php echo JText::sprintf('This asset is <strong>scheduled</strong> to be available at %s.', $used_asset->get('publish_up')); ?></span>
						</div>
						<?php } ?>
						<?php 
					}
				}
			}
			?>
			</div><!-- / .video-player-wrap -->
			<div class="video-meta">
				<h3>
					<?php if (trim($lecture->get('title')) !== '--') : ?>
						<?php echo $lecture->get('title'); ?>
					<?php else : ?>
						<?php echo $used_title; ?>
					<?php endif; ?>
				</h3>

				<ul class="lecture-assets">
					<?php
					$exams = array();
					// Are there any assets?
					if ($lecture->assets()->total())
					{
						// Loop through the assets
						foreach ($lecture->assets() as $a)
						{
							// Was this asset already used elsewhere on the page?
							// This should generally only happen with the video asset
							if ($a->get('id') == $used)
							{
								continue;
							}
							if (!$this->course->offering()->access('manage'))
							{
								if (!$a->isPublished())
								{
									continue;
								}
							}
							$href = JRoute::_($altBase . '&asset=' . $a->get('id'));
							/*if ($a->get('type') == 'video')
							{
								$href = JRoute::_($base . '&unit=' . $unit->get('alias') . '&b=' . $lecture->get('alias'));
							}*/
							$cls = 'download';
							if ($a->get('type') == 'exam')
							{
								$cls = 'edit';
								$exams[] = '<a class="' . $cls . ' btn" href="' . $href . '" target="_blank">' . $this->escape(stripslashes($a->get('title'))) . '</a>';
							}
							else
							{
								if ($a->get('type') == 'link')
								{
									$cls = 'link';
								}
								echo '<li><a class="' . $cls . '" href="' . $href . '" target="_blank">' . $this->escape(stripslashes($a->get('title'))) . '</a></li>';
							}
						}
					}
					else
					{
						echo '<li><small>' . JText::_('COURSES_NO_ASSETS_FOR_GROUPING') . '</small></li>';
					}
					?>
				</ul>
			</div>

			<p class="lecture-nav">
			<?php 
				$lecture->key($current);

				$found = false;

				if (!$lecture->isFirst()) 
				{
					$found = false;
					// Find the previous lecture
					$ky = $lecture->key();
					for ($ky; $ky >= 0; $ky--)
					{
						$lecture->key($ky);
						$prev = $lecture->sibling('prev');
						if ($prev && $prev->isPublished() && $prev->assets()->total() > 0) 
						{
							$found = true;
							?>
							<a class="icon-prev prev btn" href="<?php echo JRoute::_($base . '&unit=' . $unit->get('alias') . '&b=' . $lecture->sibling('prev')->get('alias')); ?>">
								<?php echo JText::_('Prev'); ?>
							</a>
							<?php
							break;
						}
					}
				}

				if (!$found) { ?>
				<span class="icon-prev disabled prev btn">
					<?php echo JText::_('Prev'); ?>
				</span>
				<?php }

				$gAlias = '';
				$key = $aggroups->key();
				// If NOT the last assetgroup
				if (!$unit->assetgroups()->isLast())
				{
					foreach ($unit->assetgroups() as $k => $assetgroup)
					{
						if ($k <= $current)
						{
							continue;
						}

						if ($assetgroup->isPublished()) 
						{
							$gAlias = $assetgroup->get('alias');
							break;
						}
					}
				}

				if (!$gAlias) { ?>
				<span class="icon-next disabled next opposite btn">
					<?php echo JText::_('Next'); ?>
				</span>
				<?php } else { ?>
				<a class="icon-next next opposite btn" href="<?php echo JRoute::_($base . '&unit=' . $unit->get('alias') . '&b=' . $gAlias); ?>">
					<?php echo JText::_('Next'); ?>
				</a>
				<?php }

				if (count($exams) > 0) 
				{
					echo implode("\n", $exams);
				}
			?>
			</p>

		<?php if ($lecture->get('description')) { ?>
			<p class="lecture-description">
				<?php echo $this->escape(stripslashes($lecture->get('description'))); ?>
			</p>
		<?php } ?>
		</div><!-- / .video-wrap -->
	</div><!-- / .video container -->

	<?php
		// Trigger event
		$dispatcher = JDispatcher::getInstance();
		$results = $dispatcher->trigger('onCourseAfterLecture', array(
			$this->course,
			$unit,
			$lecture
		));
		// Output results
		echo implode("\n", $results);
	?>
<?php } ?>