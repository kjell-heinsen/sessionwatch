<?php

class sessionwatch {

    public static function setup():void {
        $path = DOCROOT.'/data/session';
        if(!file_exists($path)){
            if(!mkdir($path) && !is_dir($path)){
                throw new \Exception('Der Pfad konnte nicht angelegt werden.');
            }
        } else {
            throw new \Exception('Der Pfad konnte nicht angelegt werden.');
        }

    }

    public static function write(?string $param = null):void{
        $data['session'] = session_id();
        $data['time'] = time();
        $data['class'] = debug_backtrace()[1]['class'];
        $data['function'] = debug_backtrace()[1]['function'];
        if(!is_null($param)){
        $data['param'] = $param;
        }

        $user = NULL;
        if(isset($_SESSION['username'])){
            $user = $_SESSION['username'];
        }
        self::log($data,$user);
    }


    public static function log(array $data,?string $user = NULL):void{
        $filename = DOCROOT.'/data/session/'.$user.'_'.$data['session'].'.json';
        $json = json_encode($data);
        $str = json_decode($json,true);
        if(($temp = file_get_contents($filename)) != false){
            $tempArray = json_decode($temp,true);
        }
        array_push($tempArray,$str);
        $data = json_encode($tempArray);
        file_put_contents($filename,$data);
    }


    public static function read():?array{

}

    public static function files():?array{
        $files = NULL;
        $path = DOCROOT.'/data/session/';
        if(file_exists($path)){
            $files = scandir($path);
            if($files != false){
                if(($key = array_search('.',$files)) != false){
                    unset($files[$key]);
                }
                if(($key = array_search('..',$files)) != false){
                    unset($files[$key]);
                }
            }
        }

        return $files;


    }


}