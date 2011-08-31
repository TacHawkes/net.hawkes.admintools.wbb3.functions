<?php
// wcf imports
require_once(WCF_DIR.'lib/acp/admintools/function/AbstractAdminToolsFunction.class.php');

/**
 * This function assigns boards as subscriptions to user groups
 *
 * This file is part of Admin Tools 2.
 *
 * Admin Tools 2 is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Admin Tools 2 is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Admin Tools 2.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author	Oliver Kliebisch
 * @copyright	2009 Oliver Kliebisch
 * @license	GNU General Public License <http://www.gnu.org/licenses/>
 * @package	net.hawkes.admintools.wbb3.functions
 * @subpackage 	acp.admintools.function
 * @category 	WBB
 */
class AssignSubscriptionAdminToolsFunction extends AbstractAdminToolsFunction {
	
	/**
	 * @see AdminToolsFunction::execute($data)
	 */
	public function execute($data) {
		parent::execute($data);
		
		$parameters = $data['parameters']['wbb.assignSubscription'];			
		$boardIDs = ArrayUtil::toIntegerArray(explode(',', $parameters['boards']));
		$usergroup = $parameters['usergroup'];		
								
		$sql = "SELECT 		user.userID, user_option.userOption".WCF::getUser()->getUserOptionID('enableEmailNotification')." AS notify 
			FROM 		wcf".WCF_N."_user user
			LEFT JOIN 	wcf".WCF_N."_user_option_value user_option
			ON 		(user_option.userID = user.userID)
			LEFT JOIN 	wcf".WCF_N."_user_to_groups user_to_group
			ON 		(user_to_group.userID = user.userID)
			WHERE 		user_to_group.groupID = ".$usergroup;
		$result = WCF::getDB()->sendQuery($sql);
		
		$inserts = '';
		while ($row = WCF::getDB()->fetchArray($result)) {
			foreach($boardIDs as $boardID) {
				if (!empty($inserts)) $inserts .= ',';
				$inserts .= '('.$row['userID'].', '.$boardID.', '.$row['notify'].')';
			}
		}
		
		if (!empty($inserts)) {
			$sql = "INSERT IGNORE INTO wbb".WBB_N."_board_subscription
						( userID, boardID, enableNotification)
						VALUES "
						.$inserts;
			WCF::getDB()->sendQuery($sql);
		}
		
		$this->executed();
	}
}

 ?>