<?php 
defined('_JEXEC') or die( 'Restricted access' );

ximport('Hubzero_Wiki_Parser');
$view = new Hubzero_Plugin_View(
	array(
		'folder'  => 'courses',
		'element' => 'pages',
		'name'    => 'pages',
		'layout'  => 'default_menu'
	)
);
$view->option     = $this->option;
$view->controller = $this->controller;
$view->course     = $this->course;
$view->offering   = $this->offering;
$view->page       = $this->model;
$view->display();
?>

<div class="pages-wrap">
	<div class="pages-content">

	<?php foreach ($this->notifications as $notification) { ?>
		<p class="<?php echo $notification['type']; ?>"><?php echo $this->escape($notification['message']); ?></p>
	<?php } ?>

		<form action="<?php echo JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->course->get('alias') . '&offering=' . $this->offering->get('alias') . '&active=pages'); ?>" method="post" id="pageform" class="full" enctype="multipart/form-data">
			<fieldset>
				<legend><?php echo ($this->model->exists()) ? JText::_('Edit page') : JText::_('New page'); ?></legend>

				<div class="two columns first">
					<label for="field-title">
						<?php echo JText::_('Title:'); ?> <span class="required"><?php echo JText::_('required'); ?></span>
						<input type="text" name="fields[title]" id="field-title" value="<?php echo $this->escape(stripslashes($this->model->get('title'))); ?>" />
						<span class="hint"><?php echo JText::_('Titles should be relatively short and succinct. Usually one to three words is preferred.'); ?></span>
					</label>
				</div>
				<div class="two columns second">
					<label for="field-url">
						<?php echo JText::_('URL:'); ?> <span class="optional"><?php echo JText::_('optional'); ?></span>
						<input type="text" name="fields[url]" id="field-url" value="<?php echo $this->escape(stripslashes($this->model->get('url'))); ?>" />
						<span class="hint"><?php echo JText::_('URLs can only contain alphanumeric characters and underscores. Spaces will be removed.'); ?></span>
					</label>
				</div>
				<div class="clear"></div>

				<label for="fields_content">Content: <span class="required"><?php echo JText::_('required'); ?></span>
					<?php
						ximport('Hubzero_Wiki_Editor');
						$editor =& Hubzero_Wiki_Editor::getInstance();
						echo $editor->display('fields[content]', 'field_content', stripslashes($this->model->get('content')), '', '50', '50');
					?>
					<span class="hint"><a class="popup" href="<?php echo JRoute::_('index.php?option=com_topics&scope=&pagename=Help:WikiFormatting'); ?>"><?php echo JText::_('Wiki formatting'); ?></a> &amp; <a class="popup" href="<?php echo JRoute::_('index.php?option=com_wiki&scope=&pagename=Help:WikiMacros'); ?>">Wiki Macros</a> are allowed.</span>
				</label>

				<p class="submit">
					<input type="submit" value="<?php echo JText::_('Save'); ?>" />
				</p>
			</fieldset>

			<input type="hidden" name="fields[active]" value="<?php echo $this->model->get('active', 1); ?>" />
			<input type="hidden" name="fields[offering_id]" value="<?php echo $this->offering->get('id'); ?>" />
			<input type="hidden" name="fields[course_id]" value="<?php echo $this->course->get('id'); ?>" />
			<input type="hidden" name="fields[id]" value="<?php echo $this->model->get('id'); ?>" />
	
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
			<input type="hidden" name="gid" value="<?php echo $this->course->get('alias'); ?>" />
			<input type="hidden" name="active" value="pages" />
			<input type="hidden" name="action" value="save" />
			<input type="hidden" name="offering" value="<?php echo $this->offering->get('alias') . ($this->offering->section()->get('alias') != '__default' ? ':' . $this->offering->section()->get('alias') : ''); ?>" />
		</form>

		<div class="clear"></div>
	</div><!-- / .below section -->
</div>