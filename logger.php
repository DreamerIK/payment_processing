<?php

class Logger{
    function log($message){
        $log_file = fopen('log.txt', 'a');
        $timestamp = date('Y-m-d H:i:s');

        fwrite($log_file, "[".$timestamp."] ". $message . "\n");
        fclose($log_file);
    }
}