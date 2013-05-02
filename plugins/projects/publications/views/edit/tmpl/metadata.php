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

// Determine pane title
$ptitle = '';
if($this->version == 'dev') {
	$ptitle .= $this->last_idx > $this->current_idx && $this->row->metadata
			? ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_PUB_EDIT_METADATA')) 
			: ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_PUB_ADD_METADATA')) ;
}
else {
	$ptitle .= ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_PANEL_METADATA'));	
}

// Publication title
$pubtitle = $this->row->title;
$this->row->title = $this->row->title == JText::_('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_DEFAULT_TITLE') ? '' : $this->row->title;

$fields = array();
if (trim($this->customFields) != '') {
	$fs = explode("\n", trim($this->customFields));
	foreach ($fs as $f) 
	{
		$fields[] = explode('=', $f);
	}
} 

// Filter meta data
if (!empty($fields)) {
	for ($i=0, $n=count( $fields ); $i < $n; $i++) 
	{
		preg_match("#<nb:".$fields[$i][0].">(.*?)</nb:".$fields[$i][0].">#s", $this->row->metadata, $matches);
		if (count($matches) > 0) {
			$match = $matches[0];
			$match = str_replace('<nb:'.$fields[$i][0].'>','',$match);
			$match = str_replace('</nb:'.$fields[$i][0].'>','',$match);
		} else {
			$match = '';
		}
		
		// Explore the text and pull out all matches
		array_push($fields[$i], $match);
	}
}

ximport('Hubzero_Wiki_Editor');
$editor =& Hubzero_Wiki_Editor::getInstance();

$canedit = (
	$this->pub->state == 3 
	|| $this->pub->state == 4 
	|| $this->pub->state == 5 
	|| in_array($this->active, $this->mayupdate)) 
	? 1 : 0;

?>
<form action="<?php echo $this->url; ?>" method="post" id="plg-form">	
	<?php echo $this->project->provisioned == 1 
				? PublicationHelper::showPubTitleProvisioned( $this->pub, $this->route)
				: PublicationHelper::showPubTitle( $this->pub, $this->route, $this->title); ?>
		<fieldset>	
			<input type="hidden" name="id" value="<?php echo $this->project->id; ?>" id="projectid" />
			<input type="hidden" name="version" value="<?php echo $this->version; ?>" />
			<input type="hidden" name="active" value="publications" />					
			<input type="hidden" name="action" value="save" />
			<input type="hidden" name="base" id="base" value="<?php echo $this->pub->base; ?>" />
			<input type="hidden" name="section" id="section" value="<?php echo $this->active; ?>" />
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="move" id="move" value="<?php echo $this->move; ?>" />
			<input type="hidden" name="review" value="<?php echo $this->inreview; ?>" />
			<input type="hidden" name="pid" id="pid" value="<?php echo $this->pub->id; ?>" />
			<input type="hidden" name="vid" id="vid" value="<?php echo $this->row->id; ?>" />
			<input type="hidden" name="step" value="metadata" />
			<input type="hidden" name="provisioned" id="provisioned" value="<?php echo $this->project->provisioned == 1 ? 1 : 0; ?>" />
			<?php if($this->project->provisioned == 1 ) { ?>
			<input type="hidden" name="task" value="submit" />
			<?php } ?>
		</fieldset>
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
$view->step = 'metadata';
$view->lastpane = $this->lastpane;
$view->option = $this->option;
$view->project = $this->project;
$view->current_idx = $this->current_idx;
$view->last_idx = $this->last_idx;
$view->checked = $this->checked;
$view->show_substeps = (count($fields) > 1) ? 1 : 0;
$view->url = $this->url;
$view->display();

if($this->move) {
	$panel_number = 1;
	while ($panel = current($this->panels)) {
	    if ($panel == $this->active) {
	        $panel_number = key($this->panels) + 1;
	    }
	    next($this->panels);
	}
}
// Section body starts:
?>
	<div id="pub-editor" class="pane-desc">
	  <div id="c-pane" class="columns">
		 <div class="c-inner">
			<?php if ($canedit) { ?>
			<span class="c-submit"><input type="submit" value="<?php if($this->move) { echo JText::_('PLG_PROJECTS_PUBLICATIONS_SAVE_AND_CONTINUE'); } else { echo JText::_('PLG_PROJECTS_PUBLICATIONS_SAVE_CHANGES'); } ?>" <?php if(count($this->checked['description']) == 0) { echo 'class="disabled"'; } ?> class="c-continue" id="c-continue" /></span>
			<?php } ?>
		<h4><?php echo $ptitle; ?> <span class="optional"><?php echo JText::_('OPTIONAL'); ?></span></h4>
		
		<?php if ($canedit) { ?>
					<?php 
					if(count($fields) > 1) {
					$i= 0; ?> 
						<p><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_PUB_METADATA_WRITE'); ?></p>
					<table class="tbl-panel">
						<tbody>
							<tr class="tbl-instruct">
								<td><span class="hint"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_PLEASE_USE'); ?> <a href="/topics/Help:WikiFormatting" rel="external" class="popup"><?php echo JText::_('WIKI_FORMATTING'); ?></a>. <?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_NOTICE_NO_HTML_ALLOWED'); ?></span></td>
								<td><span class="prominent"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_PUB_PREVIEW'); ?></span></td>
							</tr>
					<?php
					foreach ($fields as $field)
					{ 
						$tagcontent = preg_replace('/<br\\s*?\/??>/i', "", end($field));
						$tiplabel = preg_replace('/ /', "", strtoupper($field[1]));
					?>
							<tr>
								<td>
									<label>
										<?php echo stripslashes($field[1]); ?>: <?php echo ($field[3] == 1) ? '<span class="required">'.JText::_('COM_CONTRIBUTE_REQUIRED').'</span>': ''; ?>
										<span class="pub-info-pop tooltips" title="<?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_PUB_TIPS_'.$tiplabel).' :: '.JText::_('PLG_PROJECTS_PUBLICATIONS_PUB_TIPS_'.$tiplabel.'_ABOUT'); ?>">&nbsp;</span>
										<?php if ($field[2] == 'text') { ?>
										<input type="text" name="<?php echo 'nbtag['.$field[0].']'; ?>" value="<?php echo htmlentities(stripslashes($tagcontent), ENT_QUOTES, ENT_COMPAT,'UTF-8'); ?>" />
										<?php } else { ?>
										<textarea name="<?php echo 'nbtag['.$field[0].']'; ?>" cols="50" rows="10" id="pub_<?php echo $field[0]; ?>" class="pubwiki"><?php echo htmlentities(stripslashes($tagcontent)); ?></textarea>
										<?php } ?>
									</label>
								</td>
								<td class="preview-wiki-pane">
									<div id="preview-<?php echo $field[0]; ?>" class="wikipreview">
										<?php if($tagcontent) {
											$html = $this->parser->parse( $tagcontent, $this->wikiconfig );
											echo $html;
										} else {
											echo ProjectsHtml::showNoPreviewMessage();
										} ?>
									</div>
								</td>
							</tr>
					<?php $i++;
					} ?> 
					 </tbody>
					</table>
					<?php } else { ?>
						<p><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_PUB_NO_METADATA_COLLECTED'); ?></p>
					<?php } ?>
			<?php } else { 
				
				$metadata = PublicationsHtml::processMetadata(
					$this->row->metadata, 
					$this->_category, 
					0, 
					$this->pub->id, 
					$this->option, 
					$this->parser, 
					$this->wikiconfig
				);					
			?>
				<p class="notice"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_ADVANCED_CANT_CHANGE').' <a href="'.$this->url.'/?action=newversion">'.ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_NEW_VERSION')).'</a>'; ?></p>
			
			<?php 	echo $metadata['html'] 
				? $metadata['html'] 
				: '<p class="nocontent">'.JText::_('PLG_PROJECTS_PUBLICATIONS_NONE').'</p>';
			} ?>
		 </div>
	   </div>
	</div>
</form>