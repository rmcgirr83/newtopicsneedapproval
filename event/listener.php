<?php
/**
*
* New Topics Need Approval extension for the phpBB Forum Software package.
*
* @copyright (c) 2015 Rich McGirr (RMcGirr83)
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace rmcgirr83\newtopicsneedapproval\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
* Event listener
*/
class listener implements EventSubscriberInterface
{
	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	public function __construct(\phpbb\auth\auth $auth, \phpbb\template\template $template, \phpbb\user $user)
	{
		$this->auth = $auth;
		$this->template = $template;
		$this->user = $user;
	}

	/**
	* Assign functions defined in this class to event listeners in the core
	*
	* @return array
	* @static
	* @access public
	*/
	static public function getSubscribedEvents()
	{
		return array(
			'core.permissions'					=> 'add_permission',
			'core.modify_submit_post_data'		=> 'modify_submit_post_data',
			'core.posting_modify_template_vars'	=> 'modify_template_vars',
			'core.viewforum_get_topic_data'		=> 'modify_template_vars',
			'core.viewtopic_assign_template_vars_before'	=> 'viewtopic_assign_template_vars_before',
		);
	}

	/**
	* Add administrative permissions to manage forums
	*
	* @param object $event The event object
	* @return null
	* @access public
	*/
	public function add_permission($event)
	{
		$permissions = $event['permissions'];
		$permissions['f_topic_approve'] = array('lang' => 'ACL_F_TOPIC_APPROVE', 'cat' => 'misc');
		$event['permissions'] = $permissions;
	}

	/**
	* Check for permission to post without approval
	*
	* @param object $event The event object
	* @return null
	* @access public
	*/
	public function modify_submit_post_data($event)
	{
		$data_array = $event['data'];
		$mode = $event['mode'];

		if ($event['mode'] == 'post' && $this->check_auth($event['data']['forum_id']))
		{
			$data_array['force_approved_state'] = ITEM_UNAPPROVED;
		}
		$event['data'] = $data_array;
	}

	/**
	* Show a message if can't post without approval
	*
	* @param object $event The event object
	* @return null
	* @access public
	*/
	public function modify_template_vars($event)
	{
		if ($this->check_auth($event['forum_id'] && $event['mode']) == 'post')
		{
			$this->user->add_lang_ext('rmcgirr83/newtopicsneedapproval', 'common');
			// admins and mods don't need permission to post
			if (!$this->auth->acl_get('a_') && !$this->auth->acl_get('m_') && !$this->auth->acl_getf_global('m_'))
			{
				$this->template->assign_var('S_REQUIRES_APPROVAL', true);
			}
		}
	}

	/**
	* User/group can post in forum without approval
	*
	* @param object $forum_id The id of the forum
	* @return approval true if needed false if not
	* @access private
	*/
	private function check_auth($forum_id)
	{
		$requires_approval = false;

		// admins and mods can always post without approval
		if ($this->auth->acl_get('a_') || $this->auth->acl_get('m_') || $this->auth->acl_getf_global('m_'))
		{
			return $requires_approval;
		}

		if ($this->auth->acl_get('f_topic_approve', $forum_id))
		{
			$requires_approval = true;
		}
		return $requires_approval;
	}

	/**
	* Show a message if can't post without approval
	*
	* @param object $event The event object
	* @return null
	* @access public
	*/
	public function viewtopic_assign_template_vars_before($event)
	{
		if ($this->check_auth($event['topic_data']['forum_id']))
		{
			$this->user->add_lang_ext('rmcgirr83/newtopicsneedapproval', 'common');
			// admins and mods don't need permission to post
			if (!$this->auth->acl_get('a_') && !$this->auth->acl_get('m_') && !$this->auth->acl_getf_global('m_'))
			{
				$this->template->assign_var('S_REQUIRES_APPROVAL', true);
			}
		}
	}

}
