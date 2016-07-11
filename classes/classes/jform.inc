<?
class jform extends form{
	
	var $check=false;
	
	function check(){
		$this->check = true;	
	}
	
	function show_element($element){
        form::show_element($element);
        if(($this->check)&&(($err=$this->validate("ok",array($element)))!="ok")){
            echo '<br><font color="red">'.$err.'</font>';
        }
	}
	
	function load_defaults_with_vals($deflist="") {
      	reset($deflist);
      	$elrec = $this->elements[key($deflist)];
	    while ($elrec) {
	      $el = $elrec["ob"];
	      $el->self_load_defaults(current($deflist));
	      $this->elements[$el->name]["ob"] = $el;  // no refs -> must copy back
	      next($deflist);
	      $elrec = $this->elements[key($deflist)];
	    }
  	}
}
?>