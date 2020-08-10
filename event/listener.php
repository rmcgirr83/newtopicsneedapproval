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

use phpbb\auth\auth;
use phpbb\language\language;
use phpbb\template\template;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
* Event listener
*/
class listener implements EventSubscriberInterface
{
	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\template\template */
	protected $template;

	public function __construct(auth $auth, language $language, template $template)
	{
		$this->auth = $auth;
		$this->language = $language;
		$this->template = $template;
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
			'core.user_setup'					=> 'user_setup',
			'core.permissions'					=> 'add_permission',
			'core.modify_submit_post_data'		=> 'modify_submit_post_data',
			'core.posting_modify_template_vars'	=> 'posting_modify_template_vars',
			'core.viewforum_get_topic_data'		=> 'modify_template_vars',
		);
	}

	/**
	 * Add the lang vars
	 *
	 * @param object $event The event object
	 * @return null
	 * @access public
	 */
	public function user_setup($event)
	{
		$this->language->add_lang('common', 'rmcgirr83/newtopicsneedapproval');
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
	public function posting_modify_template_vars($event)
	{
		if ($this->check_auth($event['forum_id']) && $event['mode'] == 'post')
		{
			$this->template->assign_var('S_REQUIRES_APPROVAL', true);
		}
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
		if ($this->check_auth($event['forum_id']))
		{
			$this->template->assign_var('S_REQUIRES_APPROVAL', true);
		}
	}

	/**
	* User/group can post in forum without approval
	*
	* @param	int 	$forum_id	The id of the forum
	* @return	bool				true if needed false if not
	* @access 	private
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
}
