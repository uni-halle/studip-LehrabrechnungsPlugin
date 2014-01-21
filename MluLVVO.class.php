<?php
/**
* MluLVVO.class.php
*
*
*
*
* @author	André Noack <noack@data-quest>, Suchi & Berg GmbH <info@data-quest.de>
* @version
* @access	public
*/

// +---------------------------------------------------------------------------+
// This file is part of Stud.IP
//
// Copyright (C) 2008 André Noack ,	<noack@data-quest.de>
//
// +---------------------------------------------------------------------------+
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or any later version.
// +---------------------------------------------------------------------------+
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
// +---------------------------------------------------------------------------+

define('SWS_DATAFIELD_ID', 'b0603040a62adf1ff83d0452743e5d33');

class MluLVVO extends SimpleORMap {

    protected $db_table = "mlu_lvvo_plugin";

    static function GetSeminarSWS($seminar_id){
		$db = DbManager::get();
		$value = $db->query(sprintf("SELECT content FROM datafields_entries WHERE range_id=%s AND datafield_id=%s",
		                            $db->quote($seminar_id), $db->quote(SWS_DATAFIELD_ID))
		                   )->fetchColumn();
		$value = str_replace(',','.', trim($value));
		$value = (float)$value;
		return $value;
	}

	static function SetSeminarSWS($seminar_id, $value){
		$value = str_replace(',','.', $value);
		$db = DbManager::get();
		$affected_rows = $db->exec(sprintf("REPLACE INTO datafields_entries (range_id,datafield_id,content,chdate) VALUES (%s,%s,%s,UNIX_TIMESTAMP())",
		                  $db->quote($seminar_id), $db->quote(SWS_DATAFIELD_ID), $db->quote($value))
		          );
		return $affected_rows;
	}

	static function GetSeminarLVVO($seminar_id){
		$ret = array();
		foreach(self::findByseminar_id($seminar_id) as $one) {
			$ret[$one->user_id] = $one;
		}
		return $ret;
	}
}
?>