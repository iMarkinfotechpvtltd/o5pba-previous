<?php

class track_members {



    var $dbhost = "localhost";

    var $dbuser = "ode";

    var $dbpass = "fgu65r92d3";



    function dbconnect($db){

           $conn=mysql_pconnect("$this->dbhost","$this->dbuser","$this->dbpass");

           mysql_select_db($db,$conn);

           return $conn;

    }

    function dbconnect_admin($db){

           $conn=mysql_pconnect("localhost","root","w00dbridge");

           mysql_select_db($db,$conn);

           return $conn;

    }

    function set_dbs_allowed($username,$num){

           if(mysql_query("UPDATE auth_user_md5 SET dbs_allowed='$num' where username='$username'")){

             return true;

             }

             else{

                  return false;

             }

    }

    function activate($username){

             if(mysql_query("UPDATE auth_user_md5 SET state='1' where username='$username'")){

                return true;

             }

             else{

                  return false;

             }



    }

    function deactivate($username){

             if(mysql_query("UPDATE auth_user_md5 SET state='0' where username='$username'")){

                return true;

             }

             else{

                  return false;

             }



    }

    function delete($username){

        if(mysql_query("DELETE from auth_user_md5 where username='$username'")){

            return true;

        }

        else{

            return false;

        }



    }



    function user_list(){

             echo '<table border=0 width="640">';

             echo '<tr bgcolor="#C3C3C3"><td width="100">Username</td><td width="100">Name</td><td width="340">E-Mail</td></tr>'."\n";

             $result=mysql_query("SELECT * from auth_user_md5 where perms='manager'");

             while($row=mysql_fetch_array($result)){

                   echo '<tr bgcolor="#EDEDED"><td width="100"><a href="admin.php?cur_user='.$row["username"].'&action=user_data">'.$row["username"].'</a></td><td width="200">'.$row["name"].'</td><td>'.$row["email"].'</td></tr>';

             }

             echo '</table></form>';

    }

    function user_data($username){

             require("oohforms.inc");

             $f = new form;



             $result=mysql_query("SELECT * FROM auth_user_md5 where username='$username'");

             if($row=mysql_fetch_array($result)){

             $f->add_element(array("type"=>"text",

                       "name"=>"dbs_allowed",

                       "size"=>"2",

                       "value"=>$row["dbs_allowed"]

             ));

                $f->start("","post","processuser.php","");

                echo '<table border=0 width="400">';

                echo '<tr><td bgcolor="#C3C3C3" width="100">Username</td><td bgcolor="#EDEDED" width="300">'.$row["username"].'</td></tr>';

                echo '<tr><td bgcolor="#C3C3C3" width="100">Name</td><td bgcolor="#EDEDED" width="300">'.$row["name"].'</td></tr>';

                echo '<tr><td bgcolor="#C3C3C3" width="100">E-mail</td><td bgcolor="#EDEDED" width="300">'.$row["email"].'</td></tr>';

                echo '<tr><td bgcolor="#C3C3C3" width="100">Phone</td><td bgcolor="#EDEDED" width="300">'.$row["phone"].'</td></tr>';

                echo '<tr><td bgcolor="#C3C3C3" width="100">Address</td><td bgcolor="#EDEDED" width="300">'.$row["address"].'</td></tr>';

                echo '<tr><td bgcolor="#C3C3C3" width="100">City</td><td bgcolor="#EDEDED" width="300">'.$row["city"].'</td></tr>';

                echo '<tr><td bgcolor="#C3C3C3" width="100">Province</td><td bgcolor="#EDEDED" width="300">'.$row["province"].'</td></tr>';

                echo '<tr><td bgcolor="#C3C3C3" width="100">Postal Code</td><td bgcolor="#EDEDED" width="300">'.$row["postal_code"].'</td></tr>';

                echo '<tr><td bgcolor="#C3C3C3" width="100">Country</td><td bgcolor="#EDEDED" width="300">'.$row["country"].'</td></tr>';

                echo '<tr><td bgcolor="#C3C3C3" width="100">Databases Allowed</td><td bgcolor="#EDEDED" width="300">';$f->show_element("dbs_allowed");echo '<input type="submit" name="action" value="Apply"></td></tr>';

                echo '</table>';



                if($row["state"]=="0"){

                    echo '<input type="submit" name="action" value="Activate">';

                }else{

                    echo '<input type="submit" name="action" value="Deactivate">';

                }

                echo '<input type="submit" name="action" value="Delete">';



                $f->finish();

             }

    }

    function createdb($dbname){

             $conn=mysql_pconnect("localhost","root","w00dbridge");

             if(mysql_create_db($dbname)){

                   exec('mysql -u root -pw00dbridge '.$dbname.' <./includes/create.db');

                   mysql_select_db("mysql",$conn);

                   $query="insert into db values('localhost','$dbname','ode','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y')";

                   mysql_query($query);

                   mysql_close($conn);

                   return true;

             }else{

                   mysql_close($conn);

                   return false;

             }

    }

    function deletedb($dbname){

             $conn=mysql_pconnect("localhost","root","w00dbridge") or die (print mysql_error());

             if(mysql_drop_db($dbname)){

                mysql_select_db("mysql",$conn);

                mysql_query("delete from db where User='$dbname'");

                mysql_close($conn);

                return true;

             }else{

                return false;

             }

    }

    function insertdb($username){

             $this->dbconnect("data");

             $result=mysql_query("SELECT user_id,username FROM auth_user_md5 where username='$username'");

             if($row=mysql_fetch_array($result)){

                $query= 'insert into dbs values(\''.$row["user_id"].'\', \''.$username.'\', \'0\')';

                mysql_query($query);

             }

    }

    function profile_form($data, $url, $action="new",$uid=""){

    require("oohforms.inc");

 $f = new form;

 $elements=array("name","email","phone","address","city","province","postal_code","other","step","country");

 $f->add_element(array("type"=>"text",

                                 "name"=>"name",

                                 "size"=>"30",

                                 "minlength"=>"1",

                                 "length_e"=>"Please enter your name.",

                                 "value"=>$data["name"]

 ));

 $f->add_element(array("type"=>"text",

                                 "name"=>"email",

                                 "size"=>"30",

                                 "valid_regex"=>"^[a-zA-Z0-9|\.|\-]*@[a-zA-Z0-9|\.|\-]*$",

                                 "valid_e"=>"Incorrect e-mail address.",

                                 "value"=>$data["email"]

 ));

 $f->add_element(array("type"=>"text",

                                 "name"=>"phone",

                                 "size"=>"12",

                                 "valid_regex"=>"^[0-9]{3}\-[0-9]{3}\-[0-9]{4}$",

                                 "valid_e"=>"Incorrect telephone number.",

                                 "value"=>$data["phone"]

 ));

 $f->add_element(array("type"=>"text",

                                 "name"=>"address",

                                 "size"=>"30",

                                 "minlength"=>"1",

                                 "length_e"=>"Please enter school address.",

                                 "value"=>$data["address"]

 ));

 $f->add_element(array("type"=>"text",

                                 "name"=>"city",

                                 "size"=>"10",

                                 "minlength"=>"1",

                                 "length_e"=>"Please enter city.",

                                 "value"=>$data["city"]

 ));

 $f->add_element(array("type"=>"text",

                                 "name"=>"province",

                                 "size"=>"10",

                                 "minlength"=>"1",

                                "length_e"=>"Please select a Province/State",

                                "value"=>$data["province"]

 ));

 $f->add_element(array("type"=>"text",

                                 "name"=>"postal_code",

                                 "size"=>"10",

                                 "minlength"=>"5",

                                 "length_e"=>"Incorrect postal code/zip number.",

                                 "value"=>$data["postal_code"]

 ));

  $f->add_element(array("type"=>"text",

                                 "name"=>"country",

                                 "size"=>"10",

                                 "minlength"=>"1",

                                 "length_e"=>"Please Enter a Country",

                                 "value"=>$data["country"]

 ));

 $f->add_element(array("type"=>"hidden",

                                 "name"=>"step",

                                 "value"=>"submit"

 ));

 $f->add_element(array("type"=>"submit",

                                 "name"=>"submit",

                                 "value"=>"Update"

 ));

  if($action=="submit"){

    $results=$f->validate("ok", $elements);

    if($results!="ok"){

        $f->load_defaults($elements);

        $error=true;

    }

 }

 if($error==true || $action=="new"){

 $f->start("","",$url,"");

 ?>

   <table border="0" width="100%" height="151">

     <tr bgcolor="#C3C3C3">

       <td width="100%" colspan="2">Contact Info</td>

     </tr>

     <tr bgcolor="#EDEDED">

       <td width="23%" height="19" align="right">Name</td>

       <td width="77%" height="19"><?php $f->show_element("name");

       if($error==true){

          $test=$f->validate("OK",array("name"));

       }

       if($test!="OK"){

          print '<font color="red">'.$test.'</font>';

       }

     ?></td>

     </tr>

     <tr bgcolor="#EDEDED">

       <td width="23%" height="19" align="right">E-mail</td>

       <td width="77%" height="19">       <?php

       $f->show_element("email");

       if($error==true){

          $test=$f->validate("OK",array("email"));

       }

       if($test!="OK"){

          print '<font color="red">'.$test.'</font>';

       }

     ?></td>

     </tr>

     <tr bgcolor="#EDEDED">

       <td width="23%" height="19" align="right">Contact Phone Number</td>

       <td width="77%" height="19"><?php

       $f->show_element("phone");

       print '(Eg. 555-555-5555) ';

       if($error==true){

          $test=$f->validate("OK",array("phone"));

       }

       if($test!="OK"){

          print '<font color="red">'.$test.'</font>';

       }

     ?></td>

     </tr>

     <tr bgcolor="#C3C3C3">

       <td align="left" colspan="2">School Information</td>

     </tr>

     <tr bgcolor="#EDEDED">

       <td width="23%" height="19" align="right">School Address</td>

       <td width="77%" height="19"><?php $f->show_element("address");

       if($error==true){

          $test=$f->validate("OK",array("address"));

       }

       if($test!="OK"){

          print '<font color="red">'.$test.'</font>';

       }?></td>

     </tr>

     <tr bgcolor="#EDEDED">

       <td width="23%" height="19" align="right">City</td>

       <td width="77%" height="19"><?php $f->show_element("city");

       if($error==true){

          $test=$f->validate("OK",array("city"));

       }

       if($test!="OK"){

          print '<font color="red">'.$test.'</font>';

       }?></td>

     </tr>

     <tr bgcolor="#EDEDED">

       <td width="23%" height="19" align="right">Province</td>

       <td width="77%" height="19"><?php

       $f->show_element("province");

         if($error==true){

          $test=$f->validate("OK",array("province"));

       }

       if($test!="OK"){

          print '<font color="red">'.$test.'</font>';

       }?></td>

     </tr>

     <tr bgcolor="#EDEDED">

       <td width="23%" height="19" align="right">Postal Code</td>

       <td width="77%" height="19"><?php $f->show_element("postal_code");

       if($error==true){

          $test=$f->validate("OK",array("postal_code"));

       }

       if($test!="OK"){

          print '<font color="red">'.$test.'</font>';

       }?></td>

     </tr>

     <tr bgcolor="#EDEDED">

       <td width="23%" height="19" align="right">Country</td>

       <td width="77%" height="19"><?php $f->show_element("country");

       if($error==true){

          $test=$f->validate("OK",array("country"));

       }

       if($test!="OK"){

          print '<font color="red">'.$test.'</font>';

       }?></td>

     </tr>

   </table>

   <div align="center"><center><p></p><?php $f->show_element("step")?><?php $f->show_element("submit")?>

   </center></div>

 </form>

 <?php

 $f->finish();

 }

 elseif($error==false && $action="submit"){

                        $db=new DB_Example;

                        $name = $data["name"];

                        $addr = $data["address"];

                        $email = $data["email"];

                        $phone = $data["phone"];

                        $city = $data["city"];

                        $province = $data["province"];

                        $postal_code = $data["postal_code"];

                        $country=$data["country"];

                        $query = 'update auth_user_md5 set name="'.$name.'", address="'.$addr.'", city="'.$city.'", province="'.$province.'", postal_code="'.$postal_code.'", phone="'.$phone.'", email="'.$email.'", country="'.$country.'" where user_id="'.$uid.'"';

                        $db->query($query);

                        print "<h2>Updated</h2>\n";

 }

}

 function import_dbase($table, $file, $dbout){

          $db=$table;

          mysql_pconnect("localhost", "root", "w00dbridge") or die (print mysql_error());

          @mysql_select_db($dbout) or die (print mysql_error());

          $a = dbase_open($file, 0);

          for($i=1; $i <= dbase_numrecords($a); $i++){

              $rec = dbase_get_record_with_names($a, $i);

              list ($key, $val) = each ($rec);

              $val1='"'.$val.'"';

              for($j=1; $j <= dbase_numfields($a); $j++){

                  list ($key, $val) = each ($rec);

                  $val1=$val1.', "'.$val.'"';

              }

              //print $val1;

          mysql_query("INSERT INTO $db values ($val1)") or die (print mysql_error());

          }

 }

 function create_table($file, $tablename){

          $a = dbase_open($file, 0);

          $rec = dbase_get_record_with_names($a, 1);

          echo "create table $tablename(";

          for($i=0;$i <= dbase_numfields($a);$i++){

              list ($key, $val) = each ($rec);

              echo "$key varchar(255)";

              if($i != dbase_numfields($a)){

                 echo ",<br>";

              }else{

                 echo ')\\g';

              }

          }

 }

 function archive($in){

          $conn=$this->dbconnect_admin($in);

          $result=mysql_query("select * from meet");

	echo '1';

          if($row=mysql_fetch_array($result)){

              $val1='""';

              for($j=1; $j <= (mysql_num_fields($result)-1); $j++){

                  $val1=$val1.', "'.$row[$j].'"';

              }

              mysql_select_db("data");

              $name=substr($in,2);

              $result2=mysql_query("select maker,name FROM files where name='$name'");

              $temp=mysql_fetch_array($result2);

              $maker=$temp["maker"];

              $val1=$val1.', "'.$maker.'"';

              mysql_select_db("archive",$conn);

              mysql_query("insert into meet values($val1)")or die (print mysql_error());

              $beg_date=$row["BEG_DATE"];

              $end_date=$row["END_DATE"];

              $meet_name=$row["MEET_NAME"];

              $result2=mysql_query("select MEET_NO,MEET_NAME,maker,BEG_DATE,END_DATE from meet where MEET_NAME='$meet_name' and BEG_DATE='$beg_date' and END_DATE='$end_date' and maker='$maker'") or die(print mysql_error());

              if($row2=mysql_fetch_array($result2)){

                 $meet_no=$row2["MEET_NO"];

              }

          }

          mysql_select_db($in,$conn);

	echo '2';

          $result=mysql_query("select * from athlete");

          while($row=mysql_fetch_array($result)){

              $val1="\"$meet_no\"";

              for($j=0; $j <= mysql_num_fields($result)-1; $j++){

                  $val1=$val1.', "'.$row[$j].'"';

              }

              mysql_select_db("archive",$conn);

              mysql_query("insert into athlete values($val1)") or die(print mysql_error());

              mysql_select_db($in,$conn);

          }

	echo '3';

          $result=mysql_query("select * from entry");

          while($row=mysql_fetch_array($result)){

              $val1="\"$meet_no\"";

              for($j=0; $j <= mysql_num_fields($result)-1; $j++){

                  $val1=$val1.', "'.$row[$j].'"';

              }

              mysql_select_db("archive",$conn);

              mysql_query("insert into entry values($val1)") or die(print mysql_error());

              mysql_select_db($in,$conn);

          }

	echo '4';

          $result=mysql_query("select * from team");

          while($row=mysql_fetch_array($result)){

              $val1="\"$meet_no\"";

              for($j=0; $j <= mysql_num_fields($result)-1; $j++){

                  $val1=$val1.', "'.$row[$j].'"';

              }

              mysql_select_db("archive",$conn);

              mysql_query("insert into team values($val1)") or die(print mysql_error());

              mysql_select_db($in,$conn);

          }

	echo '5';

          $result=mysql_query("select * from event");

          while($row=mysql_fetch_array($result)){

              $val1="\"$meet_no\"";

              for($j=0; $j <= mysql_num_fields($result)-1; $j++){

                  $val1=$val1.', "'.$row[$j].'"';

              }

              mysql_select_db("archive",$conn);

              mysql_query("insert into event values($val1)") or die(print mysql_error());

              mysql_select_db($in,$conn);

          }



 }

}

?>