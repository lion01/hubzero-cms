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

$text = ($this->task == 'edit' ? JText::_('Edit Section') : JText::_('New Section'));

$canDo = CoursesHelper::getActions();

JToolBarHelper::title(JText::_('COM_COURSES').': ' . $text, 'courses.png');
if ($canDo->get('core.edit')) 
{
	JToolBarHelper::save();
}
JToolBarHelper::cancel();

jimport('joomla.html.editor');

JHtml::_('behavior.switcher');

$editor = JEditor::getInstance();

$base = str_replace('/administrator', '', rtrim(JURI::getInstance()->base(true), '/'));

$document = JFactory::getDocument();
$document->addStyleSheet('components' . DS . $this->option . DS . 'assets' . DS . 'css' . DS . 'classic.css');

$course_id = 0;
?>
<script type="text/javascript">
function submitbutton(pressbutton) 
{
	var form = document.adminForm;
	
	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}
	
	// form field validation
	if ($('field-alias').value == '') {
		alert('<?php echo JText::_('COM_COURSES_ERROR_MISSING_INFORMATION'); ?>');
	} else if ($('field-title').value == '') {
		alert('<?php echo JText::_('COM_COURSES_ERROR_MISSING_INFORMATION'); ?>');
	} else {
		submitform(pressbutton);
	}
}
document.switcher = null;
window.addEvent('domready', function(){
	toggler = document.id('submenu');
	element = document.id('section-document');
	if (element) {
		document.switcher = new JSwitcher(toggler, element, {cookieName: toggler.getProperty('class')});
	}
});
</script>
<?php if ($this->getError()) { ?>
	<p class="error"><?php echo implode('<br />', $this->getErrors()); ?></p>
<?php } ?>
<form action="index.php" method="post" name="adminForm" id="item-form" enctype="multipart/form-data">
	<nav role="navigation" class="sub-navigation">
		<div id="submenu-box">
			<div class="submenu-box">
				<div class="submenu-pad">
					<ul id="submenu" class="coursesection">
						<li><a href="#" onclick="return false;" id="details" class="active">Details</a></li>
						<li><a href="#" onclick="return false;" id="managers">Managers</a></li>
						<li><a href="#" onclick="return false;" id="datetime">Dates/Times</a></li>
						<li><a href="#" onclick="return false;" id="badge">Badge</a></li>
					</ul>
					<div class="clr"></div>
				</div>
			</div>
			<div class="clr"></div>
		</div>
	</nav><!-- / .sub-navigation -->

	<div id="section-document">
		<div id="page-details" class="tab">

			<div class="col width-50 fltlft">
				<fieldset class="adminform">
					<legend><span><?php echo JText::_('COM_COURSES_DETAILS'); ?></span></legend>

					<input type="hidden" name="fields[id]" value="<?php echo $this->row->get('id'); ?>" />
					<input type="hidden" name="offering" value="<?php echo $this->row->get('offering_id'); ?>" />
					<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
					<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
					<input type="hidden" name="task" value="save" />

					<table class="admintable">
						<tbody>
							<tr>
								<th class="key"><label for="offering_id"><?php echo JText::_('Offering'); ?>:</label></th>
								<td>
									<select name="fields[offering_id]" id="offering_id">
										<option value="-1"><?php echo JText::_('Select offering...'); ?></option>
							<?php
								require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'courses.php');
								$model = CoursesModelCourses::getInstance();
								if ($model->courses()->total() > 0)
								{
									foreach ($model->courses() as $course)
									{
							?>
										<optgroup label="<?php echo $this->escape(stripslashes($course->get('alias'))); ?>">
							<?php
										$j = 0;
										foreach ($course->offerings() as $i => $offering)
										{
											if ($offering->get('id') == $this->row->get('offering_id')) 
											{
												$course_id = $offering->get('course_id');
											}
							?>
											<option value="<?php echo $this->escape(stripslashes($offering->get('id'))); ?>"<?php if ($offering->get('id') == $this->row->get('offering_id')) { echo ' selected="selected"'; } ?>><?php echo $this->escape(stripslashes($offering->get('alias'))); ?></option>
							<?php
										}
							?>
										</optgroup>
							<?php 
									}
								}
							?>
									</select>
								</td>
							</tr>
							<tr>
								<th class="key"><label for="field-alias"><?php echo JText::_('Alias'); ?>:</label></th>
								<td>
									<input type="text" name="fields[alias]" id="field-alias"<?php if ($this->row->get('alias') == '__default') { echo ' disabled="disabled"'; } ?> value="<?php echo $this->escape(stripslashes($this->row->get('alias'))); ?>" size="50" />
									<?php if ($this->row->get('alias') == '__default') { ?><span class="hint">Offerings must have a "__default" section.</span><?php } ?>
								</td>
							</tr>
							<tr>
								<th class="key"><label for="field-title"><?php echo JText::_('COM_COURSES_TITLE'); ?>:</label></th>
								<td><input type="text" name="fields[title]" id="field-title" value="<?php echo $this->escape(stripslashes($this->row->get('title'))); ?>" size="50" /></td>
							</tr>
							<tr>
								<th class="key"><label for="field-enrollment"><?php echo JText::_('Enrollment'); ?>:</label></th>
								<td>
									<select name="fields[enrollment]" id="field-enrollment">
										<option value="0"<?php if ($this->row->get('enrollment', 2) == 0) { echo ' selected="selected"'; } ?>><?php echo JText::_('Open (anyone can join)'); ?></option>
										<option value="1"<?php if ($this->row->get('enrollment', 2) == 1) { echo ' selected="selected"'; } ?>><?php echo JText::_('Restricted (coupon code is required)'); ?></option>
										<option value="2"<?php if ($this->row->get('enrollment', 2) == 2) { echo ' selected="selected"'; } ?>><?php echo JText::_('Closed (no new enrollment)'); ?></option>
									</select>
								</td>
							</tr>
						</tbody>
					</table>
				</fieldset>

				<fieldset class="adminform">
					<legend><span><?php echo JText::_('Publishing'); ?></span></legend>
					
					<table class="admintable">
						<tbody>
							<tr>
								<th class="key"><label for="publish_up">Publish up:</label></th>
								<td>
									<?php echo JHTML::_('calendar', $this->row->get('publish_up'), 'fields[publish_up]', 'publish_up', "%Y-%m-%d", array('class' => 'calendar-field inputbox')); ?>
									<span class="hint">When the section will become available for enrollment</span>
								</td>
							</tr>
							<tr>
								<th class="key"><label for="start_date">Starts:</label></th>
								<td>
									<?php echo JHTML::_('calendar', $this->row->get('start_date'), 'fields[start_date]', 'start_date', "%Y-%m-%d", array('class' => 'calendar-field inputbox')); ?>
									<span class="hint">When the section starts a live offering</span>
								</td>
							</tr>
							<tr>
								<th class="key"><label for="end date">Finishes:</label></th>
								<td>
									<?php echo JHTML::_('calendar', $this->row->get('end_date'), 'fields[end_date]', 'end_date', "%Y-%m-%d", array('class' => 'calendar-field inputbox')); ?>
									<span class="hint">When the live offering ends</span>
								</td>
							</tr>
							<tr>
								<th class="key"><label for="publish_down">Publish down:</label></th>
								<td>
									<?php echo JHTML::_('calendar', $this->row->get('publish_down'), 'fields[publish_down]', 'publish_down', "%Y-%m-%d", array('class' => 'calendar-field inputbox')); ?>
									<span class="hint">When section will close (materials no longer accessible)</span>
								</td>
							</tr>
						</tbody>
					</table>
				</fieldset>
			</div>
			<div class="col width-50 fltrt">
				<table class="meta" summary="<?php echo JText::_('COM_COURSES_META_SUMMARY'); ?>">
					<tbody>
						<tr>
							<th><?php echo JText::_('Course ID'); ?></th>
							<td colspan="3"><?php echo $this->escape($course_id); ?></td>
						</tr>
						<tr>
							<th><?php echo JText::_('Offering ID'); ?></th>
							<td colspan="3"><?php echo $this->escape($this->row->get('offering_id')); ?></td>
						</tr>
						<tr>
							<th><?php echo JText::_('Section ID'); ?></th>
							<td colspan="3"><?php echo $this->escape($this->row->get('id')); ?></td>
						</tr>
					<?php if ($this->row->get('created')) { ?>
						<tr>
							<th><?php echo JText::_('Created'); ?></th>
							<td>
								<?php echo $this->escape($this->row->get('created')); ?>
							</td>
						</tr>
						<?php if ($this->row->get('created_by')) { ?>
						<tr>
							<th><?php echo JText::_('Creator'); ?></th>
							<td><?php 
								$creator = JUser::getInstance($this->row->get('created_by'));
								echo $this->escape(stripslashes($creator->get('name'))); ?>
							</td>
						</tr>
						<?php } ?>
					<?php } ?>
					</tbody>
				</table>

				<?php
					JPluginHelper::importPlugin('courses');
					$dispatcher = JDispatcher::getInstance();

					if ($plugins = $dispatcher->trigger('onSectionEdit'))
					{
						$pth = false;
						$paramsClass = 'JParameter';
						if (version_compare(JVERSION, '1.6', 'ge'))
						{
							$pth = true;
							//$paramsClass = 'JRegistry';
						}

						$data = $this->row->get('params');

						foreach ($plugins as $plugin)
						{
							$param = new $paramsClass(
								(is_object($data) ? $data->toString() : $data),
								JPATH_ROOT . DS . 'plugins' . DS . 'courses' . DS . $plugin['name'] . ($pth ? DS . $plugin['name'] : '') . '.xml'
							);
							$out = $param->render('params', 'onSectionEdit');
							if (!$out) 
							{
								continue;
							}
							?>
							<fieldset class="adminform eventparams" id="params-<?php echo $plugin['name']; ?>">
								<legend><?php echo JText::sprintf('%s Parameters', $plugin['title']); ?></legend>
								<?php echo $out; ?>
							</fieldset>
							<?php
						}
					}
				?>
			</div>
			<div class="clr"></div>
		</div>

		<div id="page-managers" class="tab">
			<fieldset class="adminform">
				<legend><span><?php echo JText::_('Managers'); ?></span></legend>
			<?php if ($this->row->get('id')) { ?>
				<iframe width="100%" height="500" name="managers" id="managers" frameborder="0" src="index.php?option=<?php echo $this->option; ?>&amp;controller=supervisors&amp;tmpl=component&amp;offering=<?php echo $this->row->get('offering_id'); ?>&amp;section=<?php echo $this->row->get('id'); ?>"></iframe>
			<?php } else { ?>
				<p><?php echo JText::_('Section must be saved before managers can be added.'); ?></p>
			<?php } ?>
			</fieldset>
		</div>

		<div id="page-datetime" class="tab">
		<?php if ($this->offering->units()->total() > 0) { ?>
			<div class="col width-100">
				<?php if (!$this->row->exists() && $this->row->get('alias') != '__default') { ?>
				<p class="info"><?php echo JText::_('Dates and times are initially inherited from the default section for this offering.'); ?></p>
				<?php } ?>
				<script src="<?php echo $base; ?>/media/system/js/jquery.js"></script>
				<script src="<?php echo $base; ?>/media/system/js/jquery.ui.js"></script>
				<script src="<?php echo $base; ?>/media/system/js/jquery.noconflict.js"></script>
				<script src="components/com_courses/assets/js/jquery-ui-timepicker-addon.js"></script>
				<?php 
				jimport('joomla.html.pane');
				$tabs = JPane::getInstance('sliders');

				echo $tabs->startPane("content-pane"); 
				$this->offering->section($this->row->get('alias', '__default'));

					$i = 0;
					foreach ($this->offering->units(array(), true) as $unit) 
					{
						echo $tabs->startPanel(stripslashes($unit->get('title')), stripslashes($unit->get('alias')));
				?>
							<input type="hidden" name="dates[<?php echo $i; ?>][id]" value="<?php echo $this->row->date('unit', $unit->get('id'))->get('id'); ?>" />
							<input type="hidden" name="dates[<?php echo $i; ?>][scope]" value="unit" />
							<input type="hidden" name="dates[<?php echo $i; ?>][scope_id]" value="<?php echo $unit->get('id'); ?>" />

							<table class="admintable section-dates" id="dates_<?php echo $i; ?>">
								<tbody>
									<tr>
										<th class="key"><label for="dates_<?php echo $i; ?>_publish_up"><?php echo JText::_('from'); ?></label></th>
										<td>
											<?php $tm = ($unit->get('publish_up') ? $unit->get('publish_up') : $this->row->date('unit', $unit->get('id'))->get('publish_up')); ?>
											<input type="text" name="dates[<?php echo $i; ?>][publish_up]" id="dates_<?php echo $i; ?>_publish_up" class="datetime-field" value="<?php echo $tm == '0000-00-00 00:00:00' ? '' : $tm; ?>" />
										</td>
										<th class="key"><label for="dates_<?php echo $i; ?>_publish_up"><?php echo JText::_('to'); ?></label></th>
										<td>
											<?php $tm = ($unit->get('publish_down') ? $unit->get('publish_down') : $this->row->date('unit', $unit->get('id'))->get('publish_down')); ?>
											<input type="text" name="dates[<?php echo $i; ?>][publish_down]" id="dates_<?php echo $i; ?>_publish_down" class="datetime-field" value="<?php echo $tm == '0000-00-00 00:00:00' ? '' : $tm; ?>" />
										</td>
										<td>
											Unless specified below, these values will be inherited by all descendants.
										</td>
									</tr>
								</tbody>
							</table>
							<table class="admintable section-dates" id="dates_<?php echo $i; ?>">
								<tbody>
							<?php
							// Loop through the asset group types
							$z = 0;
							foreach ($unit->assetgroups() as $agt)
							{
								$agt->set('publish_up', $this->row->date('asset_group', $agt->get('id'))->get('publish_up'));
								$agt->set('publish_down', $this->row->date('asset_group', $agt->get('id'))->get('publish_down'));
								
								if ($agt->get('publish_up') == '0000-00-00 00:00:00')
								{
									$agt->set('publish_up', $unit->get('publish_up'));
								}
								if ($agt->get('publish_down') == '0000-00-00 00:00:00')
								{
									$agt->set('publish_down', $unit->get('publish_down'));
								}
								?>
								
									<tr>
										<th class="key">
											&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="treenode">&#8970;</span> &nbsp; 
											<?php echo $this->escape(stripslashes($agt->get('title'))); ?>
										</th>
										<td><label for="dates_<?php echo $i; ?>_assetgroup_<?php echo $z; ?>_publish_up"><?php echo JText::_('from'); ?></label></th>
										<td>
											<input type="hidden" name="dates[<?php echo $i; ?>][asset_group][<?php echo $z; ?>][id]" value="<?php echo $this->row->date('asset_group', $agt->get('id'))->get('id'); ?>" />
											<input type="hidden" name="dates[<?php echo $i; ?>][asset_group][<?php echo $z; ?>][scope]" value="asset_group" />
											<input type="hidden" name="dates[<?php echo $i; ?>][asset_group][<?php echo $z; ?>][scope_id]" value="<?php echo $agt->get('id'); ?>" />
											<?php //echo JHTML::_('calendar', $unit->get('publish_up'), 'dates[' . $i . '][publish_up]', 'dates_' . $i . '_publish_up', "%Y-%m-%d", array('class' => 'calendar-field inputbox')); ?>
											<input type="text" name="dates[<?php echo $i; ?>][asset_group][<?php echo $z; ?>][publish_up]" id="dates_<?php echo $i; ?>_assetgroup_<?php echo $z; ?>_publish_up" class="datetime-field" value="<?php echo ($agt->get('publish_up') == $unit->get('publish_up') || $agt->get('publish_up') == '0000-00-00 00:00:00' ? '' : $agt->get('publish_up')); ?>" />
										</td>
										<td><label for="dates_<?php echo $i; ?>_assetgroup_<?php echo $z; ?>_publish_up"><?php echo JText::_('to'); ?></label></th>
										<td>
											<?php //echo JHTML::_('calendar', $unit->get('publish_down'), 'dates[' . $i . '][publish_down]', 'dates_' . $i . '_publish_down', "%Y-%m-%d", array('class' => 'calendar-field inputbox')); ?>
											<input type="text" name="dates[<?php echo $i; ?>][asset_group][<?php echo $z; ?>][publish_down]" id="dates_<?php echo $i; ?>_assetgroup_<?php echo $z; ?>_publish_down" class="datetime-field" value="<?php echo ($agt->get('publish_down') == $unit->get('publish_down') || $agt->get('publish_down') == '0000-00-00 00:00:00' ? '' : $agt->get('publish_down')); ?>" />
										</td>
									</tr>

								<?php
								//$agt->set('publish_up', $unit->get('publish_up'));
								//$agt->set('publish_down', $unit->get('publish_down'));
								

								$j = 0;
								foreach ($agt->children() as $ag)
								{
									$ag->set('publish_up', $this->row->date('asset_group', $ag->get('id'))->get('publish_up'));
									$ag->set('publish_down', $this->row->date('asset_group', $ag->get('id'))->get('publish_down'));

									if ($ag->get('publish_up') == '0000-00-00 00:00:00')
									{
										$ag->set('publish_up', $agt->get('publish_up'));
									}
									if ($ag->get('publish_down') == '0000-00-00 00:00:00')
									{
										$ag->set('publish_down', $agt->get('publish_down'));
									}
									?>
											<tr>
												<th class="key">
													&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="treenode">&#8970;</span> &nbsp; 
													<?php echo $this->escape(stripslashes($ag->get('title'))); ?>
												</th>
												<td><label for="dates_<?php echo $i; ?>_<?php echo $j; ?>_assetgroup_<?php echo $z; ?>_assetgroup_<?php echo $j; ?>_publish_up"><?php echo JText::_('from'); ?></label></th>
												<td>
													<input type="hidden" name="dates[<?php echo $i; ?>][asset_group][<?php echo $z; ?>][asset_group][<?php echo $j; ?>][id]" value="<?php echo $this->row->date('asset_group', $ag->get('id'))->get('id'); ?>" />
													<input type="hidden" name="dates[<?php echo $i; ?>][asset_group][<?php echo $z; ?>][asset_group][<?php echo $j; ?>][scope]" value="asset_group" />
													<input type="hidden" name="dates[<?php echo $i; ?>][asset_group][<?php echo $z; ?>][asset_group][<?php echo $j; ?>][scope_id]" value="<?php echo $ag->get('id'); ?>" />
													<?php //echo JHTML::_('calendar', $unit->get('publish_up'), 'dates[' . $i . '][publish_up]', 'dates_' . $i . '_publish_up', "%Y-%m-%d", array('class' => 'calendar-field inputbox')); ?>
													<input type="text" name="dates[<?php echo $i; ?>][asset_group][<?php echo $z; ?>][asset_group][<?php echo $j; ?>][publish_up]" id="dates_<?php echo $i; ?>_assetgroup_<?php echo $z; ?>_assetgroup_<?php echo $j; ?>_publish_up" class="datetime-field" value="<?php echo ($ag->get('publish_up') == $agt->get('publish_up') || $ag->get('publish_up') == '0000-00-00 00:00:00' ? '' : $ag->get('publish_up')); ?>" />
												</td>
												<td><label for="dates_<?php echo $i; ?>_<?php echo $j; ?>_assetgroup_<?php echo $z; ?>_assetgroup_<?php echo $j; ?>_publish_up"><?php echo JText::_('to'); ?></label></th>
												<td>
													<?php //echo JHTML::_('calendar', $unit->get('publish_down'), 'dates[' . $i . '][publish_down]', 'dates_' . $i . '_publish_down', "%Y-%m-%d", array('class' => 'calendar-field inputbox')); ?>
													<input type="text" name="dates[<?php echo $i; ?>][<?php echo $z; ?>][<?php echo $j; ?>][publish_down]" id="dates_<?php echo $i; ?>_assetgroup_<?php echo $z; ?>_assetgroup_<?php echo $j; ?>_publish_down" class="datetime-field" value="<?php echo ($ag->get('publish_down') == $agt->get('publish_down') || $ag->get('publish_down') == '0000-00-00 00:00:00' ? '' : $ag->get('publish_down')); ?>" />
												</td>
											</tr>
										
									<?php
									
									if ($ag->assets()->total())
									{
										$k = 0;
										foreach ($ag->assets() as $a)
										{
											$a->set('publish_up', $this->row->date('asset', $a->get('id'))->get('publish_up'));
											$a->set('publish_down', $this->row->date('asset', $a->get('id'))->get('publish_down'));
											
											if ($a->get('publish_up') == '0000-00-00 00:00:00')
											{
												$a->set('publish_up', $ag->get('publish_up'));
											}
											if ($a->get('publish_down') == '0000-00-00 00:00:00')
											{
												$a->set('publish_down', $ag->get('publish_down'));
											}
											?>
													<tr>
														<th class="key">
															&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="treenode">&#8970;</span> &nbsp; 
															<?php echo $this->escape(stripslashes($a->get('title'))); ?>
														</th>
														<td><label for="dates_<?php echo $i; ?>_assetgroup_<?php echo $z; ?>_assetgroup_<?php echo $j; ?>asset_<?php echo $k; ?>_publish_up"><?php echo JText::_('from'); ?></label></th>
														<td>
															<input type="hidden" name="dates[<?php echo $i; ?>][asset_group][<?php echo $z; ?>][asset_group][<?php echo $j; ?>][asset][<?php echo $k; ?>][id]" value="<?php echo $this->row->date('asset', $a->get('id'))->get('id'); ?>" />
															<input type="hidden" name="dates[<?php echo $i; ?>][asset_group][<?php echo $z; ?>][asset_group][<?php echo $j; ?>][asset][<?php echo $k; ?>][scope]" value="asset" />
															<input type="hidden" name="dates[<?php echo $i; ?>][asset_group][<?php echo $z; ?>][asset_group][<?php echo $j; ?>][asset][<?php echo $k; ?>][scope_id]" value="<?php echo $a->get('id'); ?>" />
															<?php //echo JHTML::_('calendar', $unit->get('publish_up'), 'dates[' . $i . '][publish_up]', 'dates_' . $i . '_publish_up', "%Y-%m-%d", array('class' => 'calendar-field inputbox')); ?>
															<input type="text" name="dates[<?php echo $i; ?>][asset_group][<?php echo $z; ?>][asset_group][<?php echo $j; ?>][asset][<?php echo $k; ?>][publish_up]" id="dates_<?php echo $i; ?>_assetgroup_<?php echo $z; ?>_assetgroup_<?php echo $j; ?>asset_<?php echo $k; ?>_publish_up" class="datetime-field" value="<?php echo ($a->get('publish_up') == $ag->get('publish_up') || $a->get('publish_up') == '0000-00-00 00:00:00' ? '' : $a->get('publish_up')); ?>" />
														</td>
														<td><label for="dates_<?php echo $i; ?>_assetgroup_<?php echo $z; ?>_assetgroup_<?php echo $j; ?>asset_<?php echo $k; ?>_publish_up"><?php echo JText::_('to'); ?></label></th>
														<td>
															<?php //echo JHTML::_('calendar', $unit->get('publish_down'), 'dates[' . $i . '][publish_down]', 'dates_' . $i . '_publish_down', "%Y-%m-%d", array('class' => 'calendar-field inputbox')); ?>
															<input type="text" name="dates[<?php echo $i; ?>][asset_group][<?php echo $z; ?>][asset_group][<?php echo $j; ?>][asset][<?php echo $k; ?>][publish_down]" id="dates_<?php echo $i; ?>_assetgroup_<?php echo $z; ?>_assetgroup_<?php echo $j; ?>asset_<?php echo $k; ?>_publish_down" class="datetime-field" value="<?php echo ($a->get('publish_down') == $ag->get('publish_down') || $a->get('publish_down') == '0000-00-00 00:00:00' ? '' : $a->get('publish_down')); ?>" />
														</td>
													</tr>

											<?php
											$k++;
										}
									}
									$j++;
								}
								if ($agt->assets()->total())
								{
									$k = 0;
									foreach ($agt->assets() as $a)
									{
										$a->set('publish_up', $this->row->date('asset', $a->get('id'))->get('publish_up'));
										$a->set('publish_down', $this->row->date('asset', $a->get('id'))->get('publish_down'));
										
										if ($a->get('publish_up') == '0000-00-00 00:00:00')
										{
											$a->set('publish_up', $agt->get('publish_up'));
										}
										if ($a->get('publish_down') == '0000-00-00 00:00:00')
										{
											$a->set('publish_down', $agt->get('publish_down'));
										}
										?>
												<tr>
													<th class="key">
														&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="treenode">&#8970;</span> &nbsp; 
														<?php echo $this->escape(stripslashes($a->get('title'))); ?>
													</th>
													<td><label for="dates_<?php echo $i; ?>_assetgroup_<?php echo $z; ?>_asset_<?php echo $k; ?>_publish_up"><?php echo JText::_('from'); ?></label></th>
													<td>
														<input type="hidden" name="dates[<?php echo $i; ?>][asset_group][<?php echo $z; ?>][asset][<?php echo $k; ?>][id]" value="<?php echo $this->row->date('asset', $a->get('id'))->get('id'); ?>" />
														<input type="hidden" name="dates[<?php echo $i; ?>][asset_group][<?php echo $z; ?>][asset][<?php echo $k; ?>][scope]" value="asset" />
														<input type="hidden" name="dates[<?php echo $i; ?>][asset_group][<?php echo $z; ?>][asset][<?php echo $k; ?>][scope_id]" value="<?php echo $a->get('id'); ?>" />
														<?php //echo JHTML::_('calendar', $unit->get('publish_up'), 'dates[' . $i . '][publish_up]', 'dates_' . $i . '_publish_up', "%Y-%m-%d", array('class' => 'calendar-field inputbox')); ?>
														<input type="text" name="dates[<?php echo $i; ?>][asset_group][<?php echo $z; ?>][asset][<?php echo $k; ?>][publish_up]" id="dates_<?php echo $i; ?>_assetgroup_<?php echo $z; ?>_asset_<?php echo $k; ?>_publish_up" class="datetime-field" value="<?php echo ($a->get('publish_up') == $agt->get('publish_up') || $a->get('publish_up') == '0000-00-00 00:00:00' ? '' : $a->get('publish_up')); ?>" />
													</td>
													<td><label for="dates_<?php echo $i; ?>_assetgroup_<?php echo $z; ?>_asset_<?php echo $k; ?>_publish_up"><?php echo JText::_('to'); ?></label></th>
													<td>
														<?php //echo JHTML::_('calendar', $unit->get('publish_down'), 'dates[' . $i . '][publish_down]', 'dates_' . $i . '_publish_down', "%Y-%m-%d", array('class' => 'calendar-field inputbox')); ?>
														<input type="text" name="dates[<?php echo $i; ?>][asset_group][<?php echo $z; ?>][asset][<?php echo $k; ?>][publish_down]" id="dates_<?php echo $i; ?>_assetgroup_<?php echo $z; ?>_asset_<?php echo $k; ?>_publish_down" class="datetime-field" value="<?php echo ($a->get('publish_down') == $agt->get('publish_down') || $a->get('publish_down') == '0000-00-00 00:00:00' ? '' : $a->get('publish_down')); ?>" />
													</td>
												</tr>

										<?php
										$k++;
									}
								}
								$z++;
							}
							if ($unit->assets()->total())
							{
								$k = 0;
								foreach ($unit->assets() as $a)
								{
									$a->set('publish_up', $this->row->date('asset', $a->get('id'))->get('publish_up'));
									$a->set('publish_down', $this->row->date('asset', $a->get('id'))->get('publish_down'));
									
									if ($a->get('publish_up') == '0000-00-00 00:00:00')
									{
										$a->set('publish_up', $unit->get('publish_up'));
									}
									if ($a->get('publish_down') == '0000-00-00 00:00:00')
									{
										$a->set('publish_down', $unit->get('publish_down'));
									}
									?>
											<tr>
												<th class="key">
													&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="treenode">&#8970;</span> &nbsp; 
													<?php echo $this->escape(stripslashes($a->get('title'))); ?>
												</th>
												<td><label for="dates_<?php echo $i; ?>_asset_<?php echo $k; ?>_publish_up"><?php echo JText::_('from'); ?></label></th>
												<td>
													<input type="hidden" name="dates[<?php echo $i; ?>][asset][<?php echo $k; ?>][id]" value="<?php echo $this->row->date('asset', $a->get('id'))->get('id'); ?>" />
													<input type="hidden" name="dates[<?php echo $i; ?>][asset][<?php echo $k; ?>][scope]" value="asset" />
													<input type="hidden" name="dates[<?php echo $i; ?>][asset][<?php echo $k; ?>][scope_id]" value="<?php echo $a->get('id'); ?>" />
													<?php //echo JHTML::_('calendar', $unit->get('publish_up'), 'dates[' . $i . '][publish_up]', 'dates_' . $i . '_publish_up', "%Y-%m-%d", array('class' => 'calendar-field inputbox')); ?>
													<input type="text" name="dates[<?php echo $i; ?>][asset][<?php echo $k; ?>][publish_up]" id="dates_<?php echo $i; ?>_asset_<?php echo $k; ?>_publish_up" class="datetime-field" value="<?php echo ($a->get('publish_up') == $unit->get('publish_up') || $a->get('publish_up') == '0000-00-00 00:00:00' ? '' : $a->get('publish_up')); ?>" />
												</td>
												<td><label for="dates_<?php echo $i; ?>_asset_<?php echo $k; ?>_publish_up"><?php echo JText::_('to'); ?></label></th>
												<td>
													<?php //echo JHTML::_('calendar', $unit->get('publish_down'), 'dates[' . $i . '][publish_down]', 'dates_' . $i . '_publish_down', "%Y-%m-%d", array('class' => 'calendar-field inputbox')); ?>
													<input type="text" name="dates[<?php echo $i; ?>][asset][<?php echo $k; ?>][publish_down]" id="dates_<?php echo $i; ?>_asset_<?php echo $k; ?>_publish_down" class="datetime-field" value="<?php echo ($a->get('publish_down') == $unit->get('publish_down') || $a->get('publish_down') == '0000-00-00 00:00:00' ? '' : $a->get('publish_down')); ?>" />
												</td>
											</tr>

									<?php
									$k++;
								}
							}
							?>
								</tbody>
							</table>
				<?php
						$i++;
						echo $tabs->endPanel();
					}
				echo $tabs->endPane();
				?>
				<!-- </fieldset> -->
				<script type="text/javascript">
				jQuery(document).ready(function(jq){
					var $ = jq;
					$('.datetime-field').datetimepicker({  
						duration: '',
						showTime: true,
						constrainInput: false,
						stepMinutes: 1,
						stepHours: 1,
						altTimeField: '',
						time24h: true,
						dateFormat: 'yy-mm-dd',
						timeFormat: 'HH:mm:00'
					});
				});
				</script>
			</div>
			<div class="clr"></div>
		<?php } else { ?>
			<p class="warning">There is currently no data associated with the offering.</p>
		<?php } ?>
		</div>
		<div id="page-badge" class="tab">
			<script type="text/javascript">
				jQuery(document).ready(function(jq){
					var $ = jq;
					if (!$('#badge-published').is(':checked')) {
						$('.badge-field-toggle').hide();
					}

					$('#badge-published').click(function(){
						$('.badge-field-toggle').toggle();
					});
				});
			</script>
			<fieldset class="adminform">
				<legend><span><?php echo JText::_('Badge'); ?></span></legend>
				<?php if (!$this->badge->get('id') || !$this->badge->get('provider_badge_id')) : ?>
					<input type="hidden" name="badge[id]" value="<?php echo $this->badge->get('id'); ?>" />
					<table class="admintable">
						<tbody>
							<tr>
								<th class="key" width="250"><label for="badge-published"><?php echo JText::_('Offer a badge for this section?'); ?>:</label></th>
								<td><input type="checkbox" name="badge[published]" id="badge-published" value="1" <?php echo ($this->badge->get('published')) ? 'checked="checked"' : '' ?> /></td>
							</tr>
							<tr class="badge-field-toggle">
								<th class="key"><label for="badge-image"><?php echo JText::_('Badge Image'); ?>:</label></th>
								<td>
									<?php if ($this->badge->get('img_url')) : ?>
										<?php echo $this->escape(stripslashes($this->badge->get('img_url'))); ?>
									<?php else : ?>
										<input type="file" name="badge_image" id="badge-image" />
									<?php endif; ?>
								</td>
							</tr>
							<tr class="badge-field-toggle">
								<th class="key"><label for="badge-provider"><?php echo JText::_('Badge Provider'); ?>:</label></th>
								<td>
									<select name="badge[provider_name]" id="badge-provider">
										<option value="passport"<?php if ($this->badge->get('provider_name', 'passport') == 'passport') { echo ' selected="selected"'; } ?>><?php echo JText::_('Passport'); ?></option>
									</select>
								</td>
							<tr class="badge-field-toggle">
								<th class="key"><label for="badge-criteria"><?php echo JText::_('Badge Criteria'); ?>:</label></th>
								<td>
									<textarea name="badge[criteria]" id="badge-criteria" rows="6" cols="50"><?php echo $this->escape(stripslashes($this->badge->get('criteria_text'))); ?></textarea>
								</td>
							</tr>
						</tbody>
					</table>
				<?php else : ?>
					<input type="hidden" name="badge[id]" value="<?php echo $this->badge->get('id'); ?>" />
					<table class="admintable">
						<tbody>
							<tr>
								<th class="key" width="250"><label for="badge-published"><?php echo JText::_('Offer a badge for this section?'); ?>:</label></th>
								<td><input type="checkbox" name="badge[published]" id="badge-published" value="1" <?php echo ($this->badge->get('published')) ? 'checked="checked"' : '' ?> /></td>
							</tr>
							<tr class="badge-field-toggle">
								<th class="key"><label for="badge-image"><?php echo JText::_('Badge Image'); ?>:</label></th>
								<td>
									<img src="<?php echo $this->badge->get('img_url'); ?>" width="125" />
								</td>
							</tr>
							<tr class="badge-field-toggle">
								<th class="key"><label for="badge-provider"><?php echo JText::_('Badge Provider'); ?>:</label></th>
								<td>
									<?php echo $this->escape(stripslashes($this->badge->get('provider_name'))); ?>
								</td>
							<tr class="badge-field-toggle">
								<th class="key"><label for="badge-criteria"><?php echo JText::_('Badge Criteria'); ?>:</label></th>
								<td>
									<textarea name="badge[criteria]" id="badge-criteria" rows="6" cols="50"><?php echo $this->escape(stripslashes($this->badge->get('criteria_text'))); ?></textarea>
								</td>
							</tr>
						</tbody>
					</table>
				<?php endif; ?>
			</fieldset>
		</div>
	</div>

	<?php echo JHTML::_('form.token'); ?>
</form>
