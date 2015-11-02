<?php
/**
*
* New Topics Need Approval extension for the phpBB Forum Software package.
*
* @copyright (c) 2015 Rich McGirr (RMcGirr83)
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace rmcgirr83\newtopicsneedapproval\migrations;

/**
* Migration stage 3: Initial permission
*/
class v_100 extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return array('\phpbb\db\migration\data\v310\gold');
	}

	/**
	* Add or update data in the database
	*
	* @return array Array of table data
	* @access public
	*/
	public function update_data()
	{
		return array(
			// Add permission
			array('permission.add', array('f_topic_approve', false)),

			// Set permissions
			array('permission.permission_set', array('ROLE_FORUM_FULL', 'f_topic_approve')),
			array('permission.permission_set', array('ROLE_FORUM_LIMITED', 'f_topic_approve')),
			array('permission.permission_set', array('ROLE_FORUM_LIMITED_POLLS', 'f_topic_approve')),
			array('permission.permission_set', array('ROLE_FORUM_STANDARD', 'f_topic_approve')),
			array('permission.permission_set', array('ROLE_FORUM_POLLS', 'f_topic_approve')),
			array('permission.permission_set', array('ROLE_FORUM_ONQUEUE', 'f_topic_approve', 'role', false)),
		);
	}
}
