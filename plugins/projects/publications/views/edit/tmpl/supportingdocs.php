<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

// Display publication type
$published = $this->pub->versions > 0 ? 1 : 0;

// Determine pane title
$ptitle = '';
if($this->version == 'dev') {
	$ptitle .= (($this->last_idx > $this->current_idx || $this->lastpane == 'review') && count($this->attachments) > 0)
	? ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_EDIT')).' ' 
	: ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_SELECT')).' ' ;
}
$ptitle .= JText::_('PLG_PROJECTS_PUBLICATIONS_SUPPORTING_DOCS');
	
?>
	<?php echo $this->project->provisioned == 1 
				? PublicationHelper::showPubTitleProvisioned( $this->pub, $this->route)
				: PublicationHelper::showPubTitle( $this->pub, $this->route, $this->title); ?>
<?php
// Include status bar - publication steps/sections/version navigation
$view = new Hubzero_Plugin_View(
	array(
		'folder'=>'projects',
		'element'=>'publications',
		'name'=>'edit',
		'layout'=>'statusbar'
	)
);
$view->row = $this->row;
$view->version = $this->version;
$view->panels = $this->panels;
$view->active = $this->active;
$view->move = $this->move;
$view->step = 'supporting';
$view->lastpane = $this->lastpane;
$view->option = $this->option;
$view->project = $this->project;
$view->current_idx = $this->current_idx;
$view->last_idx = $this->last_idx;
$view->checked = $this->checked;
$view->url = $this->url;
$view->display();

$canedit = (
	$this->pub->state == 3 
	|| $this->pub->state == 4 
	|| $this->pub->state == 5 
	|| in_array('supportingdocs', $this->mayupdate)) 
	? 1 : 0;

// Section body starts:
?>
	<div id="pub-editor">
		<div class="two columns first" id="c-selector">
			<div class="c-inner" id="c-file-picker">
				<h4><?php echo $ptitle; ?> <span class="optional"><?php echo JText::_('OPTIONAL'); ?></span></h4>
				<?php if ($canedit) { ?>
				<?php if (count($this->choices) > 1) { ?>
				<ul id="c-options">
					<li class="c-tab" id="c-tab-files"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_CONTENT_FILES'); ?></li>
					<!--<li class="c-tab" id="c-tab-apps"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_CONTENT_APPLICATION'); ?></li>-->
					<li class="c-tab" id="c-tab-links"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_CONTENT_LINK'); ?></li>	
					<!--<li class="c-tab" id="c-tab-article"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_CONTENT_TEXT'); ?></li>-->				
				</ul>
				<span id="c-underline">&nbsp;</span>
				<?php } else { ?>
					<p><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_CONTENT_SELECT_SUPPORTING_FILES'); ?></p>		
				<?php }  ?>					
				<!-- Load content selection browser //-->				
				<div id="c-show">
					<noscript>
						<p class="nojs"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_TAGS_NO_JS_MESSAGE'); ?></p>
					</noscript>
				</div>
				<!-- END content selection browser //-->
				<p class="pub-info"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_PUB_INFO_SUPPORTING_CONTENT_MORE'); ?></p>
				<?php } else { ?>
					<p class="notice"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_ADVANCED_CANT_CHANGE').' <a href="'.$this->url.'/?action=newversion">'.ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_NEW_VERSION')).'</a>'; ?></p>
				<?php } ?>
			</div>
		</div>
		<div class="two columns second" id="c-output">
			<form action="<?php echo $this->url;  ?>" method="post" id="plg-form" enctype="multipart/form-data">
			<fieldset>	
				<input type="hidden" name="id" value="<?php echo $this->project->id; ?>" id="projectid" />
				<input type="hidden" name="sel" value="<?php echo count($this->attachments); ?>" id="sel" />
				<input type="hidden" name="version" value="<?php echo $this->version; ?>" />
				<input type="hidden" name="active" value="publications" />					
				<input type="hidden" name="action" value="save" />
				<input type="hidden" name="base" id="base" value="<?php echo $this->base; ?>" />
				<input type="hidden" name="primary" id="primary" value="0" />
				<input type="hidden" name="section" id="section" value="<?php echo $this->active; ?>" />
				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<input type="hidden" name="vid" id="vid" value="<?php echo $this->row->id; ?>" />
				<input type="hidden" name="move" id="move" value="<?php echo $this->move; ?>" />
				<input type="hidden" name="review" value="<?php echo $this->inreview; ?>" />
				<input type="hidden" name="pid" id="pid" value="<?php echo $this->pub->id; ?>" />
				<input type="hidden" name="selections" id="selections" value="" />
				<input type="hidden" name="provisioned" id="provisioned" value="<?php echo $this->project->provisioned == 1 ? 1 : 0; ?>" />
				<?php if($this->project->provisioned == 1 ) { ?>
				<input type="hidden" name="task" value="submit" />
				<?php } ?>
			</fieldset>
			<div class="c-inner">
		<?php if($canedit) { ?>
				<span class="c-submit"><input type="submit" value="<?php if($this->move) { echo JText::_('PLG_PROJECTS_PUBLICATIONS_SAVE_AND_CONTINUE'); } else { echo JText::_('PLG_PROJECTS_PUBLICATIONS_SAVE_CHANGES'); } ?>" id="c-continue" /></span>
		<?php } ?>
				<h5><?php echo ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_PUBLICATION')).' '.JText::_('PLG_PROJECTS_PUBLICATIONS_SUPPORTING_DOCS'); ?>: </h5>
				<ul id="c-filelist" class="c-list">
					<li id="nosel" <?php if($this->pub->id) { echo 'class="hidden"'; } ?> ><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_NO_CONTENT_SELECTED_CLICK'); ?></li>
					<?php 
					// If we have files selected
					if(count($this->attachments) > 0) {
						$i = 1;
						$layout = 'default';
						foreach ($this->attachments as $att) { 
						if ($att->type == 'file') 
						{
							$file = str_replace($this->fpath . DS, '', $att->path);

							// Check if master file is still there
							$gone = is_file($this->prefix.$this->fpath.DS.$att->path) ? 0 : 1;
							?>
							<li id="clone-file::<?php echo urlencode($file); ?>" class="<?php echo 'attached-' . $i; ?> c-drag <?php if($gone) { echo ' i-missing'; } ?>">
						<?php 
						}

						// If database type
						if ($att->type == 'data') 
						{
							$gone   = ''; // TBD
							$layout = 'data';
							$dataid = $att->object_id;
							$dbName = $att->object_name;
							
							$data = new ProjectDatabase($this->database);
							if (!$data->loadRecord($dbName))
							{
								$gone = 1;
							}
							
						 ?>
							<li id="clone-data::<?php echo $dbName; ?>" class="<?php echo 'attached-' . $i; ?> c-drag <?php if($gone) { echo ' i-missing'; } ?>">
						<?php 
						}
						?>

						<?php								
							// Content Info HTML
							ximport('Hubzero_Plugin_View');
							$view = new Hubzero_Plugin_View(
								array(
									'folder'=>'projects',
									'element'=>'publications',
									'name'=>'contentitem',
									'layout' => $layout
								)
							);
							$view->url = $this->url;
							$view->project = $this->project;
							$view->option = $this->option;
							$view->pid = $this->row->publication_id;
							$view->vid = $this->row->id;

							if ($att->type == 'file') 
							{
								$view->path = $this->fpath;
								$view->item = $att->path;
								$view->revision = '';
							}
							elseif ($att->type == 'data')
							{
								$view->data = $data;
							}
							$view->canedit = $canedit;
							$view->move = $this->move;
							$view->att = $att;
							$view->role = 0;
							$view->display();
						?>

					</li>	
				<?php 					
						$i++;
					} 
				}  ?>
			</ul>
				<p id="c-instruct"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_FILES_HINT_DRAG'); ?></p>
			</div>
			</form>
		 </div>
	<iframe id="upload_target" name="upload_target" src="<?php echo JRoute::_('index.php?option='.$this->option.'&id='.$this->project->id.'&active=files').'/?action=blank&no_html=1&ajax=1'; ?>" class="iframe"></iframe> 
	</div>