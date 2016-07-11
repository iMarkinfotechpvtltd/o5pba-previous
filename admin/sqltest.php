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
} else if ($_POST['action']=='dump'){
   $db=new db;

   if (false) {      
      $tableNames = $db->table_names();
      print("Table Names\n");
      print("<table>\n");
      foreach ($tableNames as $name) print('<tr><td>'.$name['table_name'].'</td><td>'.$name['tablespace_name']."</td></tr>\n");
      print("</table>\n");
   }

   $query = 'CREATE TEMPORARY TABLE LastYear(memberid VARCHAR(30),firstYear int,lastYear int) TYPE=HEAP';
   print('<pre>'.$query.'</pre>');
   $db->query($query);

   $query = 'INSERT INTO LastYear SELECT memberid, MIN(year), MAX(year) FROM average_history GROUP BY memberid';
   print('<pre>'.$query.'</pre>');
   $db->query($query);

   $db->query('SELECT * FROM LastYear LIMIT 250');
   print("<table>\n<tr>");
   $cols = array('memberid','firstYear','lastYear');
   foreach ($cols as $col) print('<th>'.$col.'</th>');
   print("</tr>\n");
   while ($db->next_record()) {
      print('<tr>');
      foreach ($cols as $col) print('<td>'.$db->f($col).'</td>');
      print("</tr>\n");
   }
   print("</table>\n");

   $query = 'SELECT * FROM average_history, LastYear WHERE average_history.memberid = LastYear.memberid AND average_history.year = LastYear.lastYear LIMIT 250';

if (false) {
   $query = 'SELECT * FROM average_history GROUP BY memberid ORDER BY memberid, year DESC LIMIT 250';

   $query = 'SELECT * FROM average_history AS A INNER JOIN ('.
            '   SELECT memberid, MAX(year) MaxYear FROM average_history'.
            '   GROUP BY memberid)'.
            'AS B ON A.memberid = B.memberid LIMIT 250';

   $query = 'SELECT *, MAX(year) AS MaxYear, MIN(year) AS MinYear FROM average_history GROUP BY memberid LIMIT 250';

//   $query = 'SELECT * FROM average_history AS A LEFT JOIN average_history AS B ON A.memberid = B.memberid LIMIT 250';

//   $query = 'SELECT first, last, memberid, rank() OVER (PARTITION BY memberid ORDER BY year DESC) "RANK" LIMIT 250';
}



   print('<pre>'.$query.'</pre>');
   $db->query($query);
   $avgcols = array('first','last','memberid','average','games','league','association','oplacing','aplacing','year', 'firstYear', 'lastYear');
   print("<table>\n<tr>");
   foreach ($avgcols as $col) print('<th>'.$col.'</th>');
   print("</tr>\n");
   while ($db->next_record()) {
      print('<tr>');
      foreach ($avgcols as $col) print('<td>'.$db->f($col).'</td>');
      print("</tr>\n");
   }
   print("</table>\n");

   $query = 'DROP TABLE LastYear';
   print('<pre>'.$query.'</pre>');
   $db->query($query);

} else if ($_POST['action']=='clear'){
} else if ($_POST['action']=='submit'){
}
      ?>
      <form name='archive'  enctype='multipart/form-data' method='POST' action='/admin/sqltest.php?' target=''>
         <table class="listing">
            <tr>
               <td style="text-align: left"><input type='hidden' name='action' value='dump'><input name='Submit' value='Query' type='submit'></td>
            </tr>
         </table>
      </form>

<?
@ page_close();
include_once($htdocsdir.'includes/bottom.php');
?>
