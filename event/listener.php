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

	public function __construct(\phpbb\auth\auth $auth)
	{
		$this->auth = $auth;
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

	public function modify_submit_post_data($event)
	{
		$data_array = $event['data'];
		$mode = $event['mode'];

		if ($mode == 'post' && !$this->auth->acl_get('f_topic_approve', $data_array['forum_id']))
		{
			$data_array['force_approved_state'] = ITEM_UNAPPROVED;
		}
		$event['data'] = $data_array;
	}
}
