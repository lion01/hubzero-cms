<?php 
defined('_JEXEC') or die('Restricted access');
$juser = JFactory::getUser();

$base = 'index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=forum&scope=' . $this->filters['section'] . '/' . $this->category->get('alias') . '/' . $this->thread->get('thread');

$this->category->set('section_alias', $this->filters['section']);

$this->thread->set('section', $this->filters['section']);
$this->thread->set('category', $this->category->get('alias'));

ximport('Hubzero_User_Profile_Helper');
?>
<ul id="page_options">
	<li>
		<a class="icon-comments comments btn" href="<?php echo JRoute::_($this->category->link()); ?>">
			<?php echo JText::_('PLG_GROUPS_FORUM_ALL_DISCUSSIONS'); ?>
		</a>
	</li>
</ul>

<div class="main section">
	<h3 class="thread-title<?php echo ($this->thread->get('closed')) ? ' closed' : ''; ?>">
		<?php echo $this->escape(stripslashes($this->thread->get('title'))); ?>
	</h3>

<?php foreach ($this->notifications as $notification) { ?>
	<p class="<?php echo $notification['type']; ?>"><?php echo $this->escape($notification['message']); ?></p>
<?php } ?>

	<div class="aside">
		<div class="container">
			<h4><?php echo JText::_('PLG_GROUPS_FORUM_ALL_TAGS'); ?></h4>
		<?php if ($this->thread->tags('cloud')) { ?>
			<?php echo $this->thread->tags('cloud'); ?>
		<?php } else { ?>
			<p><?php echo JText::_('PLG_GROUPS_FORUM_NONE'); ?></p>
		<?php } ?>
		</div><!-- / .container -->

	<?php if ($this->thread->participants()->total() > 0) { ?>
		<div class="container">
			<h4><?php echo JText::_('PLG_GROUPS_FORUM_PARTICIPANTS'); ?></h4>
			<ul>
			<?php 
				$anon = false;
				foreach ($this->thread->participants() as $participant) 
				{ 
					if (!$participant->anonymous) { 
			?>
				<li>
					<a class="member" href="<?php echo JRoute::_('index.php?option=com_members&id=' . $participant->created_by); ?>">
						<?php echo $this->escape(stripslashes($participant->name)); ?>
					</a>
				</li>
			<?php 
					} else if (!$anon) {
						$anon = true;
			?>
				<li>
					<span class="member">
						<?php echo JText::_('PLG_GROUPS_FORUM_ANONYMOUS'); ?>
					</span>
				</li>
			<?php
					}
				}
			?>
			</ul>
	<?php } ?>
		</div><!-- / .container -->

	<?php if ($this->thread->attachments()->total() > 0) { ?>
		<div class="container">
			<h4><?php echo JText::_('PLG_GROUPS_FORUM_ATTACHMENTS'); ?></h4>
			<ul class="attachments">
			<?php 
			foreach ($this->thread->attachments() as $attachment) 
			{
				$cls = 'file';
				$title = $attachment->get('description', $attachment->get('filename'));
				if (preg_match("/bmp|gif|jpg|jpe|jpeg|png/i", $attachment->get('filename')))
				{
					$cls = 'img';
				}
			?>
				<li>
					<a class="<?php echo $cls; ?> attachment" href="<?php echo JRoute::_($base . '/' . $attachment->get('post_id') . '/' . $attachment->get('filename')); ?>">
						<?php echo $this->escape(stripslashes($title)); ?>
					</a>
				</li>
			<?php } ?>
			</ul>
		</div><!-- / .container -->
	<?php } ?>
	</div><!-- / .aside  -->

	<div class="subject">
		<!-- <h4 class="comments-title">
			<?php echo JText::_('PLG_GROUPS_FORUM_COMMENTS'); ?>
		</h4> -->
		<form action="<?php echo JRoute::_($this->thread->link()); ?>" method="get">
			<?php
			if ($this->thread->posts($this->config->get('threading', 'list'), $this->filters)->total() > 0) 
			{
				$view = new Hubzero_Plugin_View(
					array(
						'folder'  => 'groups',
						'element' => 'forum',
						'name'    => 'threads',
						'layout'  => '_list'
					)
				);
				$view->option     = $this->option;
				$view->group      = $this->group;

				$view->comments   = $this->thread->posts($this->config->get('threading', 'list'));
				$view->thread     = $this->thread;
				$view->parent     = 0;

				$view->config     = $this->config;
				$view->depth      = 0;
				$view->cls        = 'odd';
				$view->filters    = $this->filters;
				$view->category   = $this->category;

				$view->display();
			}
			else
			{
				?>
			<ol class="comments">
				<li>
					<p><?php echo JText::_('PLG_GROUPS_FORUM_NO_REPLIES_FOUND'); ?></p>
				</li>
			</ol>
				<?php
			}
			jimport('joomla.html.pagination');
			$pageNav = new JPagination(
				$this->thread->posts('count', $this->filters), 
				$this->filters['start'], 
				$this->filters['limit']
			);
			$pageNav->setAdditionalUrlParam('cn', $this->group->get('cn'));
			$pageNav->setAdditionalUrlParam('active', 'forum');
			$pageNav->setAdditionalUrlParam('scope', $this->filters['section'] . '/' . $this->category->get('alias') . '/' . $this->thread->get('id'));

			echo $pageNav->getListFooter();
			?>
		</form>
	</div><!-- / .subject -->
	<div class="clear"></div>
</div><!-- / .main section -->
<?php if ($this->config->get('access-create-thread') && !$this->thread->get('closed')) { ?>
<div class="below section">
	<h3 class="post-comment-title">
		<?php echo JText::_('PLG_GROUPS_FORUM_ADD_COMMENT'); ?>
	</h3>
	<div class="aside">
		<table class="wiki-reference">
			<caption>Wiki Syntax Reference</caption>
			<tbody>
				<tr>
					<td>'''bold'''</td>
					<td><b>bold</b></td>
				</tr>
				<tr>
					<td>''italic''</td>
					<td><i>italic</i></td>
				</tr>
				<tr>
					<td>__underline__</td>
					<td><span style="text-decoration:underline;">underline</span></td>
				</tr>
				<tr>
					<td>{{{monospace}}}</td>
					<td><code>monospace</code></td>
				</tr>
				<tr>
					<td>~~strike-through~~</td>
					<td><del>strike-through</del></td>
				</tr>
				<tr>
					<td>^superscript^</td>
					<td><sup>superscript</sup></td>
				</tr>
				<tr>
					<td>,,subscript,,</td>
					<td><sub>subscript</sub></td>
				</tr>
			</tbody>
		</table>
	</div><!-- /.aside -->
	<div class="subject">
		<form action="<?php echo JRoute::_($base); ?>" method="post" id="commentform" enctype="multipart/form-data">
			<p class="comment-member-photo">
				<?php
				$anon = (!$juser->get('guest') ? 0 : 1);
				$now = JFactory::getDate();
				?>
				<img src="<?php echo Hubzero_User_Profile_Helper::getMemberPhoto($juser, $anon); ?>" alt="" />
			</p>

			<fieldset>
			<?php if ($juser->get('guest')) { ?>
				<p class="warning"><?php echo JText::_('PLG_GROUPS_FORUM_LOGIN_COMMENT_NOTICE'); ?></p>
			<?php } else { ?>
				<p class="comment-title">
					<strong>
						<a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $juser->get('id')); ?>"><?php echo $this->escape($juser->get('name')); ?></a>
					</strong> 
					<span class="permalink">
						<span class="comment-date-at"><?php echo JText::_('PLG_GROUPS_FORUM_AT'); ?></span>
						<span class="time"><time datetime="<?php echo $now; ?>"><?php echo JHTML::_('date', $now, JText::_('TIME_FORMAT_HZ1')); ?></time></span> 
						<span class="comment-date-on"><?php echo JText::_('PLG_GROUPS_FORUM_ON'); ?></span> 
						<span class="date"><time datetime="<?php echo $now; ?>"><?php echo JHTML::_('date', $now, JText::_('DATE_FORMAT_HZ1')); ?></time></span>
					</span>
				</p>
				
				<label for="field_comment">
					<?php echo JText::_('PLG_GROUPS_FORUM_FIELD_COMMENTS'); ?>
					<?php
					ximport('Hubzero_Wiki_Editor');
					echo Hubzero_Wiki_Editor::getInstance()->display('fields[comment]', 'field_comment', '', '', '35', '15');
					?>
				</label>
				
				<label>
					<?php echo JText::_('PLG_GROUPS_FORUM_FIELD_YOUR_TAGS'); ?>:
					<?php 
						JPluginHelper::importPlugin('hubzero');
						$dispatcher = JDispatcher::getInstance();
						$tf = $dispatcher->trigger( 'onGetMultiEntry', array(array('tags', 'tags', 'actags', '', $this->thread->tags('string'))) );
						if (count($tf) > 0) {
							echo $tf[0];
						} else {
							echo '<input type="text" name="tags" value="' . $this->escape($this->thread->tags('string')) . '" />';
						}
					?>
				</label>
				
				<fieldset>
					<legend><?php echo JText::_('PLG_GROUPS_FORUM_LEGEND_ATTACHMENTS'); ?></legend>
					<div class="grouping">
						<label for="upload">
							<?php echo JText::_('PLG_GROUPS_FORUM_FIELD_FILE'); ?>:
							<input type="file" name="upload" id="upload" />
						</label>

						<label for="upload-description">
							<?php echo JText::_('PLG_GROUPS_FORUM_FIELD_DESCRIPTION'); ?>:
							<input type="text" name="description" id="upload-description" value="" />
						</label>
					</div>
				</fieldset>

				<label for="field-anonymous" id="comment-anonymous-label">
					<input class="option" type="checkbox" name="fields[anonymous]" id="field-anonymous" value="1" /> 
					<?php echo JText::_('PLG_GROUPS_FORUM_FIELD_ANONYMOUS'); ?>
				</label>

				<p class="submit">
					<input type="submit" value="<?php echo JText::_('PLG_GROUPS_FORUM_SUBMIT'); ?>" />
				</p>
			<?php } ?>

				<div class="sidenote">
					<p>
						<strong><?php echo JText::_('PLG_GROUPS_FORUM_KEEP_POLITE'); ?></strong>
					</p>
					<p>
						<?php echo JText::_('PLG_GROUPS_FORUM_WIKI_HINT'); ?>
					</p>
				</div>
			</fieldset>
			<input type="hidden" name="fields[category_id]" value="<?php echo $this->escape($this->thread->get('category_id')); ?>" />
			<input type="hidden" name="fields[parent]" value="<?php echo $this->escape($this->thread->get('id')); ?>" />
			<input type="hidden" name="fields[thread]" value="<?php echo $this->escape($this->thread->get('id')); ?>" />
			<input type="hidden" name="fields[state]" value="1" />
			<input type="hidden" name="fields[scope]" value="<?php echo $this->escape($this->model->get('scope')); ?>" />
			<input type="hidden" name="fields[scope_id]" value="<?php echo $this->escape($this->model->get('scope_id')); ?>" />
			<input type="hidden" name="fields[id]" value="" />

			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="cn" value="<?php echo $this->escape($this->group->get('cn')); ?>" />
			<input type="hidden" name="active" value="forum" />
			<input type="hidden" name="action" value="savethread" />
			<input type="hidden" name="section" value="<?php echo $this->escape($this->filters['section']); ?>" />

			<?php echo JHTML::_('form.token'); ?>
		</form>
	</div><!-- / .subject -->
	<div class="clear"></div>
</div><!-- / .below section -->
<?php } ?>