<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class cronerp {
    
    private $dir = '';
    private $linkdb = '';
    static $inst = null;
    
    public function __construct($directory, $dblink)
    {
        try {
            $this->dir = $directory;
            $this->linkdb = $dblink;
        } catch ( Exception $e ) {
            die( 'Unable to set log' );
        }
    }
    
    public function __destruct()
    {
        $this->dir = '';
        $this->linkdb = '';
    }
    
    /**
     * Singleton function
     *
     * Example usage:
     * $cron = cronerp::getInstance();
     *
     * @access private
     * @return self
     */
    static function getInstance($directory, $dblink)
    {
        if( self::$inst == null )
        {
            self::$inst = new cronerp($directory, $dblink);
        }
        return self::$inst;
    }
    
    public function executeCron($excTime){
        $risultati = $this->loadCronList($excTime);
        //echo $this->linkdb->get_query()."<br><br>";
        //print_r($risultati);
        foreach ($risultati as $rowCron) {
            echo '<iframe frameborder="0" border="0" width="100%" height="0px;" src="'.BASE_URL.'/libreria/automazioni/'.$rowCron['nomefile'].'"></iframe>';
            //include($this->dir.$rowCron['nomefile']);
            $this->updateNextExcecuteTime($rowCron);
            sleep(1);
        }
    }
    
    private function loadCronList($excTime) {
        return $this->linkdb->get_results("SELECT * FROM lista_cron WHERE UNIX_TIMESTAMP(prossima_esecuzione) <= '$excTime' ");
    }
    
    private function updateNextExcecuteTime($rowCron) {
        
        $nextTime = $this->addtime(time(), $rowCron['ore'], $rowCron['minuti'], $rowCron['secondi'], $rowCron['mesi'], $rowCron['giorni'], $rowCron['anni']);
        
        $this->linkdb->update("lista_cron", array("dataagg" => date("Y-m-d H:i:s"), "scrittore" => "class.cron","ultima_esecuzione" => date("Y-m-d H:i:s"), "prossima_esecuzione" => date("Y-m-d H:i:s",$nextTime)), array("id" => $rowCron['id']));
    }
    
    private function addtime($unixtime, $hr=0, $min=0, $sec=0, $mon=0, $day=0, $yr=0) { 
        $dt = localtime($unixtime, true); 
        $unixnewtime = mktime( 
            $dt['tm_hour']+$hr, $dt['tm_min']+$min, $dt['tm_sec']+$sec, 
            $dt['tm_mon']+1+$mon, $dt['tm_mday']+$day, $dt['tm_year']+1900+$yr); 
        return $unixnewtime;
    } 
    
}