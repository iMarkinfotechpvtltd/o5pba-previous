<?

class usermanager{
	var $db;
	var $users=array();
	
	function usermanager(){
		$this->db = new DB;
		$this->db->query('SELECT user_id FROM _auth WHERE username!="winston"');
		$this->users=array();
		while($this->db->next_record()){
			array_push($this->users,new auser($this->db->f('user_id')));
		}
	}
	
	function userlist(){
		echo '<h4>Edit Users</h4>';
		echo '<table border="0" cellpadding="2" cellspacing="0" width="500">';
		echo '<tr class=dark><td colspan=2 width=200>Username</td><td colspan=2 width=300>Able to Edit:</td></tr>';
		foreach ($this->users as $theuser){
			if ($theuser->name==$auth->auth['uname']){
				echo sprintf('<tr class=light><td width=60>Edit</td><td width=140>%s</td><td width=240>%s</td><td width=60>&nbsp;</td></tr>',$theuser->name,$this->permstostring($theuser->perms));
			}else{
				echo sprintf('<tr class=light><td width=60><a href="usermanager.php?function=edit&id=%s">Edit</a></td><td width=140>%s</td><td width=240>%s</td><td width=60><a href="javascript:confirmDelete(\'%s\',\'usermanager.php?function=delete&id=%s\');">Delete</a></td></tr>',$theuser->user_id,$theuser->name,$this->permstostring($theuser->perms),$theuser->name,$theuser->user_id);
			}
		}
		echo'<tr class=light><td colspan=4><a href="usermanager.php?function=add">Add User</a></td></tr>';
		echo '</table>';
	}
	
	function getuser($id){
		foreach($this->users as $theuser){
			if($theuser->user_id==$id) return $theuser;
		}
		return false;
	}
	function checkname($name){
		foreach ($this->users as $user){
			if($user->name == $name) return true;
		}
		return false;
	}
	
	function adduser($user){
		$this->db->query(sprintf('INSERT INTO _auth VALUES("%s","%s","%s","%s")',md5(uniqid('mountthefloopy')),$user->name,md5($user->pass),$this->permstostring($user->perms)));
		logit("Added user '".$user->name."'");
		$this->usermanager();
	}
	
	function updateuser($user){
		$this->db->query(sprintf('UPDATE _auth SET PASSWORD="%s",perms="%s" WHERE user_id="%s"',$user->pass,$this->permstostring($user->perms),$user->user_id));
		$this->usermanager();
		
		/*if($this->db->affected_rows()!=1){
		$this->adduser($user);
		}
		*/
	}
	
	function deleteuser($id){
		$this->db->query(sprintf('DELETE FROM _auth WHERE user_id="%s"',$id));
		logit("Deleted user '".$user->name."'");
		$this->usermanager();
	}
	
	function permstostring($perms){
		$string='';
		foreach($perms as $perm){
			$string.=$perm.',';
		}
		return substr($string,0,-1);
	}
	function changepass($id,$password){
		$this->db->query(sprintf('UPDATE _auth SET PASSWORD="%s" WHERE user_id="%s"',md5($password),$id));
		
	}
}
class auser{
	var $user_id;
	var $name;
	var $pass;
	var $perms=array();
	var $db;
	
	function auser($thisid=''){
		$this->db = new DB;
		if($thisid!=''){
			$this->db->query(sprintf('SELECT * FROM _auth WHERE user_id="%s"',$thisid));
			if($this->db->num_rows()==1){
				$this->db->next_record();
				$this->user_id = $this->db->f('user_id');
				$this->name = $this->db->f('username');
				$this->pass = $this->db->f('PASSWORD');
				$this->perms = split(',',$this->db->f('perms'));
			}
		}
	}
}

?>
