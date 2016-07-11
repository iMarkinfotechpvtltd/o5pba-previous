<?
class store{
	var $form;
	var $path;
	var $sizes=array("XS" => "Extra Small",
	"S"=>"Small",
	"M"=>"Medium",
	"L"=>"Large",
	"XL"=>"Extra Large",
	"XXL"=>"Extra Extra Large",
	"NA"=>"Not Applicable");
	
	function store($id=''){
		global $htdocsdir;
		$db=new DB;
		
		if ($id){
			$db->query(sprintf('select * from store where id like "%s"',$id));
			$db->next_record();
		}
		
		$this->form= new form;
		$this->path=$htdocsdir.'store/images/';
		
		
		$this->form->add_element(array("type"=>"text",
		"name"=>"name",
		"valid_regex"=>"^[a-z0-9A-Z ]*$",
		"valid_e"=>"<br><font color=red>Name is letters & numbers only.</font>",
		"minlength"=>"1",
		"length_e"=>"<br><font color=red>Name length error.</font>",
		"value"=>$db->f('name')));
		
		$this->form->add_element(array("type"=>"text",
		"name"=>"productid",
		"value"=>$db->f('productid')));
		
		$this->form->add_element(array("type"=>"textarea",
		"name"=>"description",
		"rows"=>10,
		"cols"=>30,
		"value"=>$db->f('description')
		));
		
		$this->form->add_element(array("type"=>"hidden",
		"name"=>"function",
		"value"=>'store_new'
		));
		
		$this->form->add_element(array("type"=>"hidden",
		"name"=>"action",
		"value"=>'validate'
		));
		
		$this->form->add_element(array("type"=>"text",
		"name"=>"price",
		"valid_regex"=>"^[0-9.]*$",
		"valid_e"=>"<br><font color=red>Price should be a number.</font>",
		"minlength"=>"1",
		"length_e"=>"<br><font color=red>Price length error.</font>",
		"value"=>$db->f('price')));
		
		$this->form->add_element(array("type"=>"hidden",
		"name"=>"id",
		"value"=>$id
		));
		
		$this->form->add_element(array("type"=>"file",
		"name"=>"picture"
		));
		
		$this->form->add_element(array("type"=>"submit",
		"name"=>"submit",
		"value"=>"Process"
		));
		
		$current=' '.$db->f('sizes').',';
		
		foreach ($this->sizes as $name=>$values){
			$n='sizes['.$name.']';
			$this->form->add_element(array("type"=>"checkbox",
			"name"=>$n,
			"value"=>$name,
			"checked"=>strpos($current,$name.',')
			));
		}
		
	}
	
	function edit($id){
		
		$db=new DB;
		
		if ($id){
			$db->query(sprintf('select * from store where id like "%s"',$id));
		}
		
		if ($db->num_rows()||$user_id==''){
			$this->store($id);
			
			$this->form();
		}else{
			$this->ilist();
		}
	}
	
	function form($validate=false){
		$this->form->start();
                ?>
                <table border="0" cellpadding="2" cellspacing="0" width=300>
				  <tr>
				  	<td colspan=2 class=dark width=300>
  		                <p align="center">Edit Item</p>
  		                <?php
                if ($validate){
                	echo 'Please fix the errors below.';
                }
  		                ?>
					</td>
                  </tr>
                
                  <tr>
                  <td>Item Name:</td><td>
		<?php
  		                $this->form->show_element("name");
  		                if ($validate){
  		                	echo $this->form->validate('',array('name'));
  		                }
		?>

	            	</td></tr>

	            	         <tr>
                  <td>Product Id:</td><td>
		<?php
		$this->form->show_element("productid");
		if ($validate){
			echo $this->form->validate('',array('productid'));
		}
		?>

	            	</td></tr>
                  <tr>
                  <td>Item Price:</td><td>
		<?php
		$this->form->show_element("price");
		if ($validate){
			echo $this->form->validate('',array('price'));
		}
        			?>

	            	</td></tr>
                  <tr>
                  <td>Sizes Available:</td><td>
        <?
        			$this->form->show_element("function");
        			$this->form->show_element("action");
        			$this->form->show_element("id");
        			foreach ($this->sizes as $name=>$values){
        				$n='sizes['.$name.']';
        				$this->form->show_element($n);
        				echo $values.'<br>';
        			}
		?>
							</td>
		                  </tr>
                  <tr>
                  <td>Description:</td><td>
        <?
		$this->form->show_element("description");
		?>
							</td>
		                  </tr>
		          <tr>
                  <td>Picture:</td><td><input type="hidden" name="MAX_FILE_SIZE" value="300000">
        <?
		$this->form->show_element("picture");
		?><br><br>Picture must be a jpg and less then 250kb.
							</td>
		                  </tr>
                  <tr>
                      <td colspan="2" class=light style='vertical-align:middle; text-align:center;'><? $this->form->show_element("submit"); ?></td>
                  </tr>
                </table>
                <?
		$this->form->finish();
		
	}
	
	function resize_img($path, $file){
		
			$src_img = imagecreatefromjpeg($path.$file);
			if (imagesx($src_img) > imagesy($src_img)){
				$new_w = 80;
				$new_h = imagesy($src_img)*(80/imagesx($src_img));
			}else{
				$new_w = imagesx($src_img)*(80/imagesy($src_img));
				$new_h = 80;
			}
			$dst_img = imagecreate($new_w,$new_h);
			imagecopyresized($dst_img,$src_img,0,0,0,0,$new_w,$new_h,imagesx($src_img),imagesy($src_img));
			imagejpeg($dst_img, $path.'t'.$file);
	
	}
	
	
	function validate(){
		
		if($this->form->validate()){
			$this->form->load_defaults();
			
			$this->form(true);
		}else{
			$id=$_POST['id'];
			
			@ $sizes=join($_POST['sizes'],',');
			
			$db=new DB;
			if ($id){
				$query=sprintf('update store set name = "%s", sizes = "%s", price= "%s", description="%s", productid="%s" where id = "%s"',$_POST['name'],$sizes,$_POST['price'],$_POST['description'],$_POST['productid'],$id);
				$db->query($query);
				logit(str_replace('"','&quot;',$query));
			}else{
				$query=sprintf('insert into store (name,sizes,price,description,productid) values ("%s","%s","%s","%s","%s")', $_POST['name'],$sizes, $_POST['price'],$_POST['description'],$_POST['productid']);
				$db->query($query);
				logit(str_replace('"','&quot;',$query));
				
				$query=sprintf('select id from store where name="%s" and sizes="%s" and price="%s" and description="%s"', $_POST['name'],$sizes, $_POST['price'],$_POST['description']);
				$db->query($query);
				$db->next_record();
				$id=$db->f('id');
			}
			
			if(is_uploaded_file($_FILES['picture']['tmp_name'])){
				copy ($_FILES['picture']['tmp_name'],$this->path.$id.strtolower(substr($_FILES['picture']['name'],strpos($_FILES['picture']['name'],"."))));
				$this->resize_img($this->path,$id.strtolower(substr($_FILES['picture']['name'],strpos($_FILES['picture']['name'],"."))));
			}
			$this->ilist();
		}
	}
	function ilist(){
		$db=new DB;
		
		$db->query('select * from store;');
		echo '<h4>Edit Store Items</h4>';
		echo '<table border="0" cellpadding="2" cellspacing="0" width="500">';
		echo '<tr class=dark><td width=100></td><td>Sizes</td><td colspan=1 width=200>Item Name</td><td>Price</td><Td>Picture</td><td></td></tr>';
		while($db->next_record()){
			if(file_exists($this->path.$db->f('id').'.jpg')){
				$file='Y';
			}else{
				$file='N';
			}
			echo sprintf('<tr class=light><td width=60><a href="?function=store_edit&id=%s">Edit</a></td><td width=40>&nbsp;%s</td><td width=240>%s</td></td><td>%s</td><td>%s</td><td width=60><a href="javascript:confirmDelete(\'%s\',\'?function=store_delete&id=%s\');">Delete</td></tr>',$db->f('id'),$db->f('sizes'),$db->f('name'),$db->f('price'),$file,$db->f('name'),$db->f('id'));
		}
		echo'<tr class=light><td colspan=5><a href="?function=store_new">Add Item</a></td></tr>';
		echo '</table>';
	}
	
	function delete($id){
		
		$db=new DB;
		$query=sprintf('delete from store where id like "%s";',$id);
		$db->query($query);
		logit(str_replace('"','&quot;',$query));
		$this->ilist();
	}
	
}

?>