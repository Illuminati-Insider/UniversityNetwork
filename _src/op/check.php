<?php
	
	function check_int($var,$lb,$rb) {
		if ($lb<$rb){
			if ((isset($var) && isset($rb) && isset($lb))){
				if ($var<$lb || $var>$rb) {
					throw403("Invalid range of integer"); 
				} else {
						return $var;
				};
				
			}else {
					throw403("No Data");
			}
			
		}else {throw403("leftborder>rightborder");}
	}
	
	function check_str_eng($var) {
		if(isset($var)){
			if (preg_match("/[^a-zA-Z0-9_]/", $var)) {
				throw403("False");
			} else {
					return $var;
			};
		}
	}
	
	function check_str($var) {
		if(isset($var))
		{
			if (preg_match("/[^(\w)|(\x7F-\xFF)|(\s)]/", $var)) {
				throw403("False"); 
			} else 	{
					return $var;
			};
		}
		
	}	// запрещает все, кроме пробела,_, букв русского и латинского алфавита, цифр
    function check_str_with_pattern($var,$pattern)
	{
		if((isset($var) && isset($pattern)))
		
		{
			if (preg_match("$pattern", $var)) {
				throw403("False"); 
			} else 	{
					return $var;
			};
		}
	}
	
	function check_str_len($var,$minlen,$maxlen){
		
		
		if ((isset($var) && isset($minlen) && isset($maxlen)))
		
		{
			if ($minlen<=$maxlen){
				if (strlen($var)<$minlen || strlen($var)>$maxlen) {
					throw403("Invalid range of strlen");
				} else 	{
					return $var;
				};
			} else {
				throw403('Error');
			}	
		}
		
		
	}
	
?>