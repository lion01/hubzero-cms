<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2013 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2013 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

/**
 * Resources Plugin class for favoriting a wiki page
 */
class plgWikiCollect extends JPlugin
{
	/**
	 * Constructor
	 * 
	 * @param      object &$subject Event observer
	 * @param      array  $config   Optional config values
	 * @return     void
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);

		$this->loadLanguage();
	}

	/**
	 * After display content method
	 * Method is called by the view and the results are imploded and displayed in a placeholder
	 * 
	 * @param      object $page     Wiki page
	 * @param      object $revision Wiki revision
	 * @param      object $config   Wiki config
	 * @return     string
	 */
	public function onAfterDisplayContent($page, $revision, $config)
	{
		$this->page = $page;
		$this->revision = $revision;

		// Incoming action
		$action = JRequest::getVar('action', '');
		if ($action && $action == 'collect') 
		{
			// Check the user's logged-in status
			return $this->fav();
		}

		$arr = array(
			'area' => $this->_name,
			'html' => '',
			'metadata' => ''
		);

		// Build the HTML meant for the "about" tab's metadata overview
		$juser = JFactory::getUser();
		if (!$juser->get('guest')) 
		{
			// Push some scripts to the template
			ximport('Hubzero_Document');
			Hubzero_Document::addPluginScript('wiki', $this->_name);
			Hubzero_Document::addPluginStylesheet('wiki', $this->_name);

			ximport('Hubzero_Plugin_View');
			$view = new Hubzero_Plugin_View(
				array(
					'folder'  => 'wiki',
					'element' => $this->_name,
					'name'    => 'metadata'
				)
			);
			$view->option = 'com_wiki';
			$view->page = $page;
			return $view->loadTemplate();
		}

		return '';
	}

	/**
	 * Un/favorite an item
	 * 
	 * @param      integer $oid Resource to un/favorite
	 * @return     void
	 */
	public function fav()
	{
		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_collections' . DS . 'models' . DS . 'collections.php');

		$this->option   = 'com_wiki';
		$this->juser    = JFactory::getUser();
		$this->database = JFactory::getDBO();

		// Incoming
		$item_id          = JRequest::getInt('item', 0);
		$collection_id    = JRequest::getInt('collection', 0);
		$collection_title = JRequest::getVar('collection_title', '');
		$no_html          = JRequest::getInt('no_html', 0);

		$model = new CollectionsModel('member', $this->juser->get('id'));

		$b = new CollectionsTableItem($this->database);
		$b->loadType($this->page->id, 'wiki');
		if (!$b->id)
		{
			$row = new CollectionsTableCollection($this->database);
			$row->load($collection_id);

			ximport('Hubzero_View_Helper_Html');

			$b->url         = JRoute::_('index.php?option=com_wiki&scope=' . $this->page->scope . '&pagename=' . $this->page->pagename);
			$b->type        = 'wiki';
			$b->object_id   = $this->page->id;
			$b->title       = $this->page->title;
			$b->description = Hubzero_View_Helper_Html::shortenText(strip_tags($this->revision->pagehtml), 300, 0, 1);
			if (!$b->check()) 
			{
				$this->setError($b->getError());
			}
			// Store new content
			if (!$b->store()) 
			{
				$this->setError($b->getError());
			}
			$collection_id = 0;
		}
		$item_id = $b->id;

		// No board ID selected so present repost form
		if (!$collection_id && !$collection_title)
		{
			ximport('Hubzero_Plugin_View');
			$view = new Hubzero_Plugin_View(
				array(
					'folder'  => 'wiki',
					'element' => $this->_name,
					'name'    => 'metadata',
					'layout'  => 'collect'
				)
			);

			$view->myboards      = $model->mine();
			$view->groupboards   = $model->mine('groups');

			$view->name     = $this->_name;
			$view->option   = $this->option;
			$view->page     = $this->page;
			$view->revision = $this->revision;
			$view->no_html  = $no_html;
			$view->item_id  = $item_id;

			if ($no_html)
			{
				$view->display();
				exit;
			}
			else 
			{
				return $view->loadTemplate();
			}
		}

		// Check for request forgeries
		JRequest::checkToken('get') or JRequest::checkToken() or jexit('Invalid Token');

		if ($collection_title)
		{
			$collection = new CollectionsModelCollection();
			$collection->set('title', $collection_title);
			$collection->set('object_id', $this->juser->get('id'));
			$collection->set('object_type', 'member');
			if (!$collection->store())
			{
				$this->setError($collection->getError());
			}
			$collection_id = $collection->get('id');
		}

		if (!$this->getError())
		{
			// Try loading the current board/bulletin to see
			// if this has already been posted to the board (i.e., no duplicates)
			$stick = new CollectionsTablePost($this->database);
			$stick->loadByBoard($collection_id, $item_id);
			if (!$stick->id)
			{
				// No record found -- we're OK to add one
				$stick->item_id       = $item_id;
				$stick->collection_id = $collection_id;
				$stick->description = JRequest::getVar('description', '');
				if ($stick->check()) 
				{
					// Store new content
					if (!$stick->store()) 
					{
						$this->setError($stick->getError());
					}
				}
			}
		}

		// Display updated bulletin stats if called via AJAX
		if ($no_html)
		{
			$response = new stdClass();
			$response->code = 0;
			if ($this->getError())
			{
				$response->code = 1;
				$response->message = $this->getError();
			}
			else
			{
				$response->message = JText::sprintf('PLG_WIKI_COLLECT_PAGE_COLLECTED', $item_id);
			}
			echo json_encode($response);
			exit;
		}
	}
}
