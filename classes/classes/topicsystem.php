<?
class topicsystem{
	var $abs;
	var $t_name;
	var $db;
	var $system_name;
	var $function;
	var $it_db;
	var $current_topic;
	var $postfiles;
	
	function topicsystem($name,$section){
		global $htdocsdir;
		$this->t_name = $name;
		$this->system_name = $section;
		$this->db = new DB;
		$this->postfiles = $htdocsdir.'news/posts/';
		$this->abs=$htdocsdir;
	}
	
	function addTopic($topic){
		global $auth;
		$this->db->query(sprintf('INSERT INTO %s (created,title,modified,modifiedby,createdby) VALUES("%s","%s","%s","%s","%s")',$this->t_name,$topic->created,mysql_real_escape_string($topic->title),$topic->created,$auth->auth['uname'],$auth->auth['uname']));
		$test = true;
		if($this->db->affected_rows()==1){
			logit("Topic '".$topic->title." added to ".$this->system_name);
			foreach($topic->items as $title=>$data){
				$this->db->query(sprintf('INSERT INTO %s (created,ititle,file,order1) VALUES("%s","%s","%s","%s")',$this->t_name.'_items',$topic->created,mysql_real_escape_string($title),$data[0],$data[2]));
				logit("Item '".$title."(".$data[0].")' added to ".$topic->title." in ".$this->system_name);
				if (!is_dir($this->postfiles.$topic->created.'/')){
					mkdir($this->postfiles.$topic->created.'/', 0777);
				}
				copy($data[1],$this->postfiles.$topic->created.'/'.$data[0]);
			}
			$u = new update;
			$u->dowhatsnew();
			return true;
		}else{
			return false;
		}
	}
	function updateTopic($topic){
		
		global $auth;
		$newtime = time();
		$this->db->query(sprintf('SELECT * FROM %s WHERE created="%s"',$this->t_name,$topic->created));
		if($this->db->num_rows()==1){
			$this->db->query(sprintf('UPDATE %s SET title="%s",modified="%s",modifiedby="%s" WHERE created="%s" ',$this->t_name,mysql_real_escape_string($topic->title),$newtime,$auth->auth['uname'],$topic->created));
			logit("Updated topic '".$topic->title."' in ".$this->system_name);
			$u = new update;
			$u->dowhatsnew();
			
			$count=0;
			foreach($topic->items as $name => $data){
				if(isset($data[3])){
					$this->db->query(sprintf('SELECT file from %s_items WHERE created="%s" AND order1="%s"',$this->t_name,$topic->created,$data[2]));
					$this->db->next_record();
					logit("delete:".$this->system_name.":".$name.":".$this->db->f('file'));
					@ unlink($this->postfiles.$topic->created.'/'.$this->db->f('file'));
					$this->db->query(sprintf('DELETE FROM %s_items WHERE created="%s" AND order1="%s"',$this->t_name,$topic->created,$data[2]));
					logit("Item '".$name."(".$data[0].")' deleted from ".$topic->title." in ".$this->system_name);
				}elseif($data[1]==''){
					
					$this->db->query(sprintf('SELECT * from %s_items WHERE created="%s" AND order1="%s"',$this->t_name,$topic->created,$data[2]));
					if ($this->db->next_record()){
						if ($this->db->f('ititle') != $name || $this->db->f('order1') != $count || $this->db->f('discription') != $_POST['item_description_'.$count]){
							$this->db->query(sprintf('UPDATE %s_items SET ititle="%s",order1="%s",discription="%s" WHERE created="%s" AND order1="%s"',$this->t_name,mysql_real_escape_string($name),$count, $_POST['item_description_'.$count],$topic->created,$data[2]));
							logit("Item '".$name."(".$data[0].")' updated in ".$topic->title." in ".$this->system_name);
						}
					}else{
						$this->db->query(sprintf('INSERT INTO %s_items (ititle,order1, created) values ("%s","%s","%s")',$this->t_name,mysql_real_escape_string($name),$count,$topic->created));
						logit("Item '".$name."(".$data[0].")' updated in ".$topic->title." in ".$this->system_name);
						
					}
					$count++;
					
				}else{
					$this->db->query(sprintf('SELECT file,ititle from %s_items WHERE created="%s" AND order1="%s"',$this->t_name,$topic->created,$data[2]));
					if($this->db->num_rows()!=0){
						$this->db->next_record();
						if (!is_dir($this->postfiles.$topic->created.'/')){
							mkdir($this->postfiles.$topic->created.'/', 07777);
						}
						@ unlink($this->postfiles.$topic->created.'/'.$this->db->f('file'));
						copy($data[1],$this->postfiles.$topic->created.'/'.$data[0]);
						logit("Item '".$this->db->f('ititle')."(".$this->db->f('file').")' changed to '".$name."(".$data[0].")' in ".$topic->title." in ".$this->system_name);
						$this->db->query(sprintf('UPDATE %s_items SET ititle="%s",file="%s",order1="%s",discription="%s",modified="%s" WHERE created="%s" AND order1="%s"',$this->t_name,mysql_real_escape_string($name),$data[0],$count,$_POST['item_description_'.$count],time(),$topic->created,$count));
						$count++;
					}else{
						if (!is_dir($this->postfiles.$topic->created.'/')){
							mkdir($this->postfiles.$topic->created.'/', 0777);
						}
						copy($data[1],$this->postfiles.$topic->created.'/'.$data[0]);
						logit("Item '".$name."(".$data[0].")' added to ".$topic->title." in ".$this->system_name);
						$this->db->query(sprintf('INSERT INTO %s_items (created,modified,ititle,file,order1,discription) values("%s","%s","%s","%s","%s","%s")',$this->t_name,$topic->created,time(),mysql_real_escape_string($name),$data[0],$count,$_POST['item_description_'.$count]));
						$count++;
					}
				}
			}
			if($count==0){
				$this->deleteTopic($topic->created);
			}
			return true;
			
		}else{
			return false;
		}
	}
	
	function updateTourn($topic){
		global $auth,$_POST;
		$this->db->query(sprintf('UPDATE %s SET modified="%s",modifiedby="%s" WHERE created="%s" ',$this->t_name,time(),$auth->auth['uname'],substr($topic->created,0,strpos($topic->created,"_"))));
		logit("Updated ".$this->system_name);
		$u = new update;
		$u->dowhatsnew();
		
		$count=0;
		foreach($topic->items as $name => $data){
			if(isset($data[3])){
				$this->db->query(sprintf('SELECT file from %s_items WHERE created="%s" AND order1="%s"',$this->t_name,$topic->created,$data[2]));
				$this->db->next_record();
				@ unlink($this->postfiles.$topic->created.'/'.$this->db->f('file'));
				$this->db->query(sprintf('DELETE FROM %s_items WHERE created="%s" AND order1="%s"',$this->t_name,$topic->created,$data[2]));
				logit("Item '".$name."(".$data[0].")' deleted from ".$topic->title." in ".$this->system_name);
			}elseif($data[1]==''){
				
				$this->db->query(sprintf('SELECT * from %s_items WHERE created="%s" AND order1="%s"',$this->t_name,$topic->created,$data[2]));
				if ($this->db->next_record()){
					if ($this->db->f('ititle')!=$name || $this->db->f('order1')!=$count || $this->db->f('discription')!=$_POST['item_description_'.$count]){
						$this->db->query(sprintf('UPDATE %s_items SET ititle="%s",order1="%s",discription="%s",modified="%s" WHERE created="%s" AND order1="%s"',$this->t_name,mysql_real_escape_string($name),$count,$_POST['item_description_'.$count],time(),$topic->created,$data[2]));
						logit("Item '".$name."(".$data[0].")' updated in ".$topic->title." in ".$this->system_name);
					}						
				}else{
					$this->db->query(sprintf('INSERT INTO %s_items (ititle,order1, created, modified) values ("%s","%s","%s","%s")',$this->t_name,mysql_real_escape_string($name),$count,$topic->created,time()));
					logit("Item '".$name."(".$data[0].")' updated in ".$topic->title." in ".$this->system_name);
					
				}
				$count++;
				
			}else{
				$this->db->query(sprintf('SELECT file from %s_items WHERE created="%s" AND order1="%s"',$this->t_name,$topic->created,$data[2]));
				if($this->db->num_rows()!=0){
					
					$this->db->next_record();
					if (!is_dir($this->postfiles.$topic->created.'/')){
						mkdir($this->postfiles.$topic->created.'/', 0777);
					}
					@ unlink($this->postfiles.$topic->created.'/'.$this->db->f('file'));
					copy($data[1],$this->postfiles.$topic->created.'/'.$data[0]);
					if ($this->db->f('ititle')!=$name || $this->db->f('order1')!=$count || $this->db->f('discription')!=$_POST['item_description_'.$count]){
						$this->db->query(sprintf('UPDATE %s_items SET ititle="%s",file="%s",order1="%s",discription="%s",modified="%s" WHERE created="%s" AND order1="%s"',$this->t_name,mysql_real_escape_string($name),$data[0],$count,$_POST['item_description_'.$count],time(),$topic->created,$count));
						logit("Item '".$this->db->f('ititle')."(".$this->db->f('file').")' changed to '".$name."(".$data[0].")' in ".$topic->title." in ".$this->system_name);
					}						
					$count++;
				}else{
					if (!is_dir($this->postfiles.$topic->created.'/')){
						mkdir($this->postfiles.$topic->created.'/', 0777);
					}
					copy($data[1],$this->postfiles.$topic->created.'/'.$data[0]);
					$this->db->query(sprintf('INSERT INTO %s_items (created,modified,ititle,file,order1,discription) values("%s","%s","%s","%s","%s","%s")',$this->t_name,$topic->created,time(),mysql_real_escape_string($name),$data[0],$count,$_POST['item_description_'.$count]));
					logit("Item '".$name."(".$data[0].")' added to ".$topic->title." in ".$this->system_name);
					$count++;
				}
				if (strpos($topic->created,'_ph')){
					$photo=new photo();
					if (hasmodule('advancedphoto')){
						$photo->resize_img($this->postfiles.$topic->created.'/',$data[0],'t_');
						$photo->resize_img($this->postfiles.$topic->created.'/',$data[0],'sm_');
						$photo->resize_img($this->postfiles.$topic->created.'/',$data[0],'md_');
					}else{
						$photo->resize_img($this->postfiles.$topic->created.'/',$data[0],'t_');
					}
					
				}
				
			}
		}
		
		return true;
		
	}
	function deleteTopic($time){
		$topic = new topic($this->t_name);
		$topic->loadFromTimestamp($time);
		logit("Topic '".$topic->title." deleted from ".$this->system_name);
		$this->db->query(sprintf('delete from %s where created="%s"',$this->t_name,$time));
		$this->db->query(sprintf('select file,created from %s_items where created="%s"',$this->t_name,$time));
		while($this->db->next_record()){
			@ unlink($this->postfiles.$this->db->f('created').'/'.$this->db->f('file'));
		}
		@ unlink($this->postfiles.$this->db->f('created'));
		$this->db->query(sprintf('delete from %s_items where created="%s"',$this->t_name,$time));
		
	}
	
	function getTopic($time){
		$topic = new topic($this->t_name);
		$topic->loadFromTimestamp($time);
		return $topic;
	}
	
	function iterate(){
		$this->it_db = new DB;
		$this->it_db->query(sprintf('SELECT * FROM %s ORDER BY modified DESC',$this->t_name));
	}
	
	function next_topic(){
		if($this->it_db->next_record()){
			$this->current_topic = $this->getTopic($this->it_db->f('created'));
			return true;
		}else{
			return false;
		}
	}
	
	function f($returnval){
		if($returnval=='title'){
			return $this->current_topic->title;
		}elseif($returnval=='modified'){
			return $this->current_topic->modified;
		}elseif($returnval=='items'){
			return $this->current_topic->items;
		}elseif($returnval=='created'){
			return $this->current_topic->created;
		}else{
			return '';
		}
	}
}

class topic{
	var $t_name;
	var $title;
	var $modified;
	var $items=array();
	var $db;
	var $valid;
	var $createdby;
	var $created;
	var $modifiedby;
	var $idescription=array();
	
	function topic($name){
		$this->t_name = $name;
		$this->db = new DB;
		$this->valid=false;
	}
	
	function loadFromTimestamp($stamp){
		$this->db->query(sprintf('SELECT t.*,i.order1 as order1,i.file as file,i.ititle as ititle FROM %s t,%s i WHERE t.created="%s" AND i.created="%s" ORDER BY order1',$this->t_name,$this->t_name.'_items',$stamp,$stamp));
		if($this->db->num_rows()>0){
			while($this->db->next_record()){
				$this->valid = true;
				$this->title = $this->db->f('title');
				$this->created = $this->db->f('created');
				$this->modified = $this->db->f('modified');
				$this->createdby = $this->db->f('createdby');
				$this->modifiedby = $this->db->f('modifiedby');
				$this->items[$this->db->f('ititle')] = array($this->db->f('file'),'',$this->db->f('order1'));
			}
			return true;
		}else{
			return false;
		}
	}
	
	function loadTournFromTimestamp($stamp){
		
		$this->db->query(sprintf('SELECT * FROM %s WHERE created="%s" ORDER BY order1',$this->t_name.'_items',$stamp));
		$this->created = $stamp;
		
		if($this->db->num_rows()>0){
			while($this->db->next_record()){
				$this->valid = true;
				$this->title = $this->db->f('title');
				$this->created = $this->db->f('created');
				$this->modified = $this->db->f('modified');
				$this->createdby = $this->db->f('createdby');
				$this->modifiedby = $this->db->f('modifiedby');
				$this->items[$this->db->f('ititle')] = array($this->db->f('file'),'',$this->db->f('order1'),$this->db->f('discription'),$this->db->f('modified'));
			}
			return true;
		}else{
			return false;
		}
	}
	
	function addItem($ititle,$ifile){
		global $_FILES,$_POST,$time;
		
		if(is_uploaded_file($_FILES[$ifile]['tmp_name'])){
			$this->items[$_POST[$ititle]]=array($_FILES[$ifile]['name'],$_FILES[$ifile]['tmp_name'],count($this->items));
			return true;
		}elseif(strpos(' '.$_POST[$ititle],'</a>')){
			$this->items[$_POST[$ititle]]=array('','',count($this->items));
			return true;
		}else{
			return false;
		}
	}
	
	function updateItem($order,$ititle,$ifile,$idescription=''){
		global $_FILES,$_POST,$time;
		
		foreach($this->items as $name=>$data){
			if($data[2]==$order){
				
				if($_FILES[$ifile]['tmp_name']=='' || strpos(' '.$_POST[$ititle],'</a>')){
					$file = $data[0];
					$tempfile = '';
				}else{
					if(is_uploaded_file($_FILES[$ifile]['tmp_name'])){
						$file = $_FILES[$ifile]['name'];
						$tempfile = $_FILES[$ifile]['tmp_name'];
					}else{
						return false;
						
					}
				}
				unset($this->items[$name]);
				$this->items[$_POST[$ititle]] = array($file,$tempfile,$order);
				$this->idescription[$_POST[$ititle]] = array($idescription);
				return true;
			}
		}
		return $this->addItem($ititle,$ifile);
	}
	
	function deleteItem($order){
		foreach($this->items as $name=>$data){
			if($data[2]==$order){
				$this->items[$name]=array($data[0],$data[1],$data[2],1);
				return true;
			}
		}
		return false;
	}
}
?>
