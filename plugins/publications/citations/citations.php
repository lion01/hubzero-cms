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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');
	
/**
 * Publications Plugin class for citations
 */
class plgPublicationsCitations extends JPlugin
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
		
		// Load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'publications', 'citations' );
		$this->_params = new JParameter( $this->_plugin->params );

		$this->loadLanguage();
	}
	
	/**
	 * Return the alias and name for this category of content
	 * 
	 * @param      object $publication 	Current publication
	 * @param      string $version 		Version name
	 * @param      boolean $extended 	Whether or not to show panel
	 * @return     array
	 */	
	public function &onPublicationAreas( $publication, $version = 'default', $extended = true)
	{
		if ($publication->_category->_params->get('plg_citations') && $extended) 
		{
			$areas = array(
				'citations' => JText::_('PLG_PUBLICATION_CITATIONS')
			);
		} 
		else 
		{
			$areas = array();
		}
		return $areas;
	}

	/**
	 * Return data on a resource view (this will be some form of HTML)
	 * 
	 * @param      object  	$publication 	Current publication
	 * @param      string  	$option    		Name of the component
	 * @param      array   	$areas     		Active area(s)
	 * @param      string  	$rtrn      		Data to be returned
	 * @param      string 	$version 		Version name
	 * @param      boolean 	$extended 		Whether or not to show panel
	 * @return     array
	 */	
	public function onPublication( $publication, $option, $areas, $rtrn='all', $version = 'default', $extended = true  )
	{
		$arr = array(
			'html'=>'',
			'metadata'=>''
		);
		
		// Check if our area is in the array of areas we want to return results for
		if (is_array( $areas )) 
		{
			if (!array_intersect( $areas, $this->onPublicationAreas( $publication ) ) 
			&& !array_intersect( $areas, array_keys( $this->onPublicationAreas( $publication ) ) )) 
			{
				if ($publication->_category->_params->get('plg_citations')) 
				{
					$rtrn == 'metadata';
				}
				else
				{
					return $arr;
				}
			}
		}
		
		// Only applicable to latest published version
		if (!$extended) 
		{
			return $arr;
		}
		
		$database = JFactory::getDBO();

		// Get a needed library
		include_once( JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_citations' . DS . 'tables' . DS . 'citation.php' );
		include_once( JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_citations' . DS . 'tables' . DS . 'association.php' );
		include_once( JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_citations' . DS . 'tables' . DS . 'author.php' );
		include_once( JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_citations' . DS . 'tables' . DS . 'secondary.php' );

		// Get citations for this publication
		$c = new CitationsCitation( $database );
		$citations = $c->getCitations( 'publication', $publication->id );
		
		// Are we returning HTML?
		if ($rtrn == 'all' || $rtrn == 'html') 
		{
			// Instantiate a view
			ximport('Hubzero_Plugin_View');
			$view = new Hubzero_Plugin_View(
				array(
					'folder'=>'publications',
					'element'=>'citations',
					'name'=>'browse'
				)
			);

			// Pass the view some info
			$view->option = $option;
			$view->publication = $publication;
			$view->citations = $citations;
			$view->format = (isset($this->_params) && $this->_params->get('format')) ? $this->_params->get('format') : 'APA';
			if ($this->getError()) 
			{
				$view->setError( $this->getError() );
			}

			// Return the output
			$arr['html'] = $view->loadTemplate();
		}

		// Are we returning metadata?
		if ($rtrn == 'all' || $rtrn == 'metadata') 
		{
			if ($publication->alias) 
			{
				$url = JRoute::_('index.php?option='.$option.'&alias='.$publication->alias.'&active=citations&v=' . $publication->version_number);
			} 
			else 
			{
				$url = JRoute::_('index.php?option='.$option.'&id='.$publication->id.'&active=citations&v=' . $publication->version_number);
			}
			
			$arr['metadata']  = '<p class="citation"><a href="'.$url.'">'.JText::sprintf('PLG_PUBLICATION_CITATIONS_COUNT',count($citations)).'</a></p>';
		}

		// Return results
		return $arr;
	}
}
