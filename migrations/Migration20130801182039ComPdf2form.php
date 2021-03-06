<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding indices to pdf2form tables
 **/
class Migration20130801182039ComPdf2form extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		if ($db->tableExists('#__pdf_forms'))
		{
			if (!$db->tableHasKey('#__pdf_form_deployments', 'jos_pdf_form_deployments_crumb_uidx'))
			{
				$query = "create unique index jos_pdf_form_deployments_crumb_uidx on #__pdf_form_deployments(crumb)";
				$db->setQuery($query);
				$db->query();
			}
			if (!$db->tableHasKey('#__pdf_form_responses', 'jos_pdf_form_responses_respondent_id_idx'))
			{
				$query = "create index jos_pdf_form_responses_respondent_id_idx on #__pdf_form_responses(respondent_id)";
				$db->setQuery($query);
				$db->query();
			}
			if (!$db->tableHasKey('#__pdf_form_responses', 'jos_pdf_form_responses_question_id_idx'))
			{
				$query = "create index jos_pdf_form_responses_question_id_idx on #__pdf_form_responses(question_id)";
				$db->setQuery($query);
				$db->query();
			}
			if (!$db->tableHasKey('#__pdf_form_responses', 'jos_pdf_form_responses_answer_id_idx'))
			{
				$query = "create index jos_pdf_form_responses_answer_id_idx on #__pdf_form_responses(answer_id)";
				$db->setQuery($query);
				$db->query();
			}
			if (!$db->tableHasKey('#__pdf_form_answers', 'jos_pdf_form_answers_question_id_idx'))
			{
				$query = "create index jos_pdf_form_answers_question_id_idx on #__pdf_form_answers(question_id)";
				$db->setQuery($query);
				$db->query();
			}
			if (!$db->tableHasKey('#__pdf_form_respondents', 'jos_pdf_form_respondents_user_id_idx'))
			{
				$query = "create index jos_pdf_form_respondents_user_id_idx on #__pdf_form_respondents(user_id)";
				$db->setQuery($query);
				$db->query();
			}
			if (!$db->tableHasKey('#__pdf_form_respondents', 'jos_pdf_form_respondents_deployment_id_idx'))
			{
				$query = "create index jos_pdf_form_respondents_deployment_id_idx on #__pdf_form_respondents(deployment_id)";
				$db->setQuery($query);
				$db->query();
			}
		}
	}

	/**
	 * Down
	 **/
	protected static function down($db)
	{
		if ($db->tableExists('#__pdf_forms'))
		{
			foreach (array(
				'jos_pdf_form_deployments_crumb_uidx on jos_pdf_form_deployments',
				'jos_pdf_form_responses_respondent_id_idx on jos_pdf_form_responses',
				'jos_pdf_form_responses_question_id_idx on jos_pdf_form_responses',
				'jos_pdf_form_responses_answer_id_idx on jos_pdf_form_responses',
				'jos_pdf_form_answers_question_id_idx on jos_pdf_form_answers',
				'jos_pdf_form_respondents_user_id_idx on jos_pdf_form_respondents',
				'jos_pdf_form_respondents_deployment_id_idx on jos_pdf_form_respondents'
			) as $query) 
			{
				$db->setQuery("drop index $query");
				$db->query();
			}
		}
	}
}
