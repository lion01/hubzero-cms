<?php
/**
 * HUBzero CMS
 *
 * Copyright 2013 Purdue University. All rights reserved.
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
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2013 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
?>
<div id="error-wrap">
	<div id="error-box" class="code-403">
		<h2><?php echo JText::_('COM_TOOLS_BADPARAMS'); ?></h2>
<?php if ($this->getError()) { ?>
		<p class="error-reasons"><?php echo $this->getError(); ?></p>
<?php } ?>
		<p>The custom parameters specified in this tool invocation URL are invalid.</p>
		<pre><?php echo htmlentities($this->badparams,ENT_COMPAT,'UTF-8');?></pre>
		<p>If you feel that these parameters are correct, please <a href="<?php echo JRoute::_('index.php?option=com_support&controller=tickets&task=new'); ?>">contact us</a>.</p>
	</div><!-- / #error-box -->
</div><!-- / #error-wrap -->
