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

$pathway = JFactory::getApplication()->getPathway();
$pathway->addItem(
	JText::_('Fix Links'),
	'index.php?option=' . $this->option . '&scope=' . $this->page->scope . '&pagename=Special:FixLinks'
);

$jconfig = JFactory::getConfig();
$juser = JFactory::getUser();

$limit = JRequest::getInt('limit', $jconfig->getValue('config.list_limit'));
$start = JRequest::getInt('limitstart', 0);

$database = JFactory::getDBO();

$query = "SELECT COUNT(*)
			FROM #__wiki_page AS wp 
			INNER JOIN #__wiki_version AS wv ON wp.version_id = wv.id";

$database->setQuery($query);
$total = $database->loadResult();

$query = "SELECT wp.id, wp.title, wp.pagename, wp.scope, wp.group_cn, wp.version_id, wv.created_by, wv.created, wv.pagetext 
			FROM #__wiki_page AS wp 
			INNER JOIN #__wiki_version AS wv ON wp.version_id = wv.id 
			ORDER BY created DESC";
		if ($limit && $limit != 'all')
		{
			$query .= " LIMIT $start, $limit";
		}

$database->setQuery($query);
$rows = $database->loadObjectList();

jimport('joomla.html.pagination');
$pageNav = new JPagination(
	$total, 
	$start, 
	$limit
);
?>
<form method="get" action="<?php echo JRoute::_('index.php?option=' . $this->option . '&scope=' . $this->page->scope . '&pagename=Special:FixLinks'); ?>">
	<p>
		This special page updates the link log for every page.
	</p>
	<div class="container">
		<table class="entries">
			<thead>
				<tr>
					<th scope="col">
						<?php echo JText::_('Revision ID'); ?>
					</th>
					<th scope="col">
						<?php echo JText::_('Revision timestamp'); ?>
					</th>
					<th scope="col">
						<?php echo JText::_('Page ID'); ?>
					</th>
					<th scope="col">
						<?php echo JText::_('Page'); ?>
					</th>
				</tr>
			</thead>
			<tbody>
<?php
if ($rows) 
{
	ximport('Hubzero_Wiki_Parser');
	$p = Hubzero_Wiki_Parser::getInstance();

	foreach ($rows as $row)
	{
		$wikiconfig = array(
			'option'   => $this->option,
			'scope'    => $row->scope,
			'pagename' => $row->pagename,
			'pageid'   => $row->id,
			'filepath' => '',
			'domain'   => $row->group_cn,
			'loglinks' => true
		);

		$row->pagehtml = $p->parse($row->pagetext, $wikiconfig, true, true);
?>
				<tr>
					<td>
						<?php echo $row->version_id; ?>
					</td>
					<td>
						<time datetime="<?php echo $row->created; ?>"><?php echo $row->created; ?></time>
					</td>
					<td>
						<?php echo $row->id; ?>
					</td>
					<td>
						<a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&scope=' . $row->scope . '&pagename=' . $row->pagename); ?>">
							<?php echo $this->escape(stripslashes($row->title)); ?>
						</a>
					</td>
				</tr>
<?php
	}
}
else
{
?>
				<tr>
					<td colspan="4">
						<?php echo JText::_('No pages needed updating.'); ?>
					</td>
				</tr>
<?php
}
?>
			</tbody>
		</table>
		<?php
		$pageNav->setAdditionalUrlParam('scope', $this->page->scope);
		$pageNav->setAdditionalUrlParam('pagename', 'Special:' . $this->page->pagename);

		echo $pageNav->getListFooter();
		?>
		<div class="clearfix"></div>
	</div>
</form>