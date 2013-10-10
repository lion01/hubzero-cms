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
 * @author    IS <ishunko@purdue.edu>
 * @copyright Copyright 2005-2013 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

/**
 * Cron plugin for support tickets
 */
class plgCronRegister extends JPlugin
{
	/**
	 * Return a list of events
	 * 
	 * @return     array
	 */
	public function onCronEvents()
	{
		$obj = new stdClass();
		$obj->plugin = $this->_name;
		$obj->events = array(
			array(
				'name'   => 'doRegistration',
				'label'  => JText::_('Do the PEC registration fetch'),
				'params' => ''
			)
		);

		return $obj;
	}

	/**
	 * Do the registration
	 * 
	 * @return     array
	 */
	public function doRegistration($params=null)
	{		
		// Settings
		$file = JPATH_PLUGINS . DS . "cron" . DS . "register" . DS . "register.ini";
		$settings = parse_ini_file($file);
		
		// Set fetch from date limit
		$fetchFrom = $settings['lastUpdated'];
		// Test overrride
		$fetchFrom = '8/8/2013';
		
		// Update lastUpdated to today
		$settings['lastUpdated'] = date('j/n/Y');
		$this->write_php_ini($settings, $file);
		
		// Get all offerings with associated PEC IDs (section_id);
		$database =& JFactory::getDBO();
		
		$sql = "SELECT `id`, `course_id`, `params` FROM `#__courses_offerings` WHERE `state` = 1 AND `params` LIKE '%pec_register=1%'";
		$database->setQuery($sql);
		$activeOfferings = $database->loadObjectList();
		
		if (sizeof($activeOfferings) < 1)
		{
			return true;
		}
				
		// Get registratinos from PEC		
		$curl_result = '';
		$curl_err = '';
		
		$url = 'https://dev.pec.purdue.edu/reports/ce/Premis/PremisService.asmx/GetEnrollmentsByAttributeByDateJSON';
		
		
		$data['dateInput'] = $fetchFrom;
		$data['attribute_ans'] = 'purdue next';
	
		$req = 'password=Purdue_Next_@_Service!';
		
		foreach ($data as $key => $value) 
		{
			$value = urlencode(stripslashes($value));
			$req .= "&$key=$value";
		}
				
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/x-www-form-urlencoded", "Content-Length: " . strlen($req)));
		curl_setopt($ch, CURLOPT_HEADER, 0);   
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		
		$curl_result = @curl_exec($ch);
		$curl_err = curl_error($ch);
		curl_close($ch);
	
		$registrations = (json_decode($curl_result));
		/* 
		 * The resilts are in the following format:
		 * stdClass Object ( 
		 	[Table] => 
				Array ( 
					[0] => stdClass Object ( [enrollment_id] => 481218 [Reference_number] => 374764 [section_id] => 9052 [term] => 13YR [enrollment_status] => Dropped [title] => Purdue Hub-U [subtitle_name] => test course [first_name] => noel [last_name] => robinos [enrolled_date] => 2013-05-01T14:16:00 [Start_date_program] => 2013-10-01T00:00:00 [home_email] => nrobinos@purdue.edu [work_email] => [work_company] => [ceu_check] => No [ceu_description] => null [ceu_amount] => 0 [person_id] => 354635 [schedule_number] => 9863 [username] => nrobinos@purdue.edu [password] => [preferred_addressline1] => Stewart Center, Room G54C [preferred_addressline2] => [preferred_phone] => [preferred_postal_code] => 47905 [preferred_state] => IN [preferred_country] => United States of America [amount] => 300 ) 
					[1] => stdClass Object (...
		 */
		 		 
		 $registrations = $registrations->Table;
		 print_r($registrations); die;
		 
		 /*
		 $testObj->section_id = 9052;
		 $testObj->enrollment_id = '9991218'; 
		 $testObj->lms_id = '999'; // HUB's section ID
		 $testObj->enrollment_status = 'Enrolled';
		 $testObj->first_name = 'Mashka';
		 $testObj->last_name = 'Shunko';
		 $testObj->home_email = 'mshunko@purdue.edu'; // Dropped, Enrolled
		 $testObj->password = '';
		 $testObj->cas_id = 'mshunko';
		 $testObj->username = 'mshunko@purdue.edu';
		 $registrations = array(0 => $testObj);
		 */
		 
		 // Include helper
		require_once(JPATH_ROOT . DS . 'components' . DS . 'com_register' . DS . 'helpers' . DS . 'Premis.php');
		 
		 foreach ($registrations as $registration) 
		 {
			// Do the registration

			$user['fName'] = $registration->first_name;
			$user['lName'] = $registration->last_name;
			$user['email'] = $registration->home_email;
			if (empty($registration->password))
			{
				$user['casId'] = $registration->cas_id;
			}
			$user['premisId'] = $registration->username;
			$user['password'] = $registration->password;
			$user['premisEnrollmentId'] = $registration->enrollment_id;
			
			$sectionId = $registration->lms_id;
							
			if ($testObj->enrollment_status == 'Enrolled')
			{
				$courses['add'] = $sectionId;
			}
			elseif ($testObj->enrollment_status == 'Dropped')
			{
				$courses['drop'] = $sectionId;
			}
							
			// Handle registration request
			$return = Hubzero_Register_Premis::doRegistration($user, $courses);
			//print_r($return); die;
			
			// Check request status and display a corresponding message
			if ($return['status'] != 'ok')		
			{
				echo 'error';	
			}
			else {
				echo 'ok';
			}				

		 }		
		
		die("\n\n<br><br>~<br><br>\n");
		
		// Just do it after done
		return true;			
	}
	
	
	private function write_php_ini($array, $file)
    {
        $res = array();
        foreach($array as $key => $val)
        {
            if(is_array($val))
            {
                $res[] = "[$key]";
                foreach($val as $skey => $sval) $res[] = "$skey = ".(is_numeric($sval) ? $sval : '"'.$sval.'"');
            }
            else $res[] = "$key = ".(is_numeric($val) ? $val : '"'.$val.'"');
        }
		file_put_contents($file, implode("rn", $res), LOCK_EX);  
    }

}

