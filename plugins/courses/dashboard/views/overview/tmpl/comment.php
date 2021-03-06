<?php 
defined('_JEXEC') or die('Restricted access');

	ximport('Hubzero_User_Profile');
	
	$juser = JFactory::getUser();
	
	ximport('Hubzero_User_Profile_Helper');

	$name = JText::_('PLG_COURSES_DISCUSSIONS_ANONYMOUS');
	$huser = '';
	if (!$this->comment->anonymous) 
	{
		$huser = Hubzero_User_Profile::getInstance($this->comment->created_by);
		if (is_object($huser) && $huser->get('name')) 
		{
			$name = '<a href="' . JRoute::_('index.php?option=com_members&id=' . $this->comment->created_by) . '">' . $this->escape(stripslashes($huser->get('name'))) . '</a>';
		}
	}

	$cls = isset($this->cls) ? $this->cls : 'odd';
	if (!$this->comment->anonymous && $this->offering->member($this->comment->created_by)->get('id'))
	{
		$cls .= ' ' . strtolower($this->offering->member($this->comment->created_by)->get('role_alias'));
	}

	$comment  = $this->parser->parse(stripslashes($this->comment->comment), $this->wikiconfig, false);
	$comment .= $this->attach->getAttachment(
		$this->comment->id, 
		$this->base . '&unit=' . $this->unit . '&b=' . $this->comment->object_id . '&c=' . $this->comment->id . '&file=', 
		$this->config
	);
?>
	<li class="comment <?php echo $cls; ?><?php if (!$this->comment->parent) { echo ' start'; } ?>" id="c<?php echo $this->comment->id; ?>">
		<p class="comment-member-photo">
			<a class="comment-anchor" name="c<?php echo $this->comment->id; ?>"></a>
			<img src="<?php echo Hubzero_User_Profile_Helper::getMemberPhoto($huser, $this->comment->anonymous); ?>" alt="" />
		</p>
		<div class="comment-content">
			<p class="comment-title">
				<strong><?php echo $name; ?></strong> 
				<a class="permalink" href="<?php echo JRoute::_($this->base . '&unit=' . $this->unit . '&b=' . $this->lecture . '#c' . $this->comment->id); ?>" title="<?php echo JText::_('PLG_COURSES_DISCUSSIONS_PERMALINK'); ?>"><span class="comment-date-at">@</span> 
					<span class="time"><time datetime="<?php echo $this->comment->created; ?>"><?php echo JHTML::_('date', $this->comment->created, JText::_('TIME_FORMAt_HZ1')); ?></time></span> <span class="comment-date-on"><?php echo JText::_('PLG_COURSES_DISCUSSIONS_ON'); ?></span> 
					<span class="date"><time datetime="<?php echo $this->comment->created; ?>"><?php echo JHTML::_('date', $this->comment->created, JText::_('DATE_FORMAt_HZ1')); ?></time></span>
					<?php if ($this->comment->modified && $this->comment->modified != '0000-00-00 00:00:00') { ?>
						&mdash; <?php echo JText::_('PLG_COURSES_DISCUSSIONS_EDITED'); ?>
						<span class="time"><time datetime="<?php echo $this->comment->modified; ?>"><?php echo JHTML::_('date', $this->comment->modified, JText::_('TIME_FORMAt_HZ1')); ?></time></span> <span class="comment-date-on"><?php echo JText::_('PLG_COURSES_DISCUSSIONS_ON'); ?></span> 
						<span class="date"><time datetime="<?php echo $this->comment->modified; ?>"><?php echo JHTML::_('date', $this->comment->modified, JText::_('DATE_FORMAt_HZ1')); ?></time></span>
					<?php } ?>
				</a>
			<?php if (!$this->comment->anonymous && $this->offering->member($this->comment->created_by)->get('id') && !$this->offering->member($this->comment->created_by)->get('student')) { ?>
				<span class="role <?php echo strtolower($this->offering->member($this->comment->created_by)->get('role_alias')); ?>">
					<?php echo $this->escape(stripslashes($this->offering->member($this->comment->created_by)->get('role_title'))); ?>
				</span>
			<?php } ?>
			</p>
			<?php echo $comment; ?>
		
			<p class="comment-options">
			<?php if ($this->config->get('access-edit-thread') || $juser->get('id') == $this->comment->created_by) { ?>
				<?php if ($this->config->get('access-delete-thread')) { ?>
					<a class="delete" href="<?php echo JRoute::_($this->base . '&unit=' . $this->unit . '&b=' . $this->lecture . '&c=delete&thread=' . $this->comment->id); ?>">
						<?php echo JText::_('PLG_COURSES_DISCUSSIONS_DELETE'); ?>
					</a>
				<?php } ?>
				<?php if ($this->config->get('access-edit-thread')) { ?>
					<a class="edit" href="<?php echo JRoute::_($this->base . '&unit=' . $this->unit . '&b=' . $this->lecture . '&c=edit&thread=' . $this->comment->id); ?>">
						<?php echo JText::_('PLG_COURSES_DISCUSSIONS_EDIT'); ?>
					</a>
				<?php } ?>
			<?php } ?>
				<?php if ($this->depth < $this->config->get('comments_depth', 3)) { ?>
					<a class="reply" href="<?php echo JRoute::_($this->base . '&unit=' . $this->unit . '&b=' . $this->lecture . '&c=reply&thread=' . $this->comment->id . '#post-comment'); ?>" rel="comment-form<?php echo $this->comment->id; ?>">
						<?php echo JText::_('PLG_COURSES_DISCUSSIONS_REPLY'); ?>
					</a>
				<?php } ?>
				<a class="abuse" href="<?php echo JRoute::_('index.php?option=com_support&task=reportabuse&category=forum&id=' . $this->comment->id . '&parent=' . $this->comment->parent); ?>" rel="comment-form<?php echo $this->comment->id; ?>">
					<?php echo JText::_('PLG_COURSES_DISCUSSIONS_REPORT_ABUSE'); ?>
				</a>
			</p>
		
		<?php /*if ($this->depth < $this->config->get('comments_depth', 3)) { ?>
			<div class="comment-add hide" id="comment-form<?php echo $this->comment->id; ?>">
				<form action="<?php echo JRoute::_($this->base); ?>" method="post" enctype="multipart/form-data">
					<fieldset>
						<legend><span><?php echo JText::sprintf('PLG_COURSES_DISCUSSIONS_REPLYING_TO', (!$this->comment->anonymous ? $name : JText::_('PLG_COURSES_DISCUSSIONS_ANONYMOUS'))); ?></span></legend>

						<input type="hidden" name="fields[id]" value="0" />
						<input type="hidden" name="fields[state]" value="1" />
						<input type="hidden" name="fields[scope]" value="course" />
						<input type="hidden" name="fields[category_id]" value="<?php echo $this->post->get('category_id'); ?>" />
						<input type="hidden" name="fields[scope_id]" value="<?php echo $this->post->get('scope_id'); ?>" />
						<input type="hidden" name="fields[object_id]" value="<?php echo $this->post->get('object_id'); ?>" />
						<input type="hidden" name="fields[parent]" value="<?php echo $this->comment->id; ?>" />
						<input type="hidden" name="fields[created]" value="" />
						<input type="hidden" name="fields[created_by]" value="<?php echo $juser->get('id'); ?>" />

						<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
						<input type="hidden" name="gid" value="<?php echo $this->course->get('alias'); ?>" />
						<input type="hidden" name="offering" value="<?php echo $this->course->offering()->get('alias'); ?>" />
						<input type="hidden" name="active" value="forum" />
						<input type="hidden" name="action" value="savethread" />
						<input type="hidden" name="return" value="<?php echo base64_encode(JRoute::_($this->base . '&unit=' . $this->unit . '&b=' . $this->lecture . '&c=' . $this->comment->id)); ?>" />

						<label for="comment-<?php echo $this->comment->id; ?>-content">
							<span class="label-text"><?php echo JText::_('PLG_COURSES_DISCUSSIONS_FIELD_COMMENTS'); ?></span>
							<textarea name="fields[comment]" id="comment-<?php echo $this->comment->id; ?>-content" rows="4" cols="50" placeholder="<?php echo JText::_('PLG_COURSES_DISCUSSIONS_ENTER_COMMENTS'); ?>"></textarea>
						</label>

						<label class="upload-label" for="comment-<?php echo $this->comment->id; ?>-file">
							<span class="label-text"><?php echo JText::_('PLG_COURSES_DISCUSSIONS_ATTACH_FILE'); ?>:</span>
							<input type="file" name="upload" id="comment-<?php echo $this->comment->id; ?>-file" />
						</label>

						<label class="reply-anonymous-label" for="comment-<?php echo $this->comment->id; ?>-anonymous">
					<?php if ($this->config->get('comments_anon', 1)) { ?>
							<input class="option" type="checkbox" name="fields[anonymous]" id="comment-<?php echo $this->comment->id; ?>-anonymous" value="1" /> 
							<?php echo JText::_('PLG_COURSES_DISCUSSIONS_FIELD_ANONYMOUS'); ?>
					<?php } else { ?>
							&nbsp; <input class="option" type="hidden" name="fields[anonymous]" value="0" /> 
					<?php } ?>
						</label>

						<p class="submit">
							<input type="submit" value="<?php echo JText::_('PLG_COURSES_DISCUSSIONS_SUBMIT'); ?>" /> 
							<a class="cancelreply" href="<?php echo JRoute::_($this->base . '&unit=' . $this->unit . '&b=' . $this->lecture . '#c' . $this->comment->id); ?>">
								<?php echo JText::_('PLG_COURSES_DISCUSSIONS_CANCEL'); ?>
							</a>
						</p>
					</fieldset>
				</form>
			</div><!-- / .addcomment -->
		<?php }*/ ?>
		</div><!-- / .comment-content -->
		<?php
		/*if ($this->comment->replies && $this->depth < $this->config->get('comments_depth', 3)) 
		{
			$view = new Hubzero_Plugin_View(
				array(
					'folder'  => 'courses',
					'element' => 'forum',
					'name'    => 'threads',
					'layout'  => 'list'
				)
			);
			$view->option     = $this->option;
			$view->comments   = $this->comment->replies;
			$view->post       = $this->post;
			$view->unit       = $this->unit;
			$view->lecture    = $this->lecture;
			$view->config     = $this->config;
			$view->depth      = $this->depth;
			$view->cls        = $cls;
			$view->base       = $this->base;
			$view->parser     = $this->parser;
			$view->wikiconfig = $this->wikiconfig;
			$view->attach     = $this->attach;
			$view->course     = $this->course;
			$view->display();
		}*/
		?>
	</li>