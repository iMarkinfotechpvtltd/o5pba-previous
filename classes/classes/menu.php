<?php
class menu{
	var $menus;
	function defaults(){

		global $database;

		$this->add('Admin','<center>You are not<br>logged in</center>');
				
	}

	function start($name){
		$this->menus[$name] =array();
	}

	function add($name,$option, $link=''){
		$this->menus[$name][$option]=$link;
	}

	function drawmenu($name='',$class='menu'){
		if (!is_array($this->menus)){
			$this->defaults();
		}

		if ($name==''){
			foreach ($this->menus as $names => $values){
				echo '<table width="100" cellpadding="0" cellspacing="0" border="0" class="menu">';
				echo sprintf('<tr><td align="center" class="%sheader">%s</td></tr>',$class,$names);
				foreach ($values as $key=>$link){
					if ($link){
						echo sprintf('<tr><td align="center">&nbsp;<a class="%slink"href="%s">%s</a></td></tr>',$class,$link,$key);
					}else{
						echo sprintf('<tr><td align="center">&nbsp;%s</a></td></tr>',$key);						
					}
				}
				echo '</table><br>';
			}
		}else{
			echo '<table width="100" cellpadding="0" cellspacing="0" border="0" class="menu">';
			echo sprintf('<tr><td align="center" class="%sheader">%s</td></tr>',$class,$name);
			foreach ($this->menus[$name] as $key=>$link){
				echo sprintf('<tr><td align="center">&nbsp;<a class="%slink"href="%s">%s</a></td></tr>',$class,$link,$key);
			}
			echo '</table><br>';
		}
	}
}

?>
