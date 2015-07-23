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
 *
 */


/*
 * 
 * libAbCli is a small class for writing cli scripts in php
 * version: 0.4
 */

class cli{
    
    private static $instance; // singleton instance
    
    // control Variables
    private $isCli = false;
    private $isRoot;
    private $interactive = false;


    // String Variables
    private $newLine = "\n";
    private $newTab = "\t";
    private $newReturn = "\r";
    private $exitKeys = ["q","^C"];
    private $strings = [
        "pressAnyKey"=>"Press Any Key",
        "writeConfirm"=>"please write @@@@ to confirm.", // @@@@ 
        "yes"=>"yes",
        "no"=>"no",
    ];
    private $animation = "\|/-\|/-";
    private $animationPos = 0;



    /*
     * Singleton implementation
     */
    private  function __construct($in = true){
        // check if is CLI 
        if(php_sapi_name() == "cli"){
            $this->isCli =TRUE;
        }else{
          $this->isCli =FALSE;
        }
        
        // asing the interactive atribute
        // interactive is when the user cals the script
        // non interactive is for automatization, for example a cron job
        if($in){
            $this->interactive = true;
        }
        
    }

    public static function getInstance($in = true){
        if (  !self::$instance instanceof self){
            self::$instance = new self($in);
        }
        return self::$instance;
    }
    // copy of getInstance
    public static function init($in = true){
        return self::getInstance($in);
    }
    
    
    
    
    
    public function isInteractive(){
        return $this->interactive;
    }
    // alias for isInteractive()
    public function isIn(){
        return $this->isInteractive();
    }

    public function enableInteractive(){
        return  $this->interactive = TRUE;
    }
    // alias for enable interactive
    public function enIn(){
        return $this->enableInteractive();
    }

    public function disableInteractive(){
        return $this->interactive = FALSE;
    }
    
    //alias for disableInteractive
    public function disIn(){
        return $this->disableInteractive();
    }


    /*
     * 
     * Check if is run by cli
     * 
     */
    public function isCli($exit = true){
        if($this->isCli != TRUE && $exit == TRUE ){
            exit;
        }
        return $this->isCli;
    }
    



    /*
     * 
     * Return The given args
     * 
     */
    public function getArgs(){
        $argsCount = count($_SERVER["argv"]);
        $args = $_SERVER["argv"];
        array_shift($args);

        $return = array("script"=>$_SERVER["argv"][0]);
        $options = [];
        $atributes = [];

        if($argsCount > 1){
            $args  = array_shift($args);

            $parNum = 0;
            for ($i=1; $i < $argsCount; $i++) {
                $val = $i-1;
                if(substr($_SERVER["argv"][$i], 0,2) == "--" ){
                    // if ARG starts with --
                    $parameter = substr($_SERVER["argv"][$i],2);
                    if(($i+1) != $argsCount){
                        if(substr($_SERVER["argv"][($i+1)], 0,1) == "-" ){
                            $options[$parameter]= TRUE;
                            //$this->{$parameter} = 1;
                        }else{
                          $i++;
                          $options[$parameter]= $_SERVER["argv"][$i];	
                          //$this->{$parameter} = $_SERVER["argv"][$i];						
                        }						
                    }else{
                        $options[$parameter]= TRUE;
                        //$this->{$parameter} = 1;	
                    }

                }elseif(substr($_SERVER["argv"][$i], 0,1) == "-"){
                    // if ARG starts with --
                    $parameter = substr($_SERVER["argv"][$i],1);
                    if(($i+1) != $argsCount){
                        if(substr($_SERVER["argv"][($i+1)], 0,1) == "-" ){
                            $options[$parameter]= TRUE;
                            //$this->{$parameter} = 1;
                        }else{
                          $i++;
                          $options[$parameter]= $_SERVER["argv"][$i];	
                          //$this->{$parameter} = $_SERVER["argv"][$i];						
                        }						
                    }else{
                        $options[$parameter]= TRUE;
                        //$this->{$parameter} = 1;	
                    }              
                }else{
                    $atributes[$parNum]= $_SERVER["argv"][$i];
                    //$this->{$parNum} = $_SERVER["argv"][$i];
                    $parNum++;
                }
            }
        }
        $return["options"] = $options;
        $return["atributes"]= $atributes;
        return $return;
    }

    /*
     * 
     * Check if you are root on unix
     */
    public function isRoot(){
        if (!isset($this->isRoot)){
            if (posix_getuid() == 0){
                $this->isRoot = TRUE;
            } else {
                $this->isRoot = FALSE;
            }
        }
        return $this->isRoot;
    }
    
    public function isSuperCow(){
        if($this->isRoot){
            return true;
        }else{
            $this->pline("You don't have SuperCow Powers!!!");
            exit;
        }
    }

    /*
     * 
     *  print a mesage
     */
    public function pline($string=false){
        if (!$this->isIn() || $string == false)  return;
        echo $string;
        $this->pNewLine();            
    }
    
    
    /*
     * Print any Char N times
     */
    public function pnChar($char=false,$times=1){
        if(!$char) return false;
        if (!$this->isIn())  return;
	for ($i=0; $i < $times; $i++) {
            echo $char;
        }
    }

    /*
     * Print a new line
     */
    public function pNewLine($times = 1){
        $this->pnChar($this->newLine,$times);
    }

    /*
     * PRint a Tab character
     */
    public function pNewTab($times = 1){
        $this->pnChar($this->newLine,$times);
    }
    
    /*
     *  Clear the Screen
     */
    public function clear(){
        if (!$this->isIn())  return;        
        system("clear");

    }	
	
    
    public function quit(){
	  exit;
    }
	

//#############################################################################################
//      Here we start with interactive functions
//#############################################################################################

    public function anyKeyMessage($msg=false){
        if (!$this->isIn())  return;
        $this->pline($msg);
        $this->pline($this->strings["pressAnyKey"]);
            
        $handle = fopen ("php://stdin","r");
        $return =  strtolower(trim(fgets($handle)));				

        if(in_array($return, $this->exitKeys)){
            exit;
        }			
    }

    // double confirmation yes/no question
    public function getYesNoInput($msg="",$default="n",$confirmation=false,$msg2= null,$confirmWord=null){
        if (!$this->isIn())  return;     
        if($msg2 === null) $msg2 = $this->strings["writeConfirm"];
        if($confirmWord === null ) $confirmWord = $this->strings["yes"];
        
        $msg2 = str_replace ( "@@@@" , "\"$confirmWord\"" , $msg2 );

        $return = "";

        if($default == "y"){
          $values = "Y/n";
        }else{
          $values = "y/N";
        }

        while (!in_array($return, array("y","n"))) {
                cli::pLine($msg."( ".$values." )");
                $handle = fopen ("php://stdin","r");
                $return =  strtolower(trim(fgets($handle)));

                if(in_array($return, $this->exitKeys)){
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
                      cli::pLine($msg2." $value2");
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
    }


    /*
     * Get a input from keyboad
     *  $allowed : array of values | string[ numeric | noempty ]
     */
    public function getInput($msg="",$allowed=false,$default=false){
        if (!$this->isIn())  return;   

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
                cli::pLine($msg."( ".$values." )");
                $handle = fopen ("php://stdin","r");
                $return =  strtolower(trim(fgets($handle)));

                if(in_array($return, $this->exitKeys)){
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
                cli::pLine($msg." ".$defaultstr);

                $handle = fopen ("php://stdin","r");
                $return =  strtolower(trim(fgets($handle)));				

                if(in_array($return, $this->exitKeys)){
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
                cli::pLine($msg." ".$defaultstr);

                $handle = fopen ("php://stdin","r");
                $return =  strtolower(trim(fgets($handle)));				

                if(in_array($return, $this->exitKeys)){
                        exit;
                }
                if($default != false && $return ==""){
                        $return = $default;
                        break;
                }
            }
        }else{
            cli::pLine($msg." ".$default);
            $handle = fopen ("php://stdin","r");
            $return =  strtolower(trim(fgets($handle)));

            if($default != false && $return ==""){
                    $return = $default;
            }		
        }

        if(in_array($return, $this->exitKeys)){
                exit;
        }		

        return $return;
    }
    
    
    /*
     * prints a list of options to select
     */
    public function getSelect($msg="",$selecctions=false,$printQuint = true){
        if (!$this->isIn())  return;   		
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
        return $this->getInput($msg="$msg \n".$elementSting ,"numeric", false);		
    }		

//	static function sys($cm = false){
//		if($cm!=false){
//			if(defined("DEMO") && DEMO == true){
//				cli::out("command: '".$cm."'");
//			}else{
//				system($cm);
//			}
//		}
//	}

    
    

    /*
     * 
     * Wil remove extra / from path;
     * for example /home//user will get /home/user
     */
    static function sanitizePath($path){
            $path = trim($path);
            $path = preg_replace('/\/+/', '/',$path);
            return $path;
    }    
    /*
     * Animate CLI 
     */

    
    public function animate(){
        if (!$this->isIn())  return;           
        $animLength = strlen($this->animation);
        if($this->animationPos == $animLength ){
            $this->animationPos=0;
        }
        echo $this->animation[$this->animationPos]."\r";
        $this->animationPos++;
        
    }
    


	

	
}// end of cli
