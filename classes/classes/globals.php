<?php
		$months = array(array("value"=>"","label"=>"Month"),
		array("value"=>"1","label"=>"Jan"),
		array("value"=>"2","label"=>"Feb"),
		array("value"=>"3","label"=>"Mar"),
		array("value"=>"4","label"=>"Apr"),
		array("value"=>"5","label"=>"May"),
		array("value"=>"6","label"=>"Jun"),
		array("value"=>"7","label"=>"Jul"),
		array("value"=>"8","label"=>"Aug"),
		array("value"=>"9","label"=>"Sep"),
		array("value"=>"10","label"=>"Oct"),
		array("value"=>"11","label"=>"Nov"),
		array("value"=>"12","label"=>"Dec"));
		
$days=array(array("value"=>"","label"=>"Day"),
array("label"=>"1","value"=>"1"),
array("label"=>"2","value"=>"2"),
array("label"=>"3","value"=>"3"),
array("label"=>"4","value"=>"4"),
array("label"=>"5","value"=>"5"),
array("label"=>"6","value"=>"6"),
array("label"=>"7","value"=>"7"),
array("label"=>"8","value"=>"8"),
array("label"=>"9","value"=>"9"),
array("label"=>"10","value"=>"10"),
array("label"=>"11","value"=>"11"),
array("label"=>"12","value"=>"12"),
array("label"=>"13","value"=>"13"),
array("label"=>"14","value"=>"14"),
array("label"=>"15","value"=>"15"),
array("label"=>"16","value"=>"16"),
array("label"=>"17","value"=>"17"),
array("label"=>"18","value"=>"18"),
array("label"=>"19","value"=>"19"),
array("label"=>"20","value"=>"20"),
array("label"=>"21","value"=>"21"),
array("label"=>"22","value"=>"22"),
array("label"=>"23","value"=>"23"),
array("label"=>"24","value"=>"24"),
array("label"=>"25","value"=>"25"),
array("label"=>"26","value"=>"26"),
array("label"=>"27","value"=>"27"),
array("label"=>"28","value"=>"28"),
array("label"=>"29","value"=>"29"),
array("label"=>"30","value"=>"30"),
array("label"=>"31","value"=>"31"));

$hidhdyear=date('Y');

$years=array(array("value"=>"","label"=>"Year"));

for ( $i = ($hidhdyear-2); $i <=($hidhdyear+2); $i++ ) {

array_push($years,array("label"=>$i,"value"=>$i));
}

$hours=array(array("value"=>"","label"=>"Hour"),
array("label"=>"01","value"=>"01"),
array("label"=>"02","value"=>"02"),
array("label"=>"03","value"=>"03"),
array("label"=>"04","value"=>"04"),
array("label"=>"05","value"=>"05"),
array("label"=>"06","value"=>"06"),
array("label"=>"07","value"=>"07"),
array("label"=>"08","value"=>"08"),
array("label"=>"09","value"=>"09"),
array("label"=>"10","value"=>"10"),
array("label"=>"11","value"=>"11"),
array("label"=>"12","value"=>"12"),
array("label"=>"13","value"=>"13"),
array("label"=>"14","value"=>"14"),
array("label"=>"15","value"=>"15"),
array("label"=>"16","value"=>"16"),
array("label"=>"17","value"=>"17"),
array("label"=>"18","value"=>"18"),
array("label"=>"19","value"=>"19"),
array("label"=>"20","value"=>"20"),
array("label"=>"21","value"=>"21"),
array("label"=>"22","value"=>"22"),
array("label"=>"23","value"=>"23"),
array("label"=>"24","value"=>"24"));

$mins=array(array("value"=>"","label"=>"Min"),
array("value"=>"00","label"=>"00"),
array("value"=>"05","label"=>"05"),
array("value"=>"10","label"=>"10"),
array("value"=>"15","label"=>"15"),
array("value"=>"20","label"=>"20"),
array("value"=>"25","label"=>"25"),
array("value"=>"30","label"=>"30"),
array("value"=>"35","label"=>"35"),
array("value"=>"40","label"=>"40"),
array("value"=>"45","label"=>"45"),
array("value"=>"50","label"=>"50"),
array("value"=>"55","label"=>"55"));

?>