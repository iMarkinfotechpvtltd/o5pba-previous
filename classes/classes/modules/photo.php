<?
class photo{
	var $t_name;
	var $postpath;
	var $db;
	
	function resize_img($path, $file, $size=''){
		
		$src_img = imagecreatefromjpeg($path.$file);
		
		if ($size=='t_'){
			$aspect=80;
		}else{
			if ($size=='sm_'){
				$aspect=0.33;
			}else{
				$aspect=0.66;
			}
			if (imagesx($src_img) > imagesy($src_img)){
				$aspect=imagesx($src_img) * $aspect;
				
			}else{
				$aspect=imagesy($src_img) * $aspect;
			}
                        if ($size=='sm_'){
                        	if ($aspect>'320'){
                        	        $aspect=320;
                	        }elseif($aspect<'160'){
        	                        $aspect=160;
	                        }
                        }else{
                                if ($aspect>'640'){                
                                        $aspect=640;                 
                                }elseif($aspect<'320'){ 
                                        $aspect=320; 
                                }
                        }
			
		}
		
		if (imagesx($src_img) > imagesy($src_img)){
			$new_w = $aspect;
			$new_h = imagesy($src_img)*($aspect/imagesx($src_img));
		}else{
			$new_w = imagesx($src_img)*($aspect/imagesy($src_img));
			$new_h = $aspect;
		}
		$dst_img = imagecreate($new_w,$new_h);
		imagecopyresized($dst_img,$src_img,0,0,0,0,$new_w,$new_h,imagesx($src_img),imagesy($src_img));
		imagejpeg($dst_img, $path.$size.$file);
		
	}
	
	function fsize($file){
		$a = array("B", "KB", "MB", "GB", "TB", "PB");
		
		$pos = 0;
		$size = filesize($file);
		while ($size >= 1024) {
			$size /= 1024;
			$pos++;
		}
		
		return round($size,2)." ".$a[$pos];
	}
	
	
	function view_folder($key,$id){
		global $htdocsdir,$webpath;
		
		/*
		second thingo in $accepted_types can be:
		y - show thumbnail
		n - don't show anything
		some_image - to show a generic image
		*/
		
		$accepted_types = array(".gif"=>"y", ".jpg"=>"y", ".mpg"=>"quicktime.gif", ".htm"=>"site.jpg", ".html"=>"site.jpg",".txt"=>"text.jpg",
		".mov"=>"quicktime.gif", ".rm"=>"real.gif", ".ram"=>"real.gif", ".ra"=>"real.gif", ".avi"=>"quicktime.gif", ".wav"=>"quicktime.gif", ".mid"=>"quicktime.gif", ".swf"=>"flash.gif", ".mp3"=>"quicktime.gif");
		
		$images_per_row = 5;
		
		$thumbnails = true;
		// some programs such as Graphic Workshop put a prefix on scaled images
		$thumbnail_prefix = "t_";
		// make sure you have the trailing /
		$thumbnail_directory_name = $htdocsdir.$this->postpath.$key.'/';
		
		$imagepath=$webpath.$this->postpath;
		$image_directory_name = $htdocsdir.$this->postpath.$key.'/';

		$topic = new topic($this->t_name);
		
		
		// index value used together with $images_per_row
		// to see if we should skip to the next row
		$i = 0;
		
		if ($topic->loadTournFromTimestamp($key)){
			
			$this->db=new DB;
			$this->db->query(sprintf('select * from %s where ID like "%s"',$topic->t_name,$_GET['expand']));
			$this->db->next_record();
			
			
			echo '<b>Photo Album for '.$this->db->f('title').'</b><br><br>';
			echo '<a href="'.$_SERVER['PHP_SELF'].'?function=tourn_view&expand='.$_GET['expand'].'">Back</a><hr>';
			
			
			
			echo '<table cellspacing="2" width="100%" cellpadding="2"><tr>';
			
			foreach($topic->items as $name=>$rawfile){
				$file=$rawfile[0];
				
				// Don't show '.' and '..'
				if ( $file != "." && $file != ".." ) {
					$extension = strtolower(substr($file,strpos($file,".")));
					// is this file type in the accepted types array?
					if ( $accepted_types[$extension] ) {
						if ( $i>0 && is_integer($i/$images_per_row) ) {
							$beginning = "\n</tr>\n<tr>\n";
						}else {
							$beginning = "";
						}
						echo $beginning.'<td align="center" valign=top>';
						//						echo '<a href='.$_SERVER['PHP_SELF']."?function=show&key=".$key."&id=".$_GET['expand']."&file=".str_replace ('&', '%26', str_replace (' ', '%20', $file)).'>';
						
						if ( $thumbnails && ($accepted_types[$extension]=='y') ) {
							@ $size = GetImageSize ($thumbnail_directory_name.$thumbnail_prefix.$file);
							echo sprintf('<table width=80><tr height=80><td colspan=3><center><a href=%s?function=show&key=%s&expand=%s&file=sm_%s><img src="%s" border=0 %s></a></center></td></tr><tr>',$_SERVER['PHP_SELF'],$key,$_GET['expand'],str_replace ('&', '%26', str_replace (' ', '%20', $file)),$imagepath.$key.'/'.$thumbnail_prefix.$file,$size[3]);
							
							@ $size = GetImageSize ($thumbnail_directory_name.'sm_'.$file);
							echo sprintf('<td><center><a href=%s?function=show&key=%s&expand=%s&file=sm_%s>Small</a><br><font style="font-size:9px">(%sx%s)<br><nobr>%s</nobr></font></center></td>',$_SERVER['PHP_SELF'],$key,$_GET['expand'],str_replace ('&', '%26', str_replace (' ', '%20', $file)),$size[0],$size[1],$this->fsize($thumbnail_directory_name.'sm_'.$file));
							
							@ $size = GetImageSize ($thumbnail_directory_name.'md_'.$file);
							echo sprintf('<td><center><a href=%s?function=show&key=%s&expand=%s&file=md_%s>Medium</a><br><font style="font-size:9px">(%sx%s)<br><nobr>%s</nobr></font></center></td>',$_SERVER['PHP_SELF'],$key,$_GET['expand'],str_replace ('&', '%26', str_replace (' ', '%20', $file)),$size[0],$size[1],$this->fsize($thumbnail_directory_name.'md_'.$file));
							
							@ $size = GetImageSize ($thumbnail_directory_name.$file);
							echo sprintf('<td><center><a href=%s?function=show&key=%s&expand=%s&file=%s>Large</a><br><font style="font-size:9px">(%sx%s)<br><nobr>%s</nobr></font></center></td></tr>',$_SERVER['PHP_SELF'],$key,$_GET['expand'],str_replace ('&', '%26', str_replace (' ', '%20', $file)),$size[0],$size[1],$this->fsize($thumbnail_directory_name.$file));
							
						}else if ( $thumbnails ) {
							echo sprintf('<table width=80><tr height=80><td colspan=3><a href=%s?function=show&key=%s&expand=%s&file=%s><img src="%s" border=0></a></td></tr>',$_SERVER['PHP_SELF'],$key,$_GET['expand'],str_replace ('&', '%26', str_replace (' ', '%20', $file)),$webpath.'fileicons/'.$accepted_types[$extension]);
						}
						echo '<tr><td colspan=3 valign=top><br><center><font style="font-size:12px;font-weight:bold">'.$name.'</font><br><font size="-3">'.$rawfile[3].'&nbsp;</font></center></td></tr></table></td>';
						
						$i++;
					}
				}
			}
			echo "</table>";
		}
	}
	
	function view_media($key, $file){
		global $htdocsdir,$webpath;
		
		echo '<a href="'.$_SERVER['PHP_SELF'].'?function=photo&expand='.$_GET['expand'].'&key='.$key.'">Back</a><br><br>';
		$types_image = array(".gif"=>"y", ".jpg"=>"y");
		$types_qtime = array(".mpg"=>"quicktime.gif", ".mov"=>"quicktime.gif", ".avi"=>"quicktime.gif", ".wav"=>"quicktime.gif", ".mid"=>"quicktime.gif", ".mp3"=>"quicktime.gif");
		$types_rmedia = array(".rm"=>"real.gif", ".ram"=>"real.gif", ".ra"=>"real.gif");
		$types_sites = array(".htm"=>"site.jpg", ".html"=>"site.jpg");
		$types_text = array(".txt"=>"text.jpg");
		$types_flash = array(".swf"=>"flash.gif");
		
		$extension = strtolower(substr($file,strpos($file,".")));
		$path=$htdocsdir.$this->postpath.$key.'/';
		$imagepath=$webpath.$this->postpath.$key.'/';
		
		//show images
		if ( $types_image[$extension] ) {
			echo '<center>';
			
			$size = GetImageSize (str_replace ('%20', ' ',$path).$file);
			echo '<img src="'.$imagepath.'/'.$file.'" '.$size[3].'><br>';
			
			$this->db=new DB;
			$this->db->query(sprintf('select * from %s_items where created like "%s"',$this->t_name,$key));
			while($this->db->next_record()){
				if (strpos($file,$this->db->f('file'))){
					if (substr($file,2,1)=='_'){
						$file=substr($file,3);
					}
					@ $size = GetImageSize ($path.'sm_'.$file);
					echo sprintf('<table><tr><td><center><a href=%s?function=show&key=%s&expand=%s&file=sm_%s>Small</a><br><font style="font-size:9px">(%sx%s)<br><nobr>%s</nobr></font></center></td>',$_SERVER['PHP_SELF'],$key,$_GET['expand'],str_replace ('&', '%26', str_replace (' ', '%20', $file)),$size[0],$size[1],$this->fsize($path.'sm_'.$file));
					
					@ $size = GetImageSize ($path.'md_'.$file);
					echo sprintf('<td><center><a href=%s?function=show&key=%s&expand=%s&file=md_%s>Medium</a><br><font style="font-size:9px">(%sx%s)<br><nobr>%s</nobr></font></center></td>',$_SERVER['PHP_SELF'],$key,$_GET['expand'],str_replace ('&', '%26', str_replace (' ', '%20', $file)),$size[0],$size[1],$this->fsize($path.'md_'.$file));
					
					@ $size = GetImageSize ($path.$file);
					echo sprintf('<td><center><a href=%s?function=show&key=%s&expand=%s&file=%s>Large</a><br><font style="font-size:9px">(%sx%s)<br><nobr>%s</nobr></font></center></td></tr>',$_SERVER['PHP_SELF'],$key,$_GET['expand'],str_replace ('&', '%26', str_replace (' ', '%20', $file)),$size[0],$size[1],$this->fsize($path.$file));
					
					echo '<tr><td colspan=3 valign=top><br><center><font style="font-size:12px;font-weight:bold">'.$this->db->f('ititle').'</font><br><font size="-3">'.$this->db->f('discription').'&nbsp;<br><br><br><Br></font></center></td></tr></table></td>';
					
					
				}
			}
			echo '</center>';
			
		}
		
		//show htmls
		if ( $types_sites[$extension] ) {
			$fcontents = join ('', file ($imagepath.$file));
			echo $fcontents;
		}
		
		//show text files
		if ( $types_text[$extension] ) {
			$fcontents = join ('', file ($imagepath.$file));
			echo '<pre>'.$fcontents.'</pre>';
		}
		
		//embed quicktime movies
		if ( $types_qtime[$extension] ) {
			echo "<center><EMBED SRC='$imagepath/$file' width=320 height =240 AUTOPLAY='true' CONTROLLER='true' CACHE='true' TYPE='video/quicktime' ></center>";
		}
		
		//embed real movies
		if ( $types_rmedia[$extension] ) {
			echo '<center><OBJECT ID=video1 CLASSID="clsid:CFCDAA03-8BE4-11cf-B84B-0020AFBBCCFA" BORDER="2">'."\n";
			echo '<PARAM NAME="controls" VALUE="ControlPanel">'."\n";
			echo '<PARAM NAME="console" VALUE="Clip1">'."\n";
			echo '<PARAM NAME="autostart" VALUE="true">'."\n";
			echo '<PARAM NAME="src" VALUE="'.$path.'/'.$file.'">'."\n";
			echo '<EMBED SRC="'.$imagepath.'/'.$file.'" TYPE="audio/x-pn-realaudio-plugin" CONSOLE="Clip1" CONTROLS="ControlPanel" AUTOSTART=true NOJAVA=true BORDER="2">'."\n";
			echo '</EMBED>'."\n";
			
			echo '</OBJECT></center>'."\n";
		}
		
		//embed flash movies
		if ( $types_flash[$extension] ) {
			echo '<center><OBJECT CLASSID="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" CODEBASE="http://active.macromedia.com/flash5/cabs/swflash.cab#version=5,0,0,0">'."\n";
			echo '<PARAM NAME="MOVIE" VALUE="'.$path.'/'.$file.'">'."\n";
			echo '<PARAM NAME="PLAY" VALUE="true">'."\n";
			echo '<PARAM NAME="LOOP" VALUE="true">'."\n";
			echo '<PARAM NAME="QUALITY" VALUE="high">'."\n";
			echo '<EMBED SRC="'.$imagepath.'/'.$file.'" WIDTH="320" HEIGHT="240" PLAY="true" LOOP="true" QUALITY="high" PLUGINSPAGE="http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash">'."\n";
			echo '</EMBED>'."\n";
			echo '</OBJECT>'."</center>\n\n";
		}
		
	}
	
}
?>