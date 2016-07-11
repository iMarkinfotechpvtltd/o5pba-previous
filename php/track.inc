<?php
class Track {

      var $dbhost = "localhost";
      var $dbuser = "ode";
      var $dbpass = "fgu65r92d3";
      var $event_b = array( "1" => "Midget",
                        "4" => "Open",
                        "5" => "Ambulatory",
                        "6" => "Blind",
                        "2" => "Junior",
                        "3" => "Senior",
                        "7" => "Wheelchair"
        );
      var $event_t = array( "W" => "Relay",
                        "X" => "Sprint Medley",
                        "Y" => "Distance Medley",
                        "A" => "Dash",
                        "B" => "Run",
                        "C" => "Mile Run",
                        "D" => "Race Walk",
                        "E" => "Low Hurdles",
                        "F" => "Int Hurdles",
                        "G" => "High Hurdles",
                        "H" => "Steeplechase",
                        "I" => "Half Marathon",
                        "J" => "Marathon",
                        "K" => "High Jump",
                        "L" => "Pole Vault",
                        "M" => "Long Jump",
                        "N" => "Triple Jump",
                        "O" => "Discus Throw",
                        "P" => "Hammer Throw",
                        "Q" => "Javalin Throw",
                        "R" => "Shot Put",
                        "S" => "Weight Throw",
                        "T" => "Other"
        );
        var $event_g = array(   "W" => "Womens",
                        "M" => "Mens",
                        "X" => "Mixed"
        );
        var $gender = array( "M" => "Male",
                             "F" => "Female",
                             "W" => "Female",
                             "B" => "Male"
        );
        var $round = array("1" => array("FIN"),
                            "2" => array("PRE","FIN"),
                            "3" => array("PRE","SEM","FIN"),
                            "4" => array("PRE","QTR","SEM","FIN")
        );
        var $photoround = array("FIN"=>"2",
                                 "PRE"=>"1"
        );

        //DB Connect
        function dbconnect($db){
                 mysql_pconnect("$this->dbhost","$this->dbuser","$this->dbpass") or die (print mysql_error());
                 @mysql_select_db($db) or die (print mysql_error());
        }
        function database_stats(){
                 $results = mysql_query("SELECT ath_no FROM athlete");
                 $temp["Athletes"]=mysql_num_rows($results);
                 $results = mysql_query("SELECT event_no FROM event");
                 $temp["Events"]=mysql_num_rows($results);
                 $results = mysql_query("SELECT event_no FROM entry");
                 $temp["Entrys"]=mysql_num_rows($results);
                 $results = mysql_query("SELECT team_no FROM team");
                 $temp["Teams"]=mysql_num_rows($results);
                 return $temp;
        }
        function event_name($en){
                 $results = mysql_query("SELECT EV_NAME,EVENT_NO,BRACK_NO,SEX,DIST,TRK_FIELD FROM event WHERE EVENT_NO LIKE '$en'");
                 if($row = mysql_fetch_array($results)){
                    $event = $this->event_b[$row["BRACK_NO"]]." ".$this->event_g[$row["SEX"]];
                    if($row["TRK_FIELD"]=="T"){
                       $event = $event." ".$row["DIST"]." Meter ".$this->event_t[$row["EV_NAME"]];
                    }else{
                       $event = $event." ".$this->event_t[$row["EV_NAME"]];
                    }
                 }else{
                    return false;
                 }
            return $event;
        }
        function event_type($en){
                 $results = mysql_query("SELECT TRK_FIELD,EVENT_NO FROM event WHERE EVENT_NO='$en'") or die (print mysql_error());
                 if($row = mysql_fetch_array($results)){
                    return $row["TRK_FIELD"];
                 }
        }
        function event_list($inbound, $database){
                 echo '<center><h1>Event Listings</h1><p>';
                 $this->page_list("event", $database, $inbound);
                 echo '<table width="400" border=0>';
                 echo '<tr bgcolor="#C3C3C3"><td width="10"><font color="white"><center>#</a></center></font></td><td><font color="white">Event Name</font></td></tr>';
                 $results = mysql_query("SELECT EVENT_NO FROM event LIMIT $inbound,20") or die (print mysql_error());
                 $events = array();
                 while($row=mysql_fetch_array($results)){
                          echo '<tr bgcolor="#EDEDED"><td><center>'.$row["EVENT_NO"].'</center></td><td><a href="test.php?action=event_data&event_no='.$row["EVENT_NO"].'&database='.$database.'">';
                          echo $this->event_name($row["EVENT_NO"])."</a></td></tr>";
                 }
                 echo '</table>';
                 $this->page_list("event", $database, $inbound);

        }
         function team_list($inbound, $database){
                 echo '<center><h1>Team Listings</h1><p>';
                 $this->page_list("team", $database, $inbound);
                 echo '<table width="400" border=0>';
                 echo '<tr bgcolor="#C3C3C3"><td width="10"><font color="white"><center>#</a></center></font></td><td><font color="white">Team Name</font></td></tr>';
                 $results = mysql_query("SELECT TEAM_NO,TEAM_NAME FROM team LIMIT $inbound,20") or die (print mysql_error());
                 $events = array();
                 while($row=mysql_fetch_array($results)){
                          echo '<tr bgcolor="#EDEDED"><td><center>'.$row["TEAM_NO"].'</center></td><td><a href="test.php?action=team_data&database='.$database.'&team_no='.$row["TEAM_NO"].'">';
                          echo $row["TEAM_NAME"]."</a></td></tr>";
                 }
                 echo '</table><br>';
                 $this->page_list("team", $database, $inbound);

        }
        function athlete_list($inbound, $database){
                 echo '<center><h1>Athlete Listings</h1><p>';
                 $this->page_list("athlete", $database, $inbound);
                 echo '<table width="400" border=0>';
                 echo '<tr bgcolor="#C3C3C3"><td width="20"><font color="white"><center>#</a></center></font></td><td width="480"><font color="white">Athlete Name</font></td></tr>';
                 $results = mysql_query("SELECT ATH_NO,FIRST_NAME,LAST_NAME FROM athlete LIMIT $inbound,20") or die (print mysql_error());
                 $events = array();
                 while($row=mysql_fetch_array($results)){
                          echo '<tr bgcolor="#EDEDED"><td width="20"><center>'.$row["ATH_NO"].'</center></td><td width="480"><a href="test.php?action=athlete_data&ath_no='.$row["ATH_NO"].'&database='.$database.'">';
                          echo $row["FIRST_NAME"]." ".$row["LAST_NAME"]."</a></td></tr>";

                 }
                 echo '</table><br>';
                 $this->page_list("athlete", $database, $inbound);

        }
        function event_limits($en){
                 $fields="";
                 $temp=array();
                 $results=mysql_query("SELECT SEEDING,EVENT_NO FROM event WHERE EVENT_NO='$en'") or die (print mysql_error());
                 if($row=mysql_fetch_array($results)){$rounds=$row["SEEDING"];}
                 else{return false;}
                 for($i=0;$i<=$rounds-1;$i++){
                     $fields=$fields.$this->round[$rounds][$i]."_HEAT";
                     $temp[$this->round[$rounds][$i]]="0";
                     if($i!=$rounds-1){$fields=$fields.",";}
                 }
                 $results=mysql_query("SELECT event_no,$fields FROM entry WHERE EVENT_NO='$en'") or die (print mysql_error());
                 while($row=mysql_fetch_array($results)){
                       for($i=0;$i<=$rounds-1;$i++){
                           if($row[$this->round[$rounds][$i]."_HEAT"] > $temp[$this->round[$rounds][$i]]){
                             $temp[$this->round[$rounds][$i]]=$row[$this->round[$rounds][$i]."_HEAT"];
                           }
                       }
                 }
                 return $temp;
        }

        function athlete_name($an){
                 $results = mysql_query("SELECT FIRST_NAME,LAST_NAME,ATH_NO FROM athlete WHERE ATH_NO LIKE $an");
                 if($row=mysql_fetch_array($results)){
                    $name = $row["FIRST_NAME"]." ".$row["LAST_NAME"];
                 }
                 return $name;
        }
        function heat_results($en, $round, $heat, $database){
                 $h_place=$round."_HPLACE";
                 $h_time=$round."_TD";
                 $r_place=$round."_PLACE";
                 $r_heat=$round."_HEAT";
                 $incomplete=array();

                 $results = mysql_query("SELECT MAX_FINAL,EVENT_NO FROM event WHERE EVENT_NO='$en'");
                 if($row=mysql_fetch_array($results)){$max_final=$row["MAX_FINAL"];}

                 echo '<table width="500" border=0>';
                 echo '<tr><td bgcolor="#21269A" width="50"><font color="white">Heat Place</font></td>';
                 if($round!="FIN"){echo '<td  bgcolor="#21269A" width="50"><font color="white">Round Place</font></td>';}
                 echo '<td  bgcolor="#21269A" width="275"><font color="white">Name</font></td>';
                 echo '<td bgcolor="#21269A"width="75"><font color="white">Results</font></td>';
                 if($round!="FIN"){echo '<td bgcolor="#21269A" width="50"><font color="white">Qualify</font></td></tr>';}
                 else{echo '<td bgcolor="#21269A" width="50"><font color="white">Points</font></td></tr>';}

                 $result = mysql_query("SELECT EVENT_NO,$h_time,ATH_NO,$h_place,$r_place,$r_heat,EV_SCORE FROM entry WHERE EVENT_NO='$en' AND $r_heat='$heat' ORDER BY $h_place") or die (print mysql_error());
                 while($row=mysql_fetch_array($result)){
                       if($row[$h_place]!=0){
                          echo '<tr bgcolor="#EDEDED"><td>';
                          echo $row[$h_place]."</td><td>";
                          if($round!="FIN"){echo $row[$r_place]."</td><td>";}
                          echo '<a href="test.php?action=athlete_data&ath_no='.$row["ATH_NO"].'&database='.$database.'">'.$this->athlete_name($row["ATH_NO"])."</a></td><td>";
                          echo $row[$h_time]."</td>";
                          if($round!="FIN" && $row[$r_place] <= $max_final){echo '<td>Yes</td>';}
                          if($round=="FIN"){echo '<td>'.$row["EV_SCORE"].'</td>';}
                          echo "</tr>";
                          if($row=mysql_fetch_array($result)){
                             if($row[$h_place]!=0){
                                echo '<tr bgcolor="#C3C3C3"><td>';
                                echo $row[$h_place]."</td><td>";
                                if($round!="FIN"){echo $row[$r_place]."</td><td>";}
                                echo '<a href="test.php?action=athlete_data&ath_no='.$row["ATH_NO"].'&database='.$database.'">'.$this->athlete_name($row["ATH_NO"])."</a></td><td>";
                                echo $row[$h_time]."</td>";
                                if($round!="FIN" && $row[$r_place] <= $max_final){echo '<td>Yes</td>';}
                                if($round=="FIN"){echo '<td>'.$row["EV_SCORE"].'</td>';}
                                echo "</tr>";
                             }else{
                                 array_push($incomplete, $this->athlete_name($row["ATH_NO"]));
                             }
                          }
                       }else{
                          array_push($incomplete, $this->athlete_name($row["ATH_NO"]));
                       }
                 }
                 echo '<tr bgcolor="#F0F0F0"><td><b>No Results : </b></td>';
                 if($round!="FIN"){echo '<td colspan="4">';}
                 else{echo '<td colspan="3">';}
                 while (list($key, $name) = each($incomplete)){
                        echo $name.", ";
                 }
                 echo "</td></tr></table>";
        }
        function get_points($an){
                 $score="0";
                 $results=mysql_query("SELECT EV_SCORE,ATH_NO FROM entry WHERE ATH_NO like '$an'");
                 while($row=mysql_fetch_array($results)){
                       $score=$score+$row["EV_SCORE"];
                 }
                 return $score;
        }
        function team_data($tn, $database){
                 $totalscore="0";
                 $results = mysql_query("SELECT athlete.*,team.* FROM team, athlete WHERE team.team_no='$tn' AND athlete.team_no='$tn'");
                 if($row=mysql_fetch_array($results)){
                    echo '<table width="500" border=0>';
                    echo '<tr><td bgcolor="#21269A" colspan="2"><font color="white">Team Info</font></td></tr>';
                    echo '<tr><td bgcolor="#C3C3C3" width="100">Team Name</td><td bgcolor="#EDEDED" width="400">'.$row["TEAM_NAME"].'</td></tr>';
                    echo '<tr><td bgcolor="#C3C3C3" width="100">Team Abbr.</td><td bgcolor="#EDEDED" width="400">'.$row["TEAM_ABBR"].'</td></tr>';
                    echo '<tr><td bgcolor="#C3C3C3" width="100">Head Coach</td><td bgcolor="#EDEDED" width="400">'.$row["HEAD_COACH"].'</td></tr>';
                    echo '<tr><td bgcolor="#C3C3C3" width="100">Address</td><td bgcolor="#EDEDED" width="400">'.$row["ADDRESS1"].'</td></tr>';
                    echo '<tr><td bgcolor="#C3C3C3" width="100">City</td><td bgcolor="#EDEDED" width="400">'.$row["CITY"].'</td></tr></table>';
                    mysql_data_seek($results, "0");
                    echo '<table width="500"><tr bgcolor="#21269A"><td width="380"><font color="white">Athlete Name</font></td><td width="100"><font color="white"><center>Gender</center></font></td><td width="20"><font color="white"><center>Points</center></font></td></tr>';
                    while($row=mysql_fetch_array($results)){
                       echo '<tr bgcolor="#';
                       if($color){echo 'EDEDED'; $color=false;}
                       else{echo 'C3C3C3'; $color=true;}
                       $points=$this->get_points($row["ATH_NO"]);
                       echo '"><td width="380"><a href="test.php?action=athlete_data&database='.$database.'&ath_no='.$row["ATH_NO"].'">'.$row["FIRST_NAME"]." ".$row["LAST_NAME"].'</a></td><td width="100"><center>'.$this->gender[$row["SEX"]].'</center></td><td width="20"><center>'.$points."</center></td></tr>";
                       $totalscore=$totalscore+$points;
                    }
                    echo '<tr bgcolor="#';
                       if($color){echo 'EDEDED'; $color=false;}
                       else{echo 'C3C3C3'; $color=true;}
                       echo '"><td colspan="2"><b>Total Points</b></td><td width="20"><b><center>'.$totalscore.'</center></b></td></tr>';
                    echo '</table>';
                 }else{
                       echo '<h1>No Team Members</h1>';
                 }
        }
        function athlete_data($an, $database){
                 $totalscore=0;
                 $result = mysql_query("SELECT athlete.*,team.team_no,team.TEAM_NAME,entry.*,event.* FROM athlete,team,entry,event WHERE athlete.ath_no='$an' AND team.team_no=athlete.team_no AND entry.ath_no='$an' AND entry.event_no=event.event_no") or die (print mysql_error());
                 if($row=mysql_fetch_array($result)){
                    echo '<table width="640" border=0><tr><td>';
                    echo '<table width="500" border=0>';
                    echo '<tr><td bgcolor="#21269A" colspan="2"><font color="white">Athlete Info</font></td></tr>';
                    echo '<tr><td bgcolor="#C3C3C3" width="100">First Name</td><td bgcolor="#EDEDED" width="400">'.$row["FIRST_NAME"].'</td></tr>';
                    echo '<tr><td bgcolor="#C3C3C3" width="100">Last Name</td><td bgcolor="#EDEDED" width="400">'.$row["LAST_NAME"].'</td></tr>';
                    echo '<tr><td bgcolor="#C3C3C3" width="100">Team</td><td bgcolor="#EDEDED" width="400"><a href="test.php?action=team_data&database='.$database.'&team_no='.$row["TEAM_NO"].'">'.$row["TEAM_NAME"].'</a></td></tr>';
                    echo '<tr><td bgcolor="#C3C3C3" width="100">Gender</td><td bgcolor="#EDEDED" width="400">'.$this->gender[$row["SEX"]].'</td></tr></table>';
                    echo '</td><td width="250"><b>H</b> = Heat<br><b>HP</b> = Heat Place<br><b>RP</b> = Round Place<br><b>R</b> = Results<br><b>SCR</b> = Score/Points</td></tr></table>';
                    mysql_data_seek($result, "0");


                 echo '<table width="640" border="0"><tr bgcolor="#21269A"><td rowspan="2" width="27%"><font color="white"><center>Entries</center></font></td>';
                 echo '<td width="17%" colspan="4"><font color="white"><center>Preliminary</center></font></td><td width="17%" colspan="4"><font color="white"><center>Quarter-finals</center></font></td><td width="17%" colspan="4"><font color="white"><center>Semi-finals</center></font></td><td width="17%" colspan="4"><font color="white"><center>Finals</center></font></td><td width="5%" rowspan="2"><font color="white">SCR</font></td>';
                 echo '<tr bgcolor="#21269A"><td width="3%"><font color="white"><center>H</center></font></td><td width="3%"><font color="white"><center>HP</center></font></td><td width="4%"><font color="white"><center>RP</center></font></td><td width="7%"><font color="white"><center>R</center></font></td>';
                 echo '<td width="3%"><font color="white"><center>H</center></font></td><td width="3%"><font color="white"><center>HP</center></font></td><td width="4%"><font color="white"><center>RP</center></font></td><td width="7%"><font color="white"><center>R</center></font></td>';
                 echo '<td width="3%"><font color="white"><center>H</center></font></td><td width="3%"><font color="white"><center>HP</center></font></td><td width="4%"><font color="white"><center>RP</center></font></td><td width="7%"><font color="white"><center>R</center></font></td>';
                 echo '<td width="3%"><font color="white"><center>H</center></font></td><td width="3%"><font color="white"><center>HP</center></font></td><td width="4%"><font color="white"><center>RP</center></font></td><td width="7%"><font color="white"><center>R</center></font></td>';
                 echo '</tr>';
                 while($row=mysql_fetch_array($result)){
                       echo '<tr bgcolor="#';
                       if($color){echo 'EDEDED'; $color=false;}
                       else{echo 'C3C3C3'; $color=true;}
                       echo '"><td width="27%"><font size="-1"><a href="test.php?action=event_data&database='.$database.'&event_no='.$row["EVENT_NO"].'">'.$this->event_name($row["EVENT_NO"]).'</a></font></td>';
                       for($i=0;$i<=3;$i++){
                           if($row[($this->round["4"][$i])."_HEAT"] == "0" ){
                              echo '<td width="16%" colspan="4"></td>';
                           }else{
                              echo '<td width="3%"><center>'.$row[($this->round["4"][$i])."_HEAT"].'</center></td>';
                              echo '<td width="3%"><center>'.$row[($this->round["4"][$i])."_HPLACE"].'</center></td>';
                              echo '<td width="4%"><center>'.$row[($this->round["4"][$i])."_PLACE"].'</center></td>';
                              echo '<td width="7%"><center>'.$row[($this->round["4"][$i])."_TD"].'</center></td>';
                           }
                       }
                       echo '<td width="5%"><center>'.$row["EV_SCORE"].'</center></td>';
                       echo '</tr>';
                       $totalscore=$totalscore+$row["EV_SCORE"];
                 }
                 echo '<tr bgcolor="#';
                 if($color){echo 'EDEDED'; $color=false;}
                 else{echo 'C3C3C3'; $color=true;}
                 echo '"><td colspan="17"><b>Total Score</b></td><td><center><b>'.$totalscore.'</b></center></td></tr>';
                 echo '</table>';
                 }else{

                  $results=mysql_query("SELECT team.*,athlete.* FROM team,athlete WHERE athlete.ath_no='$an'");
                  if($row=mysql_fetch_array($results)){
                    echo '<table width="640" border=0><tr><td>';
                    echo '<table width="500" border=0>';
                    echo '<tr><td bgcolor="#21269A" colspan="2"><font color="white">Athlete Info</font></td></tr>';
                    echo '<tr><td bgcolor="#C3C3C3" width="100">First Name</td><td bgcolor="#EDEDED" width="400">'.$row["FIRST_NAME"].'</td></tr>';
                    echo '<tr><td bgcolor="#C3C3C3" width="100">Last Name</td><td bgcolor="#EDEDED" width="400">'.$row["LAST_NAME"].'</td></tr>';
                    echo '<tr><td bgcolor="#C3C3C3" width="100">Team</td><td bgcolor="#EDEDED" width="400"><a href="test.php?action=team_data&database='.$database.'&team_no='.$row["TEAM_NO"].'">'.$row["TEAM_NAME"].'</a></td></tr>';
                    echo '<tr><td bgcolor="#C3C3C3" width="100">Gender</td><td bgcolor="#EDEDED" width="400">'.$this->gender[$row["SEX"]].'</td></tr></table>';
                    echo '</td><td width="250"><b>H</b> = Heat<br><b>HP</b> = Heat Place<br><b>RP</b> = Round Place<br><b>R</b> = Results<br><b>SCR</b> = Score/Points</td></tr></table>';
                   }
                   else{
                       echo '<h1>No Data for athlete '.$an.'</h1>';
                   }
                 }
        }
        function page_list($table, $database, $inbound="0"){
                 $page=0;
                 $end=false;
                 $results = mysql_query("SELECT * FROM $table");
                 $max_records = mysql_num_rows($results);
                 for($i=(-80);$i<=80;$i=($i+20)){
                     $page=((($inbound+$i)/20)+1);
                     if($i==-80 && $page > 1){echo '...';}
                     if($i==0){echo ' | '.$page;}
                     elseif($page > 0 && $inbound+$i < $max_records){
                          echo ' | <a href="test.php?action='.$table.'_list&inbound='.($inbound+($i)).'&database='.$database.'">'.$page.'</a>';
                     }
                     if(($inbound+$i) > $max_records){
                           $end=true;
                     }
                     if($inbound+$i < $max_records && $i==80){
                           echo ' | ...';
                     }
                 }
                 if($end){
                    echo ' |';
                 }
        }
        function search($query,$option, $database){
                 if($option=="ath_name"){
                    trim($query);
                     $data=array();
                      $crap_data=explode(" ", $query);
                      while(list($key, $val) = each($crap_data)){
                        if($val == NULL){
                                next;
                        }else{
                                array_push($data, $val);
                        }
                      }
                    $results=mysql_query("SELECT LAST_NAME,FIRST_NAME,ATH_NO FROM athlete WHERE FIRST_NAME like '%$data[0]%' AND LAST_NAME like '%$data[1]%'") or die (print mysql_error());
                    if(mysql_num_rows($results) > 20){
                       echo '<h1>Please be more specific.</h1>';
                    }else{
                     echo '<center><h2>Search Results</h2>';
                     echo '<table width="500" border=0>';
                     while($row=mysql_fetch_array($results)){
                       echo '<tr bgcolor="#';
                       if($color){echo 'EDEDED'; $color=false;}
                       else{echo 'C3C3C3'; $color=true;}
                       echo '"><td><a href="test.php?database='.$database.'&action=athlete_data&ath_no='.$row["ATH_NO"].'">'.$row["FIRST_NAME"]." ".$row["LAST_NAME"].'</a></td></tr>';
                     }
                     echo '</table></center>';
                    }
                 }
                 if($option=="ath_no"){
                    $this->athlete_data($query,$database);
                 }
                 if($option=="event_name"){

                 }
        }
}
?>
