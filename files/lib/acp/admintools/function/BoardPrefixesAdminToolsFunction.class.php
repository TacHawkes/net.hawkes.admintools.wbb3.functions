<?php
// wcf imports
require_once(WCF_DIR.'lib/acp/admintools/function/AbstractAdminToolsFunction.class.php');

/**
 * Copies board prefixes
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
class BoardPrefixesAdminToolsFunction extends AbstractAdminToolsFunction {

	/**
	 * @see AdminToolsFunction::execute($data)
	 */
	public function execute($data) {
		parent::execute($data);

		$parameters = $data['parameters']['wbb.boardPrefixes'];
		$sourceBoardID = intval($parameters['sourceBoard']);
		$targetBoardIDs = ArrayUtil::toIntegerArray(explode(',', $parameters['targetBoards']));
		if (in_array($sourceBoardID, $targetBoardIDs)) {
			$this->setReturnMessage('error', WCF::getLanguage()->get('wbb.acp.admintools.function.wbb.boardPrefixes.sourceInTargetArray'));
			return;
		}

		$sql = "SELECT 	prefixes
			FROM 	wbb".WBB_N."_board 
			WHERE 	boardID = ".$sourceBoardID;
		$row = WCF::getDB()->getFirstRow($sql);
		if (!empty($row['prefixes'])) {
			$sql = "UPDATE 	wbb".WBB_N."_board
				SET 	prefixes = '".escapeString($row['prefixes'])."'
				WHERE 	boardID IN (".implode(',', $targetBoardIDs).")";
			WCF::getDB()->sendQuery($sql);
		}
		else {
			$this->setReturnMessage('warning', WCF::getLanguage()->get('wbb.acp.admintools.function.wbb.boardPrefixes.noPrefixesInSource'));
		}

		// clear cache
		WCF::getCache()->clearResource('board', true);

		$this->executed();
	}
}

?>