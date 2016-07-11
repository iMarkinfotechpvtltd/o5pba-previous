<?


$menu = new menu;

$menu->add('Admin','Home','/admin/index.php');
$menu->add('Admin','News','/admin/news.php');
#if ($perm->have_perm('news')){
#	$menu->add('Admin','News','/admin/news.php');
#}else{
#	$menu->add('Admin','News','');
#}
if ($perm->have_perm('forms')){
	$menu->add('Admin','Forms','/admin/forms.php');
}else{
	$menu->add('Admin','Forms','');
}
if ($perm->have_perm('tournament')){
	$menu->add('Admin','Tournaments','/admin/tournament.php');
}else{
	$menu->add('Admin','Tournaments','');
}
if ($perm->have_perm('bowling_school')){
	$menu->add('Admin','Bowling School','/admin/bowling_school.php');
}else{
	$menu->add('Admin','Bowling School','');
}

if ($perm->have_perm('other')){
	$menu->add('Admin','Other Events','/admin/otherevents.php');
	//$menu->add('Admin','Other Events','');
}else{
	$menu->add('Admin','Other Events','');
}

if ($perm->have_perm('coach')){
	$menu->add('Admin','Coach\'s Corner','/admin/coach.php');
}else{
	$menu->add('Admin','Coach\'s Corner','');
}

if ($perm->have_perm('khp')){
	$menu->add('Admin','KHP Donations','/admin/khp.php');
}else{
	$menu->add('Admin','KHP Donations','');
}
if ($perm->have_perm('perfect')){
	$menu->add('Admin','Perfect Games','/admin/index.php?function=perfect');
}else{
	$menu->add('Admin','Perfect Games','');
}
if ($perm->have_perm('halloffame')){
	$menu->add('Admin','Hall of Fame','/admin/hof.php');
}else{
	$menu->add('Admin','Hall of Fame','');
}
if ($perm->have_perm('calendar')){
	$menu->add('Admin','Upcoming Events','/admin/admincal.php');
}else{
	$menu->add('Admin','Upcoming Events','');
}
if ($perm->have_perm('newsletter')){
	$menu->add('Admin','Newsletter Admin','/admin/newsletter.php');
}else{
	$menu->add('Admin','Newsletter Admin','');
}

if ($perm->have_perm('links')){
	$menu->add('Admin','Link Admin','/admin/linksects.php');
}else{
	$menu->add('Admin','Link Admin','');
}

if ($perm->have_perm('bod')){
	$menu->add('Admin','Edit BOD Page','/admin/bod.php?class=bod');
}else{
	$menu->add('Admin','Edit BOD Page','');
}

if ($perm->have_perm('bod')){
	$menu->add('Admin','Edit Office Staff','/admin/bod.php?class=officestaff');
}else{
	$menu->add('Admin','Edit Office Staff','');
}

if ($perm->have_perm('banner')){
	$menu->add('Admin','Front Page Banner','/admin/banner.php');
}else{
	$menu->add('Admin','Front Page Banner','');
}
if ($perm->have_perm('logs')){
	$menu->add('Admin','View Logs','/admin/logs.php');
}else{
	$menu->add('Admin','View Logs','');
}
if ($perm->have_perm('averagebook')){
	$menu->add('Admin','Average Book','/admin/averagebook.php');
}else{
	$menu->add('Admin','Average Book','');
}
if ($perm->have_perm('users')){
	$menu->add('Admin','Users','/admin/usermanager.php');
}else{
	$menu->add('Admin','Users');
}


$menu->add('Admin','Force Update','/admin/update.php');
$menu->add('Admin','Change Password','/admin/usermanager.php?function=changepassword');
$menu->add('Admin','Logout','/admin/index.php?function=logout');

?>
