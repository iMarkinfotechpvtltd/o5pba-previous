<?
set_time_limit(0);
require_once($_SERVER["DOCUMENT_ROOT"].'/classes/prepend.php');
page_open(array("sess" => "O5Session", "auth" => "O5Auth", "perm"=>"O5Perm"));
include_once('./includes/menuitems.php');
include('./includes/top.php');

$perm->check('averagebook');

/**
 * It true the form will have additional admin buttons to perform non user level operations.
 */
$adminFunctions = false;

/**
 * If true previous average book records will be deleted before importing a csv file.
 */
$deletePreviousBeforeCSVImport = true;

/**
 * These are the expected columns when importing a CSV file.  If they do not match
 * we cannot import since we do not know how to interpret the data.
 */
$expectedColumns = array('First','Last','Membership','Average','Games','League Code','Association','Ont Place','Assoc Place','Year');

$upload_class = new Upload_Files;

if ($_POST['action']=='archive'){
   $db=new db;

   /* This would be nice, but db_Sql doesn't support transactions */
   //$db->begin();
   //$db->query('LOCK TABLE average');

   /* Move the archived records to the history table */
   $db->query(sprintf('INSERT INTO average_history SELECT * FROM average WHERE year < %s',$_POST['beforeYear']));
   print("Archived ".$db->affected_rows()." records\n");

   /* Delete the archived records from the records table */
   $db->query(sprintf('DELETE FROM average WHERE year < %s',$_POST['beforeYear']));
   
   //$db->commit();
   
   logit("Archived all average book records older than ".$_POST['beforeYear']);
} else if ($_POST['action']=='dump'){
   $db=new db;

   if (false) {      
      $tableNames = $db->table_names();
      print("Table Names\n");
      print("<table>\n");
      foreach ($tableNames as $name) print('<tr><td>'.$name['table_name'].'</td><td>'.$name['tablespace_name']."</td></tr>\n");
      print("</table>\n");
   }

   $db->query('SELECT * FROM average');
   $avgcols = array('first','last','memberid','average','games','league','association','oplacing','aplacing','year');
   print("<table>\n<tr>");
   foreach ($avgcols as $col) print('<th>'.$db->f($col).'</th>');
   print("</tr>\n");
   while ($db->next_record()) {
      print('<tr>');
      foreach ($avgcols as $col) print('<td>'.$db->f($col).'</td>');
      print("</tr>\n");
   }
   print("</table>\n");
} else if ($_POST['action']=='clear'){
   $db=new db;
   
   $db->query('TRUNCATE average');
//   $db->query(sprintf('DELETE FROM average WHERE year = %s',$_POST['deleteYear']));
   
//   logit(sprintf('Deleted all records for year '.$_POST['deleteYear'].' from average book.'));
   print("<p>Deleteal all records from average book.</p>\n");
   logit(sprintf('Deleted all records from average book.'));
} else if ($_POST['action']=='submit'){
	$upload_class->temp_file_name = trim($_FILES['upload']['tmp_name']);
	$upload_class->file_name = 'average.csv';//$_FILES['upload']['name'];//'average.sql';

	$upload_class->upload_dir = $htdocsdir;
	$upload_class->upload_log_dir = $htdocsdir."hallfame/pictures/";
	$upload_class->max_file_size = 30960000;
	$upload_class->banned_array = array("");
	$upload_class->ext_array = array(".sql",".csv");

	$valid_ext = $upload_class->validate_extension();
	$valid_size = $upload_class->validate_size();
	$valid_user = $upload_class->validate_user();
	$max_size = $upload_class->get_max_size();
	$file_size = $upload_class->get_file_size();


	if (!$valid_ext) {
		$result = "The file extension is invalid, please try again!";
	}elseif (!$valid_size) {
		$result = "The file size is invalid, please try again! The maximum file size is: $max_size and your file was: $file_size";
	}elseif (!$valid_user) {
		$result = "You have been banned from uploading to this server.";
	} else {
		$upload_file = $upload_class->upload_file_with_validation();

		if (!$upload_file) {
			$result = "Your file could not be uploaded!";
		} else {
			$result = "Your file has been successfully uploaded to the server.";
		}
	}

   $fileExtension = substr(strrchr(strtoupper($_FILES['upload']['name']),'.'),1);
   
	if ($result != "Your file has been successfully uploaded to the server."){
		echo $result;

		$upload_class->averagebook_uploadform();
	}else if ($fileExtension == 'CSV') {
		echo $result.'<br>CSV File<br>';
      
      ini_set('auto_detect_line_endings',true);

      $db=new db;
      $db->Halt_On_Error = "report";

      if ($fp = fopen($htdocsdir.'average.csv','r')) {
         if ($deletePreviousBeforeCSVImport) {
            $db->query('TRUNCATE average');
         }
         
         $currentLineNumber = 0;
         
         while (!feof($fp)) {
            $currentLineNumber++;
            $currentLine = fgets($fp,8192);

            $row = split(",",rtrim($currentLine));
            if (!$haveOneRow) {
               $haveOneRow = true;

               if (count($row) < count($expectedColumns)) {
                  $mismatch = true;
               } else {
                  for ($i = 0;$i < count($expectedColumns);$i++) {
                     if ($row[$i] != $expectedColumns[$i]) {
                        $mismatch = true;
                        break;
                     }
                  }
               }
               
               if ($mismatch) {
                  echo "Your CSV file does not have a header, or it does not match the expected header:<br/><table><tr>\n";
                  foreach ($expectedColumns as $col) {
                     echo "<td>$col</td>";
                  }
                  echo '</tr></table>';
                  break;
               }
               continue;                  
            }

            if (count($row) >= count($expectedColumns)) {
               if (!$deletePreviousBeforeCSVImport) {
                  /* We are merging the imported data, so find out if this row exists already */
                  if ($db->query(sprintf('SELECT COUNT(*) FROM average WHERE  memberid = "%s" AND year = %s AND league = "%s"',$row[2],$row[9],$row[5])) === false) {
                     print("Error reading line $currentLineNumber: '$currentLine'\n");
                     $errors++;
                     continue;
                  }
                  
                  $db->next_record();
               }
               
               if (!$deletePreviousBeforeCSVImport && $db->f(0)) {
                  /* The row exists in the database, so update it */
                  $query = sprintf('UPDATE average SET first = "%s",last = "%s",average = "%s",games = "%s",association = "%s",oplacing = "%s",aplacing = "%s" WHERE memberid = "%s" AND year = %s AND league = "%s"',$row[0],$row[1],$row[3],$row[4],$row[6],$row[7],$row[8],$row[2],$row[9],$row[5]);
               } else {
                  /* The row does not exist in the database, so insert it */
                  $query = sprintf('INSERT INTO average (first,last,memberid,average,games,league,association,oplacing,aplacing,year) VALUES ("%s","%s","%s","%s","%s","%s","%s","%s","%s","%s")',$row[0],$row[1],$row[2],$row[3],$row[4],$row[5],$row[6],$row[7],$row[8],$row[9]);
               }

               if ($db->query($query) === false) {
                  print("Error reading line $currentLineNumber: '$currentLine'\n");
                  $errors++;
                  continue;
               }
            } /* Make sure that the row was not empty (if count()) */
         }

         if ($deletePreviousBeforeCSVImport) {
            logit("Updated Average Book with CSV file, replacing previous records.  $currentLineNumber rows read with $errors errors");
         } else {
            logit("Updated Average Book with CSV file, merging with previous records.  $currentLineNumber rows read with $errors errors");
         }
         
         $db->Halt_On_Error = "true";
         
         fclose($fp);         
      } else {
         print('Error: Could not parse uploaded file. '.$htdocsdir."average.csv\n");
      } /* Scan the input file for header rows (while !feof()) */
	}else if ($fileExtension == 'SQL') {
		echo $result.'<br>SQL File<br>';

		system('mysql -u o5pba -psecrethello < '.$htdocsdir.'average.csv');
      
      logit("Updated Average Book with SQL file");
	}else {
		echo $result.'<br>Unknown file type.<br>';
	}

}else{
		$upload_class->averagebook_uploadform();
      
      ?>
      <form name='archive'  enctype='multipart/form-data' method='POST' action='/admin/averagebook.php?' target=''>
         <table class="listing">
            <tr>
               <td style="text-align: right">Before Year:</td>
               <td style="text-align: left"><input type='text' name='beforeYear' value='<?php print(date('Y')); ?>'/></td>
               <td style="text-align: left"><input type='hidden' name='action' value='archive'><input name='Submit' value='Archive' type='submit'></td>
            </tr>
            <tr>
               <td></td>
               <td></td>
               <td>
                  <p>
                     Archive Copies all average book entries older than 'Before Year' to the averages_history.  This will prevent them from showing
                     up in a search.  This should only be done once per year at the start of a new season.<br/>
                     Warning: There is no way to undo this action.
                  </p>
               </td>
            </tr>
         </table>
      </form>

      <?php if ($adminFunctions) { ?>
         <form name='delete'  enctype='multipart/form-data' method='POST' action='/admin/averagebook.php?' target=''>
            <table class="listing">
               <tr>
                  <td style="text-align: left"><input type='hidden' name='action' value='clear'><input name='Submit' value='Delete' type='submit'></td>
               </tr>
            </table>
         </form>

         <form name='dump'  enctype='multipart/form-data' method='POST' action='/admin/averagebook.php?' target=''>
            <table class="listing">
               <tr>
                  <td style="text-align: left"><input type='hidden' name='action' value='dump'><input name='Submit' value='Dump' type='submit'></td>
               </tr>
            </table>
         </form>
      <?php
      }
}
@ page_close();
include_once($htdocsdir.'includes/bottom.php');
?>
