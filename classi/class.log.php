<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class logerp {
    
    public $dir;
    
    private $setFilenameError;
    
    public function __construct()
    {
        try {
            $this->dir = $_SERVER['DOCUMENT_ROOT']."/";
        } catch ( Exception $e ) {
            die( 'Unable to set log' );
        }
    }
    
    public function __destruct()
    {
        $this->dir = '';
    }
    
    public function log_all_errors($message, $level)
    {
        if(!defined( 'LOG_DEBUG_ALL' ) || ( defined( 'LOG_DEBUG_ALL' ) && LOG_DEBUG_ALL )){
            $formatted_message = '*'.$level.'* '."\t".date('d-m-Y - H:i:s')."\tFILE: ".($_SERVER["REQUEST_URI"])."\t"."MESSAGGIO: ".$message."\r\n";

            $this->setFilenameError = $this->dir.'log/'.date('Ymd').'_errori_erp.log';

            if(DISPLAY_DEBUG){
                echo "<li>".$formatted_message."</li>";
            }else if(file_put_contents($this->setFilenameError, $formatted_message, FILE_APPEND)){
                /*if (file_exists(_PS_ROOT_DIR_.'/error500.html') && $level!="AVVISO")
                    echo file_get_contents(_PS_ROOT_DIR_.'/error500.html');*/
            }
        }else if((strtoupper($level)=="KO" || strtoupper($level)=="ERRORE")){
            $formatted_message = '*'.$level.'* '."\t".date('d-m-Y - H:i:s')."\tFILE: ".($_SERVER["REQUEST_URI"])."\t"."MESSAGGIO: ".$message."\r\n";

            $this->setFilenameError = $this->dir.'log/'.date('Ymd').'_errori_erp.log';
            
            if(DISPLAY_DEBUG){
                echo "<li>".$formatted_message."</li>";
            }else{
                file_put_contents($this->setFilenameError, $formatted_message, FILE_APPEND);
            }
        }
    }
    
    public function log_db_errors($error, $query, $level)
    {
        $formatted_message = '*'.$level.'* '."\t".date('d-m-Y - H:i:s')."\tFILE: ".($_SERVER["REQUEST_URI"])."\t"."Query: ".htmlentities( $query )."\t"."Errore: ".$error."\r\n";

        $this->setFilenameError = $this->dir.'log/'.date('Ymd').'_errori_database_erp.log';

        if(DISPLAY_DEBUG){
                echo "<li>".$formatted_message."</li>";
        }else if(file_put_contents($this->setFilenameError, $formatted_message, FILE_APPEND)){
            /*if (file_exists($this->dir.'/error500.html') && $level!="AVVISO")
                echo file_get_contents($this->dir.'/error500.html');*/
        }
    }
}

?>