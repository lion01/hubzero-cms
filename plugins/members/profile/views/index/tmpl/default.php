<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

//get user object
$juser = JFactory::getUser();

//flags for not logged in and not user
$loggedin = false;
$isUser = false;

//if we are logged in set logged in flag
if (!$juser->get('guest')) 
{
	$loggedin = true;
}

//if we are this user set user flag
if ($juser->get("id") == $this->profile->get("uidNumber")) 
{
	$isUser = true;
}

//registration update
$update_missing = array();
if(isset($this->registration_update))
{
	$update_missing = $this->registration_update->_missing;
}

//incremental registration
require_once JPATH_BASE.'/administrator/components/com_register/tables/incremental.php';
$uid = (int)$this->profile->get('uidNumber');
$incrOpts = new ModIncrementalRegistrationOptions;
$isIncrementalEnabled = $incrOpts->isEnabled($uid);
?>

<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>

<div id="profile-page-content" data-url="<?php echo JRoute::_('index.php?option=com_members&id=' . $this->profile->get('uidNumber') . '&active=profile'); ?>">
	<h3 class="section-header">
		<?php echo JText::_('PLG_MEMBERS_PROFILE'); ?>
	</h3>

	<?php if(count($update_missing) > 0) : ?>
		<?php if(count($update_missing) == 1 && in_array("usageAgreement",array_keys($update_missing))) : ?>
		<?php else: ?>
			<div class="error member-update-missing">
				<strong><?php echo JText::_('PLG_MEMBERS_PROFILE_UPDATE_BEFORE_CONTINUING'); ?></strong>
				<ul>
					<?php foreach ($update_missing as $um) : ?>
						<li><?php echo $um; ?></li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>
	<?php endif; ?>
	
	<?php if ($isUser) : ?>
	<ul>
		<li id="member-profile-completeness" class="hide">
			<?php echo JText::_('PLG_MEMBERS_PROFILE_COMPLETENESS'); ?>
			<div id="meter">
				<span id="meter-percent" data-percent="<?php echo $this->completeness; ?>" data-percent-level="<?php echo @$this->completeness_level; ?>" style="width:0%"></span>
			</div>
			<?php if ($isUser && $isIncrementalEnabled) : ?>
				<span id="completeness-info"><?php echo JText::_('PLG_MEMBERS_PROFILE_COMPLETENESS_MEANS'); ?></span>
			<?php endif; ?>
		</li>
	</ul>	
	<?php endif; ?>
	
	<?php 
		if ($isUser && $isIncrementalEnabled) 
		{ 
			$awards = new ModIncrementalRegistrationAwards($this->profile);
			$awards = $awards->award();
			
			$increm  = '<div id="award-info">';
			$increm .= '<p>' . JText::sprintf('PLG_MEMBERS_PROFILE_INCREMENTAL_OFFERING_POINTS', JRoute::_('index.php?option=com_store')) . '</p>';
			
			if ($awards['prior']) 
			{
				$increm .= '<p>' . JText::sprintf('PLG_MEMBERS_PROFILE_INCREMENTAL_AWARDED_POINTS', $awards['prior']) . '</p>';
			}
		
			if ($awards['new']) 
			{
				$increm .= '<p>' . JText::sprintf('PLG_MEMBERS_PROFILE_INCREMENTAL_EARNED_POINTS', $awards['new']) . '</p>';
			}
			
			$increm .= '<p>' . JText::sprintf('PLG_MEMBERS_PROFILE_INCREMENTAL_EARN_MORE_POINTS', $incrOpts->getAwardPerField(), JRoute::_('index.php?option=com_store'), JRoute::_('index.php?option=com_answers'), JRoute::_('index.php?option=com_wishlist')) .'</p>';
			
			$increm .= '</div>';
			$increm .= '<div id="wallet"><span>'.($awards['prior'] + $awards['new']).'</span></div>';
			$increm .= '<script type="text/javascript">
							window.bonus_eligible_fields = '.json_encode($awards['eligible']).';
							window.bonus_amount = '.$incrOpts->getAwardPerField().';
						</script>';
			echo $increm;
			ximport('Hubzero_Document');
			Hubzero_Document::addComponentScript('assets/js/incremental');
		}
	?>
	
	<?php if (isset($update_missing) && in_array("usageAgreement",array_keys($update_missing))) : ?>
		<div id="usage-agreement-popup">
			<form action="index.php" method="post" data-section-registration="usageAgreement" data-section-profile="usageAgreement">
				<h2><?php echo JText::_('PLG_MEMBERS_PROFILE_NEW_TERMS_OF_USE'); ?></h2>
				<div id="usage-agreement-box">
					<iframe id="usage-agreement" src="/legal/terms?tmpl=component"></iframe>
					<div id="usage-agreement-last-chance">
						<h3><?php echo JText::_('PLG_MEMBERS_PROFILE_ARE_YOU_SURE'); ?></h3>
						<p><?php echo JText::_('PLG_MEMBERS_PROFILE_ARE_YOU_SURE_EXPLANATION'); ?></p>
					</div>
				</div>
				<div id="usage-agreement-buttons">
					<button class="section-edit-cancel usage-agreement-do-not-agree"><?php echo JText::_('PLG_MEMBERS_PROFILE_TERMS_NOT_AGREE'); ?></button>
					<button class="section-edit-submit"><?php echo JText::_('PLG_MEMBERS_PROFILE_TERMS_AGREE'); ?></button>
				</div>
				<div id="usage-agreement-last-chance-buttons">
					<button class="section-edit-cancel usage-agreement-back-to-agree"><?php echo JText::_('PLG_MEMBERS_PROFILE_TERMS_GO_BACK'); ?></button>
					<button class="section-edit-cancel usage-agreement-dont-accept"><?php echo JText::_('PLG_MEMBERS_PROFILE_TERMS_I_DO_NOT_AGREE'); ?></button>
				</div>
				<input type="hidden" name="declinetou" value="0" />
				<input type="hidden" name="usageAgreement" value="1" />
				<input type="hidden" name="field_to_check[]" value="usageAgreement" />
				<input type="hidden" name="option" value="com_members" />
				<input type="hidden" name="id" value="<?php echo $juser->get("id"); ?>" />
				<input type="hidden" name="task" value="save" />
			</form>
		</div>
	<?php endif; ?>
	
	<ul id="profile">
		<?php if($isUser) : ?> 
			<li class="profile-name section hidden">
				<div class="section-content">
					<div class="key"><?php echo JText::_('PLG_MEMBERS_PROFILE_NAME'); ?></div>
					<div class="value"><?php echo $this->escape($this->profile->get('name')); ?></div>
					<br class="clear" />
					<?php
						ximport('Hubzero_Plugin_View');
						$editview = new Hubzero_Plugin_View(
							array(
								'folder'  => 'members',
								'element' => 'profile',
								'name'    => 'edit'
							)
						);
						$name  = '<label class="side-by-side three">' . JText::_('PLG_MEMBERS_PROFILE_FIRST_NAME') . ' <input type="text" name="name[first]" id="" class="input-text" value="'.$this->escape($this->profile->get("givenName")).'" /></label>';
						$name .= '<label class="side-by-side three">' . JText::_('PLG_MEMBERS_PROFILE_MIDDLE_NAME') . ' <input type="text" name="name[middle]" id="" class="input-text" value="'.$this->escape($this->profile->get("middleName")).'" /></label>';
						$name .= '<label class="side-by-side three no-padding-right">' . JText::_('PLG_MEMBERS_PROFILE_LAST_NAME') . ' <input type="text" name="name[last]" id="" class="input-text" value="'.$this->escape($this->profile->get("surname")).'" /></label>';

						$editview->registration_field = 'name';
						$editview->profile_field = 'name';
						$editview->title   =  JText::_('PLG_MEMBERS_PROFILE_NAME');
						$editview->profile = $this->profile;
						$editview->isUser  = $isUser;
						$editview->inputs  = $name;
						$editview->access  = '';
						$editview->display();
					?>
				</div>
				<div class="section-edit">
					<a class="edit-profile-section" href="#">
						<?php echo JText::_('PLG_MEMBERS_PROFILE_EDIT'); ?>
					</a>
				</div>
			</li>
		<?php endif; ?>
		
		<?php if(!JPluginHelper::isEnabled('members', 'account')) : ?>
			<?php if($isUser) : ?>
				<li class="profile-password section hidden">
					<div class="section-content">
						<div class="key"><?php echo JText::_('PLG_MEMBERS_PROFILE_PASSWORD'); ?></div>
						<div class="value">***************</div>
						<br class="clear" />
						<div class="section-edit-container">
							<!--
							<div class="edit-profile-title"><h2>Change Password</h2></div>
							<a href="#" class="edit-profile-close">&times;</a>
							-->
							<div class="section-edit-content">
								<form action="index.php" method="post" data-section-registation="password" data-section-profile="password">
									<span class="section-edit-errors"></span>
									<label for="password">
										<?php echo JText::_('PLG_MEMBERS_PROFILE_PASSWORD_CURRENT'); ?>
										<input type="password" name="oldpass" id="password" class="input-text" />
									</label>
									<label for="newpass" class="side-by-side">
										<?php echo JText::_('PLG_MEMBERS_PROFILE_PASSWORD_NEW'); ?> 
										<input type="password" name="newpass" id="newpass" class="input-text" />
									</label>
									<label for="newpass2" class="side-by-side no-padding-right">
										<?php echo JText::_('PLG_MEMBERS_PROFILE_PASSWORD_CONFIRM'); ?> 
										<input type="password" name="newpass2" id="newpass2" class="input-text" />
									</label>
									<input type="hidden" name="change" value="1" />
									<input type="submit" class="section-edit-submit" value="Save" /> 
									<input type="reset" class="section-edit-cancel" value="Cancel" /> 
									<input type="hidden" name="option" value="com_members" />
									<input type="hidden" name="id" value="<?php echo $this->profile->get('uidNumber'); ?>" />
									<input type="hidden" name="task" value="changepassword" />
									<input type="hidden" name="no_html" value="1" />
								</form>
							</div>
						</div>
					</div>
					<div class="section-edit">
						<a class="edit-profile-section" href="#">
							<?php echo JText::_('PLG_MEMBERS_PROFILE_EDIT'); ?>
						</a>
					</div>
				</li>
			<?php endif; ?>
		<?php endif; ?>

		<?php if ($this->registration->Organization != REG_HIDE) : ?>
			<?php if ($this->params->get('access_org') == 0 
					|| ($this->params->get('access_org') == 1 && $loggedin) 
					|| ($this->params->get('access_org') == 2 && $isUser)
					) : ?>
				
					<?php
						$cls = "";
						if($this->params->get('access_org') == 2) 
						{
							$cls .= "private";
						}                     
						if($this->profile->get("organization") == "" || is_null($this->profile->get("organization")))
						{
							$cls .= ($isUser) ? " hidden" : " hide";
						}
					?>
				<li class="profile-org section <?php echo $cls; ?>">
					<div class="section-content">
						<div class="key"><?php echo JText::_('PLG_MEMBERS_PROFILE_ORGANIZATION'); ?></div>
						<div class="value">
							<?php 
								$org = $this->escape(stripslashes($this->profile->get('organization'))); 
								echo ($org) ? $org : JText::_('PLG_MEMBERS_PROFILE_ENTER_ORG');
							?>
						</div>
						<br class="clear" />
						<?php
							//get list of organizations from db
							include_once( JPATH_ROOT.DS.'components'.DS.'com_register'.DS.'tables'.DS.'organization.php' );
							$database = JFactory::getDBO();
							$xo = new RegisterOrganization( $database );
							$orgs = $xo->getOrgs();
						
							//create select for organizations and optional text input
							$organizations  = '<select name="org" class="input-select">';
							$organizations .= '<option value="">' . JText::_('PLG_MEMBERS_PROFILE_SELECT_OR_ENTER_BELOW') . '</option>';
							foreach($orgs as $o)
							{   
								$sel = ($o == $this->profile->get("organization")) ? "selected=\"selected\"" : "";
								$organizations .= "<option {$sel} value=\"{$o}\">{$o}</option>";
							}
							$organizations .= "</select>";
							$organization_alt = (!in_array($this->profile->get("organization"), $orgs)) ? $this->escape($this->profile->get('organization')) : "";
							$organizations_text = "<input type=\"text\" name=\"orgtext\" class=\"input-text\" value=\"{$organization_alt}\" />";
						
							ximport('Hubzero_Plugin_View');
							$editview = new Hubzero_Plugin_View(
								array(
									'folder'  => 'members',
									'element' => 'profile',
									'name'    => 'edit'
								)
							);
							$editview->registration_field = 'org';
							$editview->profile_field = 'organization';
							$editview->title =  JText::_('PLG_MEMBERS_PROFILE_ORGANIZATION');
							$editview->profile = $this->profile;
							$editview->isUser = $isUser;
							$editview->inputs = '<label>' . JText::_('PLG_MEMBERS_PROFILE_ORGANIZATION') . $organizations.'</label><label>' . JText::_('PLG_MEMBERS_PROFILE_ENTER_ORG_BELOW') . $organizations_text.'</label>';
							$editview->access = '<label>' . JText::_('PLG_MEMBERS_PROFILE_PRIVACY') . MembersHtml::selectAccess('access[org]',$this->params->get('access_org'),'input-select') . '</label>';
							$editview->display();
						?>
					</div>
					<?php if($isUser) : ?>
						<div class="section-edit">
							<a class="edit-profile-section" href="#">
								<?php echo JText::_('PLG_MEMBERS_PROFILE_EDIT'); ?>
							</a>
						</div>
					<?php endif; ?>
				</li>
			<?php endif; ?>
		<?php endif; ?>
	
		<?php if ($this->registration->Employment != REG_HIDE) : ?>
			<?php if ($this->params->get('access_orgtype') == 0 
			 		|| ($this->params->get('access_orgtype') == 1 && $loggedin) 
			 		|| ($this->params->get('access_orgtype') == 2 && $isUser)
					) : ?>
					<?php
						$cls = "";
						if ($this->params->get('access_orgtype') == 2) 
						{
							$cls .= "private";
						}                     
						if ($this->profile->get("orgtype") == "" || is_null($this->profile->get("orgtype")))
						{
							$cls .= ($isUser) ? " hidden" : " hide";
						}
					?>
				<li class="profile-orgtype section <?php echo $cls; ?>">
					<div class="section-content">
						<div class="key"><?php echo JText::_('PLG_MEMBERS_PROFILE_EMPLOYMENT_TYPE'); ?></div>
						<?php
							//get organization types from db
							include_once( JPATH_ROOT.DS.'components'.DS.'com_register'.DS.'tables'.DS.'organizationtype.php' );
							$database = JFactory::getDBO();
							$xot = new RegisterOrganizationType( $database );
							$orgtypes = $xot->getTypes();

							//output value
							if (array_key_exists($this->profile->get("orgtype"), $orgtypes)) {
								$orgtype = $orgtypes[$this->profile->get("orgtype")];
							} else {
								$orgtype = $this->escape($this->profile->get('orgtype'));
							}
						?>
						<div class="value">
							<?php echo ($orgtype) ? $orgtype : JText::_('PLG_MEMBERS_PROFILE_ENTER_EMPLOYMENT_TYPE'); ?>
						</div>
						<br class="clear" />
						<?php
							//build select of org types
							$organization_types  = "<select name=\"orgtype\" class=\"input-select\">";
							foreach($orgtypes as $k => $o)
							{   
								$sel = ($k == $this->profile->get("orgtype")) ? "selected=\"selected\"" : "";
								$organization_types .= "<option {$sel} value=\"{$k}\">{$o}</option>";
							}
							$organization_types .= "</select>"; 

							ximport('Hubzero_Plugin_View');
							$editview = new Hubzero_Plugin_View(
								array(
									'folder'  => 'members',
									'element' => 'profile',
									'name'    => 'edit'
								)
							);
							$editview->registration_field = 'orgtype';
							$editview->profile_field = 'orgtype';
							$editview->title =  JText::_('PLG_MEMBERS_PROFILE_EMPLOYMENT_TYPE');
							$editview->profile = $this->profile;
							$editview->isUser = $isUser;
							$editview->inputs = '<label>' . JText::_('PLG_MEMBERS_PROFILE_EMPLOYMENT_TYPE') . $organization_types.'</label>';
							$editview->access = '<label>' . JText::_('PLG_MEMBERS_PROFILE_PRIVACY') . MembersHtml::selectAccess('access[orgtype]',$this->params->get('access_orgtype'),'input-select') . '</label>';
							$editview->display();
						?>
					</div>
					<?php if ($isUser) : ?>
						<div class="section-edit">
							<a class="edit-profile-section" href="#">
								<?php echo JText::_('PLG_MEMBERS_PROFILE_EDIT'); ?>
							</a>
						</div>
					<?php endif; ?>
				</li>
			<?php endif; ?>
		<?php endif; ?>
	
		<?php if ($this->profile->get('email')) : ?>
			<?php if ($this->params->get('access_email', 2) == 0 
			 		|| ($this->params->get('access_email', 2) == 1 && $loggedin) 
			 		|| ($this->params->get('access_email', 2) == 2 && $isUser)
					) : ?>
					<?php
						$cls = "";
						if ($this->params->get('access_email', 2) == 2) 
						{
							$cls .= "private";
						}                     
						if ($this->profile->get("email") == "" || is_null($this->profile->get("email")))
						{
							$cls .= ($isUser) ? " hidden" : " hide";
						}
					?>
				<li class="profile-email section <?php echo $cls; ?>">
					<div class="section-content">
						<div class="key"><?php echo JText::_('PLG_MEMBERS_PROFILE_EMAIL'); ?></div>
						<div class="value">
							<a class="email" href="mailto:<?php echo MembersHtml::obfuscate($this->profile->get('email')); ?>" rel="nofollow">
								<?php echo MembersHtml::obfuscate($this->profile->get('email')); ?>
							</a>
						</div>
						<br class="clear" />
						<?php
							ximport('Hubzero_Plugin_View');
							$editview = new Hubzero_Plugin_View(
								array(
									'folder'  => 'members',
									'element' => 'profile',
									'name'    => 'edit'
								)
							);
							$editview->registration_field = 'email';
							$editview->profile_field = 'email';
							$editview->title = JText::_('PLG_MEMBERS_PROFILE_EMAIL');
							$editview->profile = $this->profile;
							$editview->isUser = $isUser;
							$editview->inputs = '<label class="side-by-side">' . JText::_('PLG_MEMBERS_PROFILE_EMAIL_VALID') . ' <input type="text" class="input-text" name="email" id="profile-email" value="'.$this->escape($this->profile->get('email')).'" /></label>';
							$editview->inputs .= '<label class="side-by-side no-padding-right">' . JText::_('PLG_MEMBERS_PROFILE_EMAIL_CONFIRM') . ' <input type="text" class="input-text" name="email2" id="profile-email2" value="'.$this->escape($this->profile->get('email')).'" /></label>';
							$editview->inputs .= '<br class="clear" /><p class="warning no-margin-top">' . JText::_('PLG_MEMBERS_PROFILE_EMAIL_WARNING') . '</p>';
							$editview->access = '<label>' . JText::_('PLG_MEMBERS_PROFILE_PRIVACY') . MembersHtml::selectAccess('access[email]',$this->params->get('access_email'),'input-select') . '</label>';
							$editview->display();
						?>
					</div>
					<?php if($isUser) : ?>
						<div class="section-edit">
							<a class="edit-profile-section" href="#">
								<?php echo JText::_('PLG_MEMBERS_PROFILE_EDIT'); ?>
							</a>
						</div>
					<?php endif; ?>
				</li>
			<?php endif; ?>
		<?php endif; ?>

		<?php if ($this->registration->URL != REG_HIDE) : ?>
			<?php if ($this->params->get('access_url') == 0 
			 		|| ($this->params->get('access_url') == 1 && $loggedin) 
			 		|| ($this->params->get('access_url') == 2 && $isUser)
				) : ?>
					<?php
						$cls = "";
						if ($this->params->get('access_url') == 2) 
						{
							$cls .= "private";
						}
						if ($this->profile->get("url") == "" || is_null($this->profile->get("url")))
						{
							$cls .= ($isUser) ? " hidden" : " hide";
						}
						if (isset($update_missing) && in_array("web", array_keys($update_missing))) 
						{
							$cls = str_replace(" hide", "", $cls);
							$cls .= " missing";
						}
					?>
				<li class="profile-web section <?php echo $cls; ?>">
					<div class="section-content">
						<div class="key"><?php echo JText::_('PLG_MEMBERS_PROFILE_WEBSITE'); ?></div>
						<?php
							$url = stripslashes($this->profile->get('url'));
							if ($url)
							{
								$UrlPtn  = "(?:https?:|mailto:|ftp:|gopher:|news:|file:)";
								if (!preg_match("/$UrlPtn/", $url)) 
								{
									$url = 'http://' . $url;
								}
							}
							$title = JText::sprintf('PLG_MEMBERS_PROFILE_WEBSITE_MEMBERS', $this->profile->get('name'));
							$url = ($url) ? '<a class="url" rel="external" title="' . $title . '" href="' . $url . '">' . $url . '</a>' : JText::_('PLG_MEMBERS_PROFILE_WEBSITE_ENTER');
						?>
						<div class="value"><?php echo $url; ?></div>
						<br class="clear" />
						<?php
							ximport('Hubzero_Plugin_View');
							$editview = new Hubzero_Plugin_View(
								array(
									'folder'  => 'members',
									'element' => 'profile',
									'name'    => 'edit'
								)
							);
							$editview->registration_field = 'web';
							$editview->profile_field = 'url';
							$editview->title = JText::_('PLG_MEMBERS_PROFILE_WEBSITE');
							$editview->profile = $this->profile;
							$editview->isUser = $isUser;
							$editview->inputs = '<label>' . JText::_('PLG_MEMBERS_PROFILE_WEBSITE') . '<input type="text" class="input-text" name="web" id="profile-url" value="'.$this->escape($this->profile->get('url')).'" /></label>';
							$editview->access = '<label>' . JText::_('PLG_MEMBERS_PROFILE_PRIVACY') . MembersHtml::selectAccess('access[url]',$this->params->get('access_url'),'input-select') . '</label>';
							$editview->display();
						?>
					</div>
					<?php if($isUser) : ?>
						<div class="section-edit">
							<a class="edit-profile-section" href="#">
								<?php echo JText::_('PLG_MEMBERS_PROFILE_EDIT'); ?>
							</a>
						</div>
					<?php endif; ?>
				</li>
			<?php endif; ?>
		<?php endif; ?>

		<?php if ($this->registration->Phone != REG_HIDE) : ?>
			<?php if ($this->params->get('access_phone') == 0 
			 		|| ($this->params->get('access_phone') == 1 && $loggedin) 
			 		|| ($this->params->get('access_phone') == 2 && $isUser)
				) : ?>
					<?php
						$cls = "";
						if ($this->params->get('access_phone') == 2) 
						{
							$cls .= "private";
						}                     
						if ($this->profile->get("phone") == "" || is_null($this->profile->get("phone")))
						{
							$cls .= ($isUser) ? " hidden" : " hide";
						}
						if (isset($update_missing) && in_array("phone",array_keys($update_missing))) 
						{            
							$cls = str_replace(" hide", "", $cls);
							$cls .= " missing";
						}
					?>
				<li class="profile-phone section <?php echo $cls; ?>">
					<div class="section-content">
						<div class="key"><?php echo JText::_('PLG_MEMBERS_PROFILE_TELEPHONE'); ?></div>
						<?php
							$tel = $this->escape($this->profile->get('phone'));
							//$tel = str_replace(".","-",$tel);
							$tel = str_replace(' ', '-', $tel);
							//$tel = ($tel) ? "<a class=\"phone\" href=\"tel:{$tel}\">{$tel}</a>" : JText::_('Enter your Phone Number');
							$tel = ($tel) ? $tel : JText::_('PLG_MEMBERS_PROFILE_TELEPHONE_ENTER');
						?>
						<div class="value"><?php echo $tel; ?></div>
						<br class="clear" />
						<?php
							ximport('Hubzero_Plugin_View');
							$editview = new Hubzero_Plugin_View(
								array(
									'folder'  => 'members',
									'element' => 'profile',
									'name'    => 'edit'
								)
							);
							$editview->registration_field = 'phone';
							$editview->profile_field = 'phone';
							$editview->title = JText::_('PLG_MEMBERS_PROFILE_TELEPHONE');
							$editview->profile = $this->profile;
							$editview->isUser = $isUser;
							$editview->inputs = '<label>' . JText::_('PLG_MEMBERS_PROFILE_TELEPHONE') . ' <input type="text" class="input-text" name="phone" id="profile-phone" value="'.$this->escape($this->profile->get('phone')) .'" /></label>';
							$editview->access = '<label>' . JText::_('PLG_MEMBERS_PROFILE_PRIVACY') . MembersHtml::selectAccess('access[phone]',$this->params->get('access_phone'),'input-select') . '</label>';
							$editview->display();
						?>
					</div>
					<?php if($isUser) : ?>
						<div class="section-edit">
							<a class="edit-profile-section" href="#">
								<?php echo JText::_('PLG_MEMBERS_PROFILE_EDIT'); ?>
							</a>
						</div>
					<?php endif; ?>
				</li>
			<?php endif; ?>
		<?php endif; ?>
		
		<?php if ($this->registration->address != REG_HIDE) : ?>
			<?php if ($this->params->get('access_address') == 0 
			 		|| ($this->params->get('access_address') == 1 && $loggedin) 
			 		|| ($this->params->get('access_address') == 2 && $isUser)
				) : ?>
				<?php
					//get member addresses
					$membersAddress = new MembersAddress( JFactory::getDBO() );
					$addresses = $membersAddress->getAddressesForMember( $this->profile->get("uidNumber") );
				
					$cls = "";
					if ($this->params->get('access_address') == 2) 
					{
						$cls .= "private";
					}
					if (count($addresses) < 1)
					{
						$cls .= ($isUser) ? " hidden" : " hide";
					}
					if (isset($update_missing) && in_array('address', array_keys($update_missing))) 
					{
						$cls = str_replace(" hide", '', $cls);
						$cls .= " missing";
					}
				?>
				<li class="profile-address section <?php echo $cls; ?>">
					<div class="section-content">
						<div class="key">
							<?php echo JText::_('PLG_MEMBERS_PROFILE_ADDRESS'); ?>
						</div>
						<div class="value">
							<?php echo $membersAddress->formatAddressesForProfile( $addresses, $isUser ); ?>
						</div>
						<br class="clear" />
						<?php
							ximport('Hubzero_Plugin_View');
							$editview = new Hubzero_Plugin_View(
								array(
									'folder'  => 'members',
									'element' => 'profile',
									'name'    => 'edit'
								)
							);
							
							$addAddressLink = '<a class="btn add add-address" href="'.JRoute::_('index.php?option=com_members&id='.JFactory::getUser()->get('id').'&active=profile&action=addaddress').'">' . JText::_('PLG_MEMBERS_PROFILE_ADDRESS_ADD') . '</a>';
							
							$editview->registration_field = "address";
							$editview->profile_field = "address";
							$editview->title = JText::_('test');
							$editview->profile = $this->profile;
							$editview->isUser = $isUser;
							$editview->inputs = '<label for="profile_bio">' . JText::_('PLG_MEMBERS_PROFILE_ADDRESS') . '<br />'.$addAddressLink.'</label>';
							$editview->access = '<label>' . JText::_('PLG_MEMBERS_PROFILE_PRIVACY') . MembersHtml::selectAccess('access[address]',$this->params->get('access_address'),'input-select') . '</label>';
							$editview->display();
						?>
					</div>
					<?php if($isUser) : ?>
						<div class="section-edit">
							<a class="edit-profile-section" href="#">
								<?php echo JText::_('PLG_MEMBERS_PROFILE_EDIT'); ?>
							</a>
						</div>
					<?php endif; ?>
				</li>
			<?php endif; ?>
		<?php endif; ?>
	
		<?php if ($this->params->get('access_bio') == 0 
		 		|| ($this->params->get('access_bio') == 1 && $loggedin) 
		 		|| ($this->params->get('access_bio') == 2 && $isUser)
			) : ?>
				<?php
					$cls = "";
					if($this->params->get('access_bio') == 2) 
					{
						$cls .= "private";
					}                     
					if($this->profile->get("bio") == "" || is_null($this->profile->get("bio")))
					{
						$cls .= ($isUser) ? " hidden" : " hide";
					}
					if(isset($update_missing) && in_array("bio",array_keys($update_missing))) 
					{            
						$cls = str_replace(" hide", "", $cls);
						$cls .= " missing";
					}
				?>
			<li class="profile-bio section <?php echo $cls; ?>">
				<div class="section-content">
					<div class="key"><?php echo JText::_('PLG_MEMBERS_PROFILE_BIOGRAPHY'); ?></div>
					<?php
						if ($this->profile->get('bio')) {
							$wikiconfig = array(
								'option'   => $this->option,
								'scope'    => 'members'.DS.'profile',
								'pagename' => 'member',
								'pageid'   => 0,
								'filepath' => '',
								'domain'   => '' 
							);
							ximport('Hubzero_Wiki_Parser');
							$p = Hubzero_Wiki_Parser::getInstance();
							$bio = $p->parse(stripslashes($this->profile->get('bio')), $wikiconfig, false);
						}
						else
						{
							$bio = JText::_('PLG_MEMBERS_PROFILE_BIOGRAPHY_ENTER');
						}
					?>
					<div class="value"><?php echo $bio; ?></div>
					<br class="clear" />
					
					<?php
						ximport('Hubzero_Plugin_View');
						$editview = new Hubzero_Plugin_View(
							array(
								'folder'  => 'members',
								'element' => 'profile',
								'name'    => 'edit'
							)
						);
					
						ximport('Hubzero_Wiki_Editor');
						$editor = Hubzero_Wiki_Editor::getInstance();
					
						$bio = $editor->display('profile[bio]', 'profile_bio', stripslashes($this->profile->get('bio')), '', '100', '15'); 
					
						$editview->registration_field = "bio";
						$editview->profile_field = "bio";
						$editview->title = JText::_('Biography');
						$editview->profile = $this->profile;
						$editview->isUser = $isUser;
						$editview->inputs = '<label for="profile_bio">' . JText::_('PLG_MEMBERS_PROFILE_BIOGRAPHY') . $bio . '</label>';
						$editview->access = '<label>' . JText::_('PLG_MEMBERS_PROFILE_PRIVACY') . MembersHtml::selectAccess('access[bio]',$this->params->get('access_bio'),'input-select') . '</label>';
						$editview->display();
					?>
				</div>
				<?php if($isUser) : ?>
					<div class="section-edit">
						<a class="edit-profile-section" href="#">
							<?php echo JText::_('PLG_MEMBERS_PROFILE_EDIT'); ?>
						</a>
					</div>
				<?php endif; ?>
			</li>
		<?php endif; ?>
	
		<?php if ($this->registration->Interests != REG_HIDE) : ?>
			<?php if ($this->params->get('access_tags') == 0 
			 		|| ($this->params->get('access_tags') == 1 && $loggedin) 
			 		|| ($this->params->get('access_tags') == 2 && $isUser)
				) : ?>
				<?php
					$cls = "";
					$database = JFactory::getDBO();
					$mt = new MembersTags( $database );
					$tags = $mt->get_tag_cloud(0,0,$this->profile->get('uidNumber'));
					$tag_string = $mt->get_tag_string( $this->profile->get('uidNumber') );

					if ($this->params->get('access_tags') == 2) 
					{
						$cls .= "private";
					}
					if ($tag_string == "")
					{
						$cls .= ($isUser) ? " hidden" : " hide";
					}
					if (isset($update_missing) && in_array("interests",array_keys($update_missing))) 
					{
						$cls = str_replace(" hide", "", $cls);
						$cls .= " missing";
					}
				?>
				<li class="profile-interests section <?php echo $cls; ?>">
					<div class="section-content">
						<div class="key"><?php echo JText::_('PLG_MEMBERS_PROFILE_INTERESTS'); ?></div>
						<div class="value">
							<?php echo ($tags) ? $tags : JText::_('PLG_MEMBERS_PROFILE_INTERESTS_ENTER'); ?>
						</div>
						<br class="clear" />
						<?php
							ximport('Hubzero_Plugin_View');
							$editview = new Hubzero_Plugin_View(
								array(
									'folder'  => 'members',
									'element' => 'profile',
									'name'    => 'edit'
								)
							);
						
							JPluginHelper::importPlugin( 'hubzero' );
							$dispatcher = JDispatcher::getInstance();
							$tf = $dispatcher->trigger( 'onGetMultiEntry', array(array('tags', 'tags', 'actags','',stripslashes($tag_string))) );
						
							if (count($tf) > 0) 
							{
								$interests = $tf[0];
							} else 
							{
								$interests = "\t\t\t".'<input type="text" name="tags" value="'. $this->escape($tag_string) .'" />'."\n";
							}

							$editview->registration_field = 'interests';
							$editview->profile_field = 'interests';
							$editview->title = JText::_('PLG_MEMBERS_PROFILE_INTERESTS');
							$editview->profile = $this->profile;
							$editview->isUser = $isUser;
							$editview->inputs = '<label>' . JText::_('PLG_MEMBERS_PROFILE_INTERESTS') . $interests . '</label>';
							$editview->access = '<label>' . JText::_('PLG_MEMBERS_PROFILE_PRIVACY') . MembersHtml::selectAccess('access[tags]',$this->params->get('access_tags'),'input-select') . '</label>';
							$editview->display();
						?>
					</div>
					<?php if($isUser) : ?>
						<div class="section-edit">
							<a class="edit-profile-section" href="#">
								<?php echo JText::_('PLG_MEMBERS_PROFILE_EDIT'); ?>
							</a>
						</div>
					<?php endif; ?>
				</li>
			<?php endif; ?>
		<?php endif; ?>
	
		<?php if ($this->registration->Citizenship != REG_HIDE) : ?>
			<?php if ($this->params->get('access_countryorigin') == 0 
			 		|| ($this->params->get('access_countryorigin') == 1 && $loggedin) 
			 		|| ($this->params->get('access_countryorigin') == 2 && $isUser)
				) : ?>
					<?php
						$cls = "";
						if ($this->params->get('access_countryorigin') == 2) 
						{
							$cls .= "private";
						}                     
						if ($this->profile->get("countryorigin") == '' || is_null($this->profile->get("countryorigin")))
						{
							$cls .= ($isUser) ? " hidden" : " hide";
						}
						if (isset($update_missing) && in_array("countryorigin",array_keys($update_missing))) 
						{            
							$cls = str_replace(" hide", '', $cls);
							$cls .= " missing";
						}
					?>
				<li class="profile-countryorigin section <?php echo $cls; ?>">
					<div class="section-content">
						<div class="key"><?php echo JText::_('PLG_MEMBERS_PROFILE_CITIZENSHIP'); ?></div>
						<?php
							$img = '';
							if (is_file(JPATH_ROOT.DS.'components'.DS.$this->option.DS.'images'.DS.'flags'.DS.strtolower($this->profile->get('countryorigin')).'.gif')) {
								$img = '<img src="' . rtrim(JURI::getInstance()->base(true), '/') . '/components/'.$this->option.'/images/flags/'.strtolower($this->profile->get('countryorigin')).'.gif" alt="'.$this->escape($this->profile->get('countryorigin')).' '.JText::_('PLG_MEMBERS_PROFILE_FLAG').'" /> ';
							}
							$citizenship = $img . strtoupper($this->escape($this->profile->get('countryorigin')));
						?>
						<div class="value">
							<?php echo ($citizenship) ? $citizenship : JText::_('PLG_MEMBERS_PROFILE_CITIZENSHIP_ENTER'); ?>
						</div>
						<br class="clear" />

						<?php
							ximport('Hubzero_Plugin_View');
							$editview = new Hubzero_Plugin_View(
								array(
									'folder'  => 'members',
									'element' => 'profile',
									'name'    => 'edit'
								)
							);

							ximport('Hubzero_Geo');
							$co = Hubzero_Geo::getcountries();

							$countries  = '<select name="corigin" id="corigin" class="input-select">';
							$countries .= '<option value="">'.JText::_('PLG_MEMBERS_PROFILE_SELECT').'</option>';
							foreach ($co as $c)
							{
								if ($c['code'] != 'US') 
								{
									$countries .= '<option value="' . $c['code'] . '"';
									if ($this->profile->get('countryorigin') == $c['code']) 
									{
										$countries .= ' selected="selected"';
									}
									$countries .= '>' . $this->escape($c['name']) . '</option>';
								}
							}
							$countries .= '</select>';

							$yes = ""; $no = "";
							if(strcasecmp($this->profile->get('countryorigin'),'US') == 0)
							{
								$yes = 'checked="checked"';
							}
							elseif($this->profile->get('countryorigin') != "" && (strcasecmp($this->profile->get('countryorigin'),'US') != 0) ) 
							{
								$no = 'checked="checked"';
							}

							$citizenship  = '<br /><input type="radio" name="corigin_us" id="corigin_usyes" value="yes" '.$yes.' /> ' . JText::_('PLG_MEMBERS_PROFILE_YES') . ' &nbsp;&nbsp;&nbsp;';
							$citizenship .= '<input type="radio" name="corigin_us" id="corigin_usno" value="no" '.$no.' /> ' . JText::_('PLG_MEMBERS_PROFILE_NO') . ' ';

							$editview->registration_field = "countryorigin";
							$editview->profile_field = "countryorigin";
							$editview->title = JText::_('Citizenship');
							$editview->profile = $this->profile;
							$editview->isUser = $isUser;
							$editview->inputs  = '<label class="side-by-side" for="123">' . JText::_('PLG_MEMBERS_PROFILE_CITIZEN_OF_USA') . $citizenship . '</label>';
							$editview->inputs .= '<label class="side-by-side no-padding-right" for="corigin">'.JText::_('PLG_MEMBERS_PROFILE_CITIZEN_OR_RESIDENT') . $countries . '</label>';
							$editview->access = '<label>' . JText::_('PLG_MEMBERS_PROFILE_PRIVACY') . MembersHtml::selectAccess('access[countryorigin]', $this->params->get('access_countryorigin'), 'input-select') . '</label>';
							$editview->display();
						?>
					</div>
					<?php if ($isUser) : ?>
						<div class="section-edit">
							<a class="edit-profile-section" href="#">
								<?php echo JText::_('PLG_MEMBERS_PROFILE_EDIT'); ?>
							</a>
						</div>
					<?php endif; ?>
				</li>
			<?php endif; ?>
		<?php endif; ?>
	
		<?php if ($this->registration->Residency != REG_HIDE) : ?>
			<?php if ($this->params->get('access_countryresident') == 0 
			 		|| ($this->params->get('access_countryresident') == 1 && $loggedin) 
			 		|| ($this->params->get('access_countryresident') == 2 && $isUser)
				) : ?>
					<?php
						$cls = "";
						if ($this->params->get('access_countryresident') == 2) 
						{
							$cls .= "private";
						}                     
						if ($this->profile->get("countryresident") == "" || is_null($this->profile->get("countryresident")))
						{
							$cls .= ($isUser) ? " hidden" : " hide";
						}
						if (isset($update_missing) && in_array("countryresident", array_keys($update_missing))) 
						{            
							$cls = str_replace(" hide", "", $cls);
							$cls .= " missing";
						}
					?>
				<li class="profile-countryresident section <?php echo $cls; ?>">
					<div class="section-content">
						<div class="key"><?php echo JText::_('PLG_MEMBERS_PROFILE_RESIDENCE'); ?></div>
						<?php
							$img = '';
							if (is_file(JPATH_ROOT.DS.'components'.DS.$this->option.DS.'images'.DS.'flags'.DS.strtolower($this->profile->get('countryresident')).'.gif')) {
								$img = '<img src="' . rtrim(JURI::getInstance()->base(true), '/') . '/components/'.$this->option.'/images/flags/'.strtolower($this->profile->get('countryresident')).'.gif" alt="'.$this->profile->get('countryresident').' '.JText::_('PLG_MEMBERS_PROFILE_FLAG').'" /> ';
							}
							$residence = $img . strtoupper($this->escape($this->profile->get('countryresident')));
						?>
						<div class="value">
							<?php echo ($residence) ? $residence : JText::_('PLG_MEMBERS_PROFILE_RESIDENCE_ENTER'); ?>
						</div>
						<br class="clear" />
						<?php
							ximport('Hubzero_Plugin_View');
							$editview = new Hubzero_Plugin_View(
								array(
									'folder'  => 'members',
									'element' => 'profile',
									'name'    => 'edit'
								)
							);
						
							ximport('Hubzero_Geo');
							$co = Hubzero_Geo::getcountries();
						
							$countries = '<select name="cresident" id="cresident" class="input-select">';
							$countries .= '<option value="">'.JText::_('PLG_MEMBERS_PROFILE_SELECT').'</option>';
							foreach ($co as $c)
							{
								if ($c['code'] != 'US') 
								{
									$countries .= '<option value="' . $c['code'] . '"';
									if ($this->profile->get('countryresident') == $c['code']) 
									{
										$countries .= ' selected="selected"';
									}
									$countries .= '>' . $this->escape($c['name']) . '</option>';
								}
							}
							$countries .= '</select>';
						
						
							$yes = ""; $no = "";
							if(strcasecmp($this->profile->get('countryresident'),'US') == 0)
							{
								$yes = 'checked="checked"';
							}
							elseif($this->profile->get('countryresident') != "" && (strcasecmp($this->profile->get('countryresident'),'US') != 0) ) 
							{
								$no = 'checked="checked"';
							}
						 
							$citizenship  = '<br /><input type="radio" name="cresident_us" id="cresident_usyes" value="yes" '.$yes.' /> ' . JText::_('PLG_MEMBERS_PROFILE_YES') . ' &nbsp;&nbsp;&nbsp;';
							$citizenship .= '<input type="radio" name="cresident_us" id="cresident_usno" value="no" '.$no.' /> ' . JText::_('PLG_MEMBERS_PROFILE_NO') . ' ';

							$editview->registration_field = "countryresident";
							$editview->profile_field = "countryresident";
							$editview->title = JText::_('Residence');
							$editview->profile = $this->profile;
							$editview->isUser = $isUser;
							$editview->inputs = '<label class="side-by-side" for="456">' . JText::_('PLG_MEMBERS_PROFILE_RESIDENCE_CURRENTLY_IN_USA') . $citizenship.'</label>';
							$editview->inputs .= '<label class="side-by-side no-padding-right">'.JText::_('PLG_MEMBERS_PROFILE_RESIDENCE_CURRENTLY_LIVING_IN').$countries.'</label>';
							$editview->access = '<label>' . JText::_('PLG_MEMBERS_PROFILE_PRIVACY') . MembersHtml::selectAccess('access[countryresident]',$this->params->get('access_countryresident'),'input-select') . '</label>';
							$editview->display();
						?>
					</div>
					<?php if($isUser) : ?>
						<div class="section-edit">
							<a class="edit-profile-section" href="#">
								<?php echo JText::_('PLG_MEMBERS_PROFILE_EDIT'); ?>
							</a>
						</div>
					<?php endif; ?>
				</li>
			<?php endif; ?>
		<?php endif; ?>
	
		<?php if ($this->registration->Sex != REG_HIDE) : ?>
			<?php if ($this->params->get('access_gender') == 0 
			 		|| ($this->params->get('access_gender') == 1 && $loggedin) 
			 		|| ($this->params->get('access_gender') == 2 && $isUser)
				) : ?>
					<?php
						$cls = "";
						if($this->params->get('access_gender') == 2) 
						{
							$cls .= "private";
						}                     
						if($this->profile->get("gender") == "" || is_null($this->profile->get("gender")))
						{
							$cls .= ($isUser) ? " hidden" : " hide";
						}
						if(isset($update_missing) && in_array("sex",array_keys($update_missing))) 
						{            
							$cls = str_replace(" hide", "", $cls);
							$cls .= " missing";
						}
					?>
				<li class="profile-sex section <?php echo $cls; ?>">
					<div class="section-content">
						<div class="key"><?php echo JText::_('PLG_MEMBERS_PROFILE_GENDER'); ?></div>
						<div class="value">
							<?php 
								$gender = MembersHtml::propercase_singleresponse($this->profile->get('gender')); 
								echo ($gender != 'n/a') ? $gender : JText::_('PLG_MEMBERS_PROFILE_GENDER_ENTER');
							?>
						</div>
						<br class="clear" />
						
						<?php
							ximport('Hubzero_Plugin_View');
							$editview = new Hubzero_Plugin_View(
								array(
									'folder'  => 'members',
									'element' => 'profile',
									'name'    => 'edit'
								)
							);
						
							$sexes = array(
								'male'    => JText::_('PLG_MEMBERS_PROFILE_GENDER_OPT_MALE'),
								'female'  => JText::_('PLG_MEMBERS_PROFILE_GENDER_OPT_FEMALE'),
								'refused' => JText::_('PLG_MEMBERS_PROFILE_GENDER_OPT_REFUSED')
							);
						
							$sex = '<select name="sex" class="input-select">';
							//$sex .= '<option value="unspecified">Unspecified</option>';
							foreach($sexes as $k=>$v)
							{   
								$sel = ($k == $this->profile->get('gender')) ? 'selected="selected"' : '';
								$sex .= '<option '.$sel.' value="'.$k.'">'.$v.'</option>';
							}
							$sex .= '</select>';

							$editview->registration_field = "sex";
							$editview->profile_field = "gender";
							$editview->title = JText::_('Gender');
							$editview->profile = $this->profile;
							$editview->isUser = $isUser;
							$editview->inputs = '<label>' . JText::_('PLG_MEMBERS_PROFILE_GENDER') . $sex . '</label>';
							$editview->access = '<label>' . JText::_('PLG_MEMBERS_PROFILE_PRIVACY') . MembersHtml::selectAccess('access[gender]',$this->params->get('access_gender'),'input-select') . '</label>';
							$editview->display();
						?>
					</div>
					<?php if($isUser) : ?>
						<div class="section-edit">
							<a class="edit-profile-section" href="#">
								<?php echo JText::_('PLG_MEMBERS_PROFILE_EDIT'); ?>
							</a>
						</div>
					<?php endif; ?>
				</li>
			<?php endif; ?>
		<?php endif; ?>
	
		<?php if ($this->registration->Disability != REG_HIDE) : ?>
			<?php if ($this->params->get('access_disability') == 0 
			 		|| ($this->params->get('access_disability') == 1 && $loggedin) 
			 		|| ($this->params->get('access_disability') == 2 && $isUser)
				) : ?>
				<?php
					$cls = "";
					if($this->params->get('access_disability') == 2) 
					{
						$cls .= "private";
					}                     
					if($this->profile->get("disability") == "" || is_null($this->profile->get("disability")) || count($this->profile->get("disability")) < 1)
					{
						$cls .= ($isUser) ? " hidden" : " hide";
					}
					if(isset($update_missing) && in_array("disability",array_keys($update_missing))) 
					{            
						$cls = str_replace(" hide", "", $cls);
						$cls .= " missing";
					}                                  
					//dont show meant for stats only
					$cls .= (!$isUser) ? " hide" : "" ;
				?>
				<li class="profile-disability section <?php echo $cls; ?>">
					<div class="section-content">
						<div class="key"><?php echo JText::_('PLG_MEMBERS_PROFILE_DISABILITY'); ?></div>
						<div class="value">
							<?php 
								$disability = MembersHtml::propercase_multiresponse($this->profile->get('disability')); 
								echo ($disability != 'n/a') ? $disability : JText::_('PLG_MEMBERS_PROFILE_DISABILITY_ENTER');
							?>
						</div>
						<br class="clear" />
						
						<?php
							ximport('Hubzero_Plugin_View');
							$editview = new Hubzero_Plugin_View(
								array(
									'folder'  => 'members',
									'element' => 'profile',
									'name'    => 'edit'
								)
							);
						
							$disabilities = $this->profile->get('disability');
							if (!is_array($disabilities)) {
								$disabilities = array();
							}

							$disabilityyes = false;
							$disabilityother = '';
							foreach ($disabilities as $disabilityitem)
							{
								if ($disabilityitem != 'no'
								 && $disabilityitem != 'refused') {
									if (!$disabilityyes) {
										$disabilityyes = true;
									}

									if ($disabilityitem != 'blind'
									 && $disabilityitem != 'deaf'
									 && $disabilityitem != 'physical'
									 && $disabilityitem != 'learning'
									 && $disabilityitem != 'vocal'
									 && $disabilityitem != 'yes') {
										$disabilityother = $disabilityitem;
									}
								}
							}
						
							$disability_html = "";
						
							$disability_html .= "\t\t".'<fieldset class="sub">'."\n";
							$disability_html .= "\t\t\t\t".'<label><input type="radio" class="option" name="disability" id="disabilityyes" value="yes"';
							if ($disabilityyes) {
								$disability_html .= ' checked="checked"';
							}
							$disability_html .= ' /> ' . JText::_('PLG_MEMBERS_PROFILE_YES') . '</label>'."\n";
							$disability_html .= "\t\t\t".'<fieldset class="sub-sub">'."\n";
							$disability_html .= "\t\t\t\t".'<label><input type="checkbox" class="option" name="disabilityblind" id="disabilityblind" ';
							if (in_array('blind', $disabilities)) {
								$disability_html .= 'checked="checked" ';
							}
							$disability_html .= '/> '.JText::_('PLG_MEMBERS_PROFILE_DISABILITY_OPT_VISUAL').'</label>'."\n";
							$disability_html .= "\t\t\t\t".'<label><input type="checkbox" class="option" name="disabilitydeaf" id="disabilitydeaf" ';
							if (in_array('deaf', $disabilities)) {
								$disability_html .= 'checked="checked" ';
							}
						   	$disability_html .= '/> '.JText::_('PLG_MEMBERS_PROFILE_DISABILITY_OPT_HEARING').'</label>'."\n";
							$disability_html .= "\t\t\t\t".'<label><input type="checkbox" class="option" name="disabilityphysical" id="disabilityphysical" ';
							if (in_array('physical', $disabilities)) {
								$disability_html .= 'checked="checked" ';
							}
							$disability_html .= '/> '.JText::_('PLG_MEMBERS_PROFILE_DISABILITY_OPT_PHYSICAL').'</label>'."\n";
							$disability_html .= "\t\t\t\t".'<label><input type="checkbox" class="option" name="disabilitylearning" id="disabilitylearning" ';
							if (in_array('learning', $disabilities)) {
								$disability_html .= 'checked="checked" ';
							}
							$disability_html .= '/> '.JText::_('PLG_MEMBERS_PROFILE_DISABILITY_OPT_COGNITIVE').'</label>'."\n";
							$disability_html .= "\t\t\t\t".'<label><input type="checkbox" class="option" name="disabilityvocal" id="disabilityvocal" ';
							if (in_array('vocal', $disabilities)) {
							   	$disability_html .= 'checked="checked" ';
							}
							$disability_html .= '/> '.JText::_('PLG_MEMBERS_PROFILE_DISABILITY_OPT_VOCAL').'</label>'."\n";
							$disability_html .= "\t\t\t\t".'<label>'.JText::_('PLG_MEMBERS_PROFILE_OPT_OTHER').':'."\n";
							$disability_html .= "\t\t\t\t".'<input name="disabilityother" class="input-text" id="disabilityother" type="text" value="'. $this->escape($disabilityother) .'" /></label>'."\n";
							$disability_html .= "\t\t\t".'</fieldset>'."\n";
							$disability_html .= "\t\t\t".'<label><input type="radio" class="option" name="disability" id="disabilityno" value="no"';
							if (in_array('no', $disabilities)) {
								$disability_html .= ' checked="checked"';
							}
							$disability_html .= '> '.JText::_('PLG_MEMBERS_PROFILE_NO_NONE').'</label>'."\n";
							$disability_html .= "\t\t\t".'<label><input type="radio" class="option" name="disability" id="disabilityrefused" value="refused"';
							if (in_array('refused', $disabilities)) {
								$disability_html .= ' checked="checked"';
							}
							$disability_html .= '> '.JText::_('PLG_MEMBERS_PROFILE_OPT_REFUSED').'</label>'."\n";
							$disability_html .= "\t\t".'</fieldset>'."\n";

							$editview->registration_field = 'disability';
							$editview->profile_field = 'disability';
							$editview->title = JText::_('PLG_MEMBERS_PROFILE_DISABILITY');
							$editview->profile = $this->profile;
							$editview->isUser = $isUser;
							$editview->inputs = '<label>' . JText::_('PLG_MEMBERS_PROFILE_DISABILITY') . $disability_html . '</label>';
							$editview->access = '<label>' . JText::_('PLG_MEMBERS_PROFILE_PRIVACY') . MembersHtml::selectAccess('access[disability]',$this->params->get('access_disability'),'input-select') . '</label>';
							$editview->display();
						?>
					</div>
					<?php if($isUser) : ?>
						<div class="section-edit">
							<a class="edit-profile-section" href="#">
								<?php echo JText::_('PLG_MEMBERS_PROFILE_EDIT'); ?>
							</a>
						</div>
					<?php endif; ?>
				</li>
			<?php endif; ?>
		<?php endif; ?>
	
		<?php if ($this->registration->Hispanic != REG_HIDE) : ?>
			<?php if ($this->params->get('access_hispanic') == 0 
			 		|| ($this->params->get('access_hispanic') == 1 && $loggedin) 
			 		|| ($this->params->get('access_hispanic') == 2 && $isUser)
				) : ?>
				<?php
					$cls = "";
					if ($this->params->get('access_hispanic') == 2) 
					{
						$cls .= "private";
					}                     
					if ($this->profile->get("hispanic") == "" || is_null($this->profile->get("hispanic")) || count($this->profile->get("hispanic")) < 1)
					{
						$cls .= ($isUser) ? " hidden" : " hide";
					}
					if (isset($update_missing) && in_array("hispanic",array_keys($update_missing))) 
					{            
						$cls = str_replace(" hide", "", $cls);
						$cls .= " missing";
					}
					//dont show meant for stats only
					$cls .= (!$isUser) ? " hide" : "" ;
				?>
				<li class="profile-hispanic section <?php echo $cls; ?>">
					<div class="section-content">
						<div class="key"><?php echo JText::_('PLG_MEMBERS_PROFILE_HISPANIC'); ?></div>
						<div class="value">
							<?php
								$hispanic = MembersHtml::propercase_multiresponse($this->profile->get('hispanic')); 
								echo ($hispanic != 'n/a') ? $hispanic : JText::_('PLG_MEMBERS_PROFILE_HISPANIC_ENTER');
							?>
						</div>
						<br class="clear" />
						
						<?php
							ximport('Hubzero_Plugin_View');
							$editview = new Hubzero_Plugin_View(
								array(
									'folder'  => 'members',
									'element' => 'profile',
									'name'    => 'edit'
								)
							);
						
							$hispanic = $this->profile->get('hispanic');
							if (!is_array($hispanic)) {
								$hispanic = array();
							}

							$hispanicyes = false;
							$hispanicother = '';
							foreach ($hispanic as $hispanicitem)
							{
								if ($hispanicitem != 'no'
								 && $hispanicitem != 'refused') {
									if (!$hispanicyes) {
										$hispanicyes = true;
									}

									if ($hispanicitem != 'cuban'
									 && $hispanicitem != 'mexican'
									 && $hispanicitem != 'puertorican') {
										$hispanicother = $hispanicitem;
									}
								}
							}
						
							$hispanic_html = "";
							$hispanic_html .= "\t\t".'<fieldset class="sub">'."\n";
							$hispanic_html .= "\t\t\t\t".'<label><input type="radio" class="option" name="hispanic" id="hispanicyes" value="yes" ';
							if ($hispanicyes) {
								$hispanic_html .= 'checked="checked"';
							}
							$hispanic_html .= ' /> '.JText::_('PLG_MEMBERS_PROFILE_HISPANIC_OPT_YES').'</label>'."\n";
							$hispanic_html .= "\t\t\t".'<fieldset class="sub-sub">'."\n";
							$hispanic_html .= "\t\t\t\t".'<label><input type="checkbox" class="option" name="hispaniccuban" id="hispaniccuban" ';
							if (in_array('cuban', $hispanic)) {
								$hispanic_html .= 'checked="checked" ';
							}
							$hispanic_html .= '/> '.JText::_('PLG_MEMBERS_PROFILE_HISPANIC_OPT_CUBAN').'</label>'."\n";
							$hispanic_html .= "\t\t\t\t".'<label><input type="checkbox" class="option" name="hispanicmexican" id="hispanicmexican" ';
							if (in_array('mexican', $hispanic)) {
								$hispanic_html .= 'checked="checked" ';
							}
							$hispanic_html .= '/> '.JText::_('PLG_MEMBERS_PROFILE_HISPANIC_OPT_MEXICAN').'</label>'."\n";
							$hispanic_html .= "\t\t\t\t".'<label><input type="checkbox" class="option" name="hispanicpuertorican" id="hispanicpuertorican" ';
							if (in_array('puertorican', $hispanic)) {
								$hispanic_html .= 'checked="checked" ';
							}
							$hispanic_html .= '/> '.JText::_('PLG_MEMBERS_PROFILE_HISPANIC_OPT_PEURTORICAN').'</label>'."\n";
							$hispanic_html .= "\t\t\t\t".'<label>'.JText::_('PLG_MEMBERS_PROFILE_HISPANIC_OPT_OTHER')."\n";
							$hispanic_html .= "\t\t\t\t".'<input name="hispanicother" class="input-text" id="hispanicother" type="text" value="'. $this->escape($hispanicother) .'" /></label>'."\n";
							$hispanic_html .= "\t\t\t".'</fieldset>'."\n";
							$hispanic_html .= "\t\t\t".'<label><input type="radio" class="option" name="hispanic" id="hispanicno" value="no"';
							if (in_array('no', $hispanic)) {
								$hispanic_html .= ' checked="checked"';
							}
							$hispanic_html .= '> '.JText::_('PLG_MEMBERS_PROFILE_HISPANIC_OPT_NO').'</label>'."\n";
							$hispanic_html .= "\t\t\t".'<label><input type="radio" class="option" name="hispanic" id="hispanicrefused" value="refused"';
							if (in_array('refused', $hispanic)) {
								$hispanic_html .= ' checked="checked"';
							}
							$hispanic_html .= '> '.JText::_('PLG_MEMBERS_PROFILE_OPT_REFUSED').'</label>'."\n";
							$hispanic_html .= "\t\t".'</fieldset>'."\n";

							$editview->registration_field = 'hispanic';
							$editview->profile_field = 'hispanic';
							$editview->title = JText::_('PLG_MEMBERS_PROFILE_HISPANIC');
							$editview->profile = $this->profile;
							$editview->isUser = $isUser;
							$editview->inputs = '<label>' . JText::_('PLG_MEMBERS_PROFILE_HISPANIC') . $hispanic_html . '</label>';
							$editview->access = '<label>' . JText::_('PLG_MEMBERS_PROFILE_PRIVACY') . MembersHtml::selectAccess('access[hispanic]',$this->params->get('access_hispanic'),'input-select') . '</label>';
							$editview->display();
						?>
					</div>
					<?php if($isUser) : ?>
						<div class="section-edit">
							<a class="edit-profile-section" href="#">
							<?php echo JText::_('PLG_MEMBERS_PROFILE_EDIT'); ?>
						</a>
						</div>
					<?php endif; ?>
				</li>
			<?php endif; ?>
		<?php endif; ?>
	
		<?php if ($this->registration->Race != REG_HIDE) : ?>
			<?php if ($this->params->get('access_race') == 0 
			 		|| ($this->params->get('access_race') == 1 && $loggedin) 
			 		|| ($this->params->get('access_race') == 2 && $isUser)
				) : ?>
				<?php
					$cls = "";
					if ($this->params->get('access_race') == 2) 
					{
						$cls .= "private";
					}
					if ($this->profile->get("race") == "" || is_null($this->profile->get("race")) || count($this->profile->get("race")) < 1)
					{
						$cls .= ($isUser) ? " hidden" : " hide";
					}
					if (isset($update_missing) && in_array("race",array_keys($update_missing))) 
					{
						$cls = str_replace(" hide", "", $cls);
						$cls .= " missing";
					}
					//dont show meant for stats only
					$cls .= (!$isUser) ? " hide" : "" ;
				?>
				<li class="profile-race section <?php echo $cls; ?>">
					<div class="section-content">
						<div class="key"><?php echo JText::_('PLG_MEMBERS_PROFILE_RACE'); ?></div>
						<div class="value">
							<?php 
								$race = MembersHtml::propercase_multiresponse($this->profile->get('race')); 
								echo ($race != 'n/a') ? $race : JText::_('PLG_MEMBERS_PROFILE_RACE_ENTER');
							?>
						</div>
						<br class="clear" />
						
						<?php
							ximport('Hubzero_Plugin_View');
							$editview = new Hubzero_Plugin_View(
								array(
									'folder'  => 'members',
									'element' => 'profile',
									'name'    => 'edit'
								)
							);
						
							$race = $this->profile->get('race');
							if (!is_array($race)) {
								$race = array();
							}
						
							$race_html = "";
							$race_html .= "\t\t".'<fieldset class="sub">'."\n";
							$race_html .= "\t\t\t".'<p class="hint">'.JText::_('PLG_MEMBERS_PROFILE_SELECT_MULTIPLE').'</p>'."\n";

							$race_html .= "\t\t\t".'<label><input type="checkbox" class="option" name="racenativeamerican" id="racenativeamerican" value="1" ';
							if (in_array('nativeamerican', $race)) {
								$race_html .= 'checked="checked" ';
							}
							$race_html .= '/> '.JText::_('PLG_MEMBERS_PROFILE_RACE_OPT_NATIVEAMERICAN').'</label>'."\n";
							$race_html .= "\t\t\t".'<label class="indent">'.JText::_('PLG_MEMBERS_PROFILE_RACE_OPT_TRIBE').':'."\n";
							$race_html .= "\t\t\t".'<input name="racenativetribe" class="input-text" id="racenativetribe" type="text" value="'. $this->escape($this->profile->get('nativeTribe')) .'" /></label>'."\n";
							$race_html .= "\t\t\t".'<label><input type="checkbox" class="option" name="raceasian" id="raceasian" value="1" ';
							if (in_array('asian', $race)) {
								$race_html .= 'checked="checked" ';
							}
							$race_html .= '/> '.JText::_('PLG_MEMBERS_PROFILE_RACE_OPT_ASIAN').'</label>'."\n";
							$race_html .= "\t\t\t".'<label><input type="checkbox" class="option" name="raceblack" id="raceblack" value="1" ';
							if (in_array('black', $race)) {
								$race_html .= 'checked="checked" ';
							}
							$race_html .= '/> '.JText::_('PLG_MEMBERS_PROFILE_RACE_OPT_BLACK').'</label>'."\n";
							$race_html .= "\t\t\t".'<label><input type="checkbox" class="option" name="racehawaiian" id="racehawaiian" value="1" ';
							if (in_array('hawaiian', $race)) {
								$race_html .= 'checked="checked" ';
							}
							$race_html .= '/> '.JText::_('PLG_MEMBERS_PROFILE_RACE_OPT_HAWAIIAN').'</label>'."\n";
							$race_html .= "\t\t\t".'<label><input type="checkbox" class="option" name="racewhite" id="racewhite" value="1" ';
							if (in_array('white', $race)) {
								$race_html .= 'checked="checked" ';
							}
							$race_html .= '/> '.JText::_('PLG_MEMBERS_PROFILE_RACE_OPT_WHITE').'</label>'."\n";
							$race_html .= "\t\t\t".'<label><input type="checkbox" class="option" name="racerefused" id="racerefused" value="1" ';
							if (in_array('refused', $race)) {
								$race_html .= 'checked="checked" ';
							}
							$race_html .= '/> '.JText::_('PLG_MEMBERS_PROFILE_OPT_REFUSED').'</label>'."\n";
							$race_html .= "\t\t".'</fieldset>'."\n";

							$editview->registration_field = "race";
							$editview->profile_field = "race";
							$editview->title = JText::_('PLG_MEMBERS_PROFILE_RACE');
							$editview->profile = $this->profile;
							$editview->isUser = $isUser;
							$editview->inputs = '<label for="xxx">' . JText::_('PLG_MEMBERS_PROFILE_RACE') . $race_html.'</label>';
							$editview->access = '<label>' . JText::_('PLG_MEMBERS_PROFILE_PRIVACY') . MembersHtml::selectAccess('access[race]',$this->params->get('access_race'),'input-select') . '</label>';
							$editview->display();
						?>
					</div>
					<?php if($isUser) : ?>
						<div class="section-edit">
							<a class="edit-profile-section" href="#">
							<?php echo JText::_('PLG_MEMBERS_PROFILE_EDIT'); ?>
						</a>
						</div>
					<?php endif; ?>
				</li>
			<?php endif; ?>
		<?php endif; ?>
	
		<?php if ($this->registration->OptIn != REG_HIDE) : ?>
			<?php if ($this->params->get('access_optin') == 0 
			 		|| ($this->params->get('access_optin') == 1 && $loggedin) 
			 		|| ($this->params->get('access_optin') == 2 && $isUser)
				) : ?>
				<?php
					$cls = "";
					if($this->params->get('access_optin') == 2) 
					{
						$cls .= "private";
					}                     
					if($this->profile->get("mailPreferenceOption") == "" || is_null($this->profile->get("mailPreferenceOption")))
					{
						$cls .= ($isUser) ? " hidden" : " hide";
					}
					if(isset($update_missing) && in_array("optin",array_keys($update_missing))) 
					{            
						$cls = str_replace(" hide", "", $cls);
						$cls .= " missing";
					}
					//dont show meant for stats only
					$cls .= (!$isUser) ? ' hide' : '' ;
					
					//get value of mail preference option
					switch($this->profile->get('mailPreferenceOption'))
					{
						case '1':    $mailPreferenceValue = 'Yes, send me emails';          break;
						case '0':    $mailPreferenceValue = 'No, don\'t send me emails';    break;
						case '-1':
						default:     $mailPreferenceValue = 'Unanswered';                   break;
					}
				?>
				<li class="profile-optin section <?php echo $cls; ?>">
					<div class="section-content">
						<div class="key"><?php echo JText::_('PLG_MEMBERS_PROFILE_EMAILUPDATES'); ?></div>
						<div class="value"><?php echo $mailPreferenceValue; ?></div>
						<br class="clear" />
						<?php
							ximport('Hubzero_Plugin_View');
							$editview = new Hubzero_Plugin_View(
								array(
									'folder'  => 'members',
									'element' => 'profile',
									'name'    => 'edit'
								)
							);
							
							//define mail preference options
							$options = array(
								'-1' => JText::_('PLG_MEMBERS_PROFILE_EMAILUPDATES_OPT_SELECT'),
								'1'  => JText::_('PLG_MEMBERS_PROFILE_EMAILUPDATES_OPT_YES'),
								'0'  => JText::_('PLG_MEMBERS_PROFILE_EMAILUPDATES_OPT_NO')
							);
							
							//build option list
							$optin_html  = '<strong>' . JText::_('PLG_MEMBERS_PROFILE_EMAILUPDATES_EXPLANATION') . '</strong>';
							$optin_html .= '<label for="mailPreferenceOption">';
							$optin_html .= '<select name="mailPreferenceOption">';
							foreach ($options as $key => $value)
							{
								$sel = ($key == $this->profile->get('mailPreferenceOption')) ? 'selected="selected"' : '';
								$optin_html .= '<option '.$sel.' value="'. $key .'">' . $value . '</option>';
							}
							$optin_html .= '</select>';
							$optin_html .= '</label>';
							
							$editview->registration_field = 'mailPreferenceOption';
							$editview->profile_field = 'mailPreferenceOption';
							$editview->title =  JText::_('PLG_MEMBERS_PROFILE_EMAILUPDATES');
							$editview->profile = $this->profile;
							$editview->isUser = $isUser;
							$editview->inputs = $optin_html;
							$editview->access = '<label>' . JText::_('PLG_MEMBERS_PROFILE_PRIVACY') . MembersHtml::selectAccess('access[optin]',$this->params->get('access_optin'),'input-select') . '</label>';
							$editview->display();
						?>
					</div>
					<?php if($isUser) : ?>
						<div class="section-edit">
							<a class="edit-profile-section" href="#">
							<?php echo JText::_('PLG_MEMBERS_PROFILE_EDIT'); ?>
						</a>
						</div>
					<?php endif; ?>
				</li>
			<?php endif; ?>
		<?php endif; ?>
	</ul>
</div><!-- /#profile-page-content -->
