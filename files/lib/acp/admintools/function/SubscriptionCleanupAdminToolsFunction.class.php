<?php
// wcf imports
require_once(WCF_DIR.'lib/acp/admintools/function/AbstractAdminToolsFunction.class.php');

/**
 * Cleans up subscriptions
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
class SubscriptionCleanupAdminToolsFunction extends AbstractAdminToolsFunction {

	/**
	 * @see AdminToolsFunction::execute($data)
	 */
	public function execute($data) {
		parent::execute($data);

		$parameters = $data['parameters']['wbb.subscriptionCleanup'];
		if(!$parameters['active']) return;

		$count = 0;
		$sql = "DELETE FROM 	wbb".WBB_N."_thread_subscription
                	WHERE 		(userID) NOT IN (SELECT userID FROM wbb".WBB_N."_user)";
		WCF::getDB()->sendQuery($sql);
		$count += WCF::getDB()->getAffectedRows();

		$sql = "DELETE FROM 	wbb".WBB_N."_board_subscription
                	WHERE 		(userID) NOT IN (SELECT userID FROM wbb".WBB_N."_user)";
		WCF::getDB()->sendQuery($sql);
		$count += WCF::getDB()->getAffectedRows();

		// FIXME: Broken functionality
		/*
		$sql = "DELETE FROM wbb".WBB_N."_thread_subscription
		WHERE (userID, threadID) NOT IN (
		SELECT utg.userID, thr.threadID
		FROM wbb".WBB_N."_thread thr, wcf".WCF_N."_user_to_groups utg, wbb".WBB_N."_board_to_group btg
		WHERE thr.boardID = btg.boardID
		AND btg.groupID = utg.groupID
		AND btg.canViewBoard = 1
		AND btg.canEnterBoard = 1
		AND btg.canReadThread = 1)";
		WCF::getDB()->sendQuery($sql);
		$count += WCF::getDB()->getAffectedRows();

		$sql = "DELETE FROM wbb".WBB_N."_board_subscription
		WHERE (userID, boardID) NOT IN (
		SELECT utg.userID, btg.boardID
		FROM wbb".WBB_N."_board_to_group btg, wcf".WCF_N."_user_to_groups utg
		WHERE btg.groupID = utg.groupID
		AND btg.canViewBoard = 1
		AND btg.canEnterBoard = 1
		AND btg.canReadThread = 1)";
		WCF::getDB()->sendQuery($sql);
		$count += WCF::getDB()->getAffectedRows();
		*/
		$this->setReturnMessage('success', WCF::getLanguage()->get('wbb.acp.admintools.function.wbb.subscriptionCleanup.success', array('$count' => $count)));
		$this->executed();
	}
}

?>