<?php
class Helper extends Student{
	
	public function isSignedIn(){
		global $mysql;
		$query = "SELECT `action` FROM `history` WHERE `student` = \"".$this->getID()."\" AND `action` =".HISTORYEVENT_SIGNIN." OR `student` = \"".$this->studentId."\" AND `action` =".HISTORYEVENT_SIGNOUT;
		$result = $mysql->query($query);
		$row = mysqli_fetch_assoc($result);
		return $row["action"];
	}

	public function signin(){
		addHistoryItem(-1, $this->getID(), HISTORYEVENT_SIGNIN);
	}

	public function signout(){
		addHistoryItem(-1, $this->getID(), HISTORYEVENT_SIGNOUT);
	}

	public static function exists($studentId){
		global $helpers;
		return in_array(strval($studentId), $helpers);
	}
}
?>
