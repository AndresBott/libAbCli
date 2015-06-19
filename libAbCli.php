<?php
/*
 *
 * LibAbCli
 * Copyright (c) Andrés Bott (contact@andresbott.com) , All rights reserved.
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 3.0 of the License, or (at your option) any later version.
 * 
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library.
 * 
 */


/*
 * 
 * libAbCli is a small class for writing cli scripts in php
 * versio: 0.3
 */

class cli{

    /*
     * 
     * Return The given args
     * 
     */
    static function getArgs(){

      $argsCount = count($_SERVER["argv"]);
      $args = $_SERVER["argv"];
      array_shift($args);

      $return = array("script"=>$_SERVER["argv"][0]);

      if($argsCount > 1){
        $args  = array_shift($args);

        $parNum = 0;
        for ($i=1; $i < $argsCount; $i++) {
          $val = $i-1;
          if(substr($_SERVER["argv"][$i], 0,1) == "-"){

            $parameter = substr($_SERVER["argv"][$i],1);
            if(($i+1) != $argsCount){
              if(substr($_SERVER["argv"][($i+1)], 0,1) == "-" ){
                  $return[$parameter]= 1;
                  //$this->{$parameter} = 1;
              }else{
                $i++;
                $return[$parameter]= $_SERVER["argv"][$i];	
                //$this->{$parameter} = $_SERVER["argv"][$i];						
              }						
            }else{
              $return[$parameter]= 1;
              //$this->{$parameter} = 1;	
            }

          }else{
            $return[$parNum]= $_SERVER["argv"][$i];
            //$this->{$parNum} = $_SERVER["argv"][$i];
            $parNum++;
          }
        }
      }
      return $return;
    }

    /*
     * 
     * Check if is run by cli
     * 
     */
    static function is_cli(){
      if(php_sapi_name() == "cli"){
        return true;
      }else{
        return false;
      }
    }
	
	
	static function safe_cli(){
	  if(!cli::is_cli()){
	       exit;
	  }
	}
	
	
	static function hasRootPower(){
	    // we asume only root can write in /root
	    if(is_writable("/root")){
	      return true;
	    }else{
	      return false;
	    }
	}
	
	static function anyKeyMessage($msg){
		if(cli::is_interactive()){
			cli::out($msg);
			cli::out("pres any key to continue");
			$handle = fopen ("php://stdin","r");
			$return =  strtolower(trim(fgets($handle)));				
			
			if($return == "q" || $return == "^C"){
				exit;
			}			
		}	
	
	}

	// double confirmation yes/no question
	static function getYesNoInput($msg="",$default="n",$confirmation=false,$msg2="please write @@@@ to confirm.",$confirmWord="yes"){
		if(!cli::is_interactive()){
			return $default;
		}


		$msg2 = str_replace ( "@@@@" , "\"$confirmWord\"" , $msg2 );


		$return = "";

		if($default == "y"){
		  $values = "Y/n";
		}else{
		  $values = "y/N";
		}

		while (!in_array($return, array("y","n"))) {
			cli::out($msg."( ".$values." )");
			$handle = fopen ("php://stdin","r");
			$return =  strtolower(trim(fgets($handle)));
			
			if($return == "q" || $return == "^C"){
				exit;
			}
				
			if($default != false && $return ==""){
				$return = $default;
				break;
			}
		}

		if($confirmation == true && $return == "y"){

		      $default2 = "n";
		      $value2 = "(N/no/$confirmWord)";
		      $return2 = "";
		      while (!in_array($return2, array($confirmWord,"n","no"))) {
			      cli::out($msg2." $value2");
			      $handle = fopen ("php://stdin","r");
			      $return2 =  strtolower(trim(fgets($handle)));
		      }

		      if($return2 == $confirmWord){
			  return "y";
		      }else{
			  return "n";
		      }

		}else{
		    return $return;
		}







	    $valid = false;

	    $sure = cli::getInput("Are you sure you want to perform this action? (write \"yes\" )",array("no","n","yes") );

// 	    while($valid != true){
// 
// 		if(in_array($sure, array("yes","n","no") ) ){
// 		    $valid = true;
// 		    if($sure == "yes"){
// 			  return true;
// 		    }else{
// 			  return false;
// 		    }
// 		}else{
// 			
// 			//cli::out("Please write \"yes\" , \"no\" or \"n\"");
// 		}
// $sure = cli::getInput("Please write \"yes\" , \"no\" or \"n\"",array("no","n","yes") );
// 	    }

	}




	# $allowed => numeric / noempty
	static function getInput($msg="",$allowed=false,$default=false){
		if(!cli::is_interactive()){
			return $default;
		}
		
		$return = "";

		if(is_array($allowed)){
			
			$values = "";
			if($default!= false){
				$values .= strtoupper($default);
				foreach ($allowed as $val) {
					if($val != $default){
						$values .= "/".$val;
					}
				}
			}else{
				$values = implode("/",  $allowed);
			}
					
			while (!in_array($return, $allowed)) {
				cli::out($msg."( ".$values." )");
				$handle = fopen ("php://stdin","r");
				$return =  strtolower(trim(fgets($handle)));
				
				if($return == "q" || $return == "^C"){
					exit;
				}
					
				if($default != false && $return ==""){
					$return = $default;
					break;
				}
			}			
		}elseif($allowed == "numeric"){
			if($default!= false){
				$defaultstr = "(defautl:".$default.")";
			}else{
				$defaultstr = "";
			}
			while (!is_numeric($return)) {
				cli::out($msg." ".$defaultstr);
				
				$handle = fopen ("php://stdin","r");
				$return =  strtolower(trim(fgets($handle)));				
				
				if($return == "q" || $return == "^C"){
					exit;
				}
				if($default != false && $return ==""){
					$return = $default;
					break;
				}
			}
			
		}elseif($allowed == "noempty"){
			if($default!= false){
				$defaultstr = "(defautl:".$default.")";
			}else{
				$defaultstr = "";
			}
			while (empty($return)) {
				cli::out($msg." ".$defaultstr);
				
				$handle = fopen ("php://stdin","r");
				$return =  strtolower(trim(fgets($handle)));				
				
				if($return == "q" || $return == "^C"){
					exit;
				}
				if($default != false && $return ==""){
					$return = $default;
					break;
				}
			}
		}else{
			cli::out($msg." ".$default);
			$handle = fopen ("php://stdin","r");
			$return =  strtolower(trim(fgets($handle)));
			
			if($default != false && $return ==""){
				$return = $default;
			}		
		}
		
		
		
		
		if($return == "q" || $return == "^C"){
			exit;
		}		
		return $return;
	}

	static function getSelectInput($msg="",$selecctions=false,$printQuint = true){
		
		if(!is_array($selecctions)){
			return false;
		}
		
		$elementSting = "";
		$count = 1;
		foreach ($selecctions as $element) {
			$var[$count] = $element;
			$elementSting .= "  $count - ".$element."\n";
			$count ++;
		}
		if($printQuint == true){
		    $elementSting .= "  q - Quit! \n";
		}
		return cli::getInput($msg="$msg \n".$elementSting ,"numeric", false);
		
	}		

	static function sys($cm = false){
		if($cm!=false){
			if(defined("DEMO") && DEMO == true){
				cli::out("command: '".$cm."'");
			}else{
				system($cm);
			}
		}
	}
	static function is_interactive(){
	  if(defined("INTERACTIVE") && INTERACTIVE == true){
		return true;
 	  }else{
 	  	return false;
 	  }
		
	}
	static function out($string){

		if(cli::is_interactive()){
			echo $string;
			cli::n();
		}
	}
	
	static function info($string){
		if(cli::is_interactive()){
			cli::n();
			echo "======== INFO ========\n";
			echo $string;
			cli::n(2);
		}	
	}

	static function printForms($char=false,$times=1){
	  if($char == false){
	    return;
	  }
	          
	  if(!cli::is_interactive()){
		return;
 	  }
					  
	  for ($i=0; $i < $times; $i++) { 
	    switch ($char) {
	      case 'n':
	    echo "\n";	
	    break;
	  case 't':
	    echo "\t";	
	        break;
	      default:
	      
	        break;
	    }		
	  }
	}
	
	static function n($times=1){
	  cli::printForms("n",$times);
	}
	 
	static function t($times=1){
	  cli::printForms("t",$times);
	}
	static function c(){
		if(cli::is_interactive()){
			system("clear");
		}
	}	
	
	static function quit(){
	  exit;
	}
	
	
}// end of cli
