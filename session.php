<?php

/**
 * Session Klasse, welche die Session vor jeder Operation öffnet und danach direkt wieder schließt
 * Vorteil dieser Variante ist, dass die Session in PHP nicht blockiert. Mehrere Tabs mit längeren komplexen Operationen innerhalb
 * einer Session haben häufig das Problem, dass andere Tabs durch längere Operationen blockieren.
 * Diese Variante überbrückt dieses Problem.
 */
class session {

    /**
     * Ein Konfigurationswert für die Session, der sich aus einer Configdatei speist.
     * @var array
     */
   private array $_config = array(
      'cookie_lifetime' => SESSIONTIME,
      'cookie_path' => '/',
      'cookie_domain' => DOMAIN,
      'cookie_secure' => true,
      'cookie_httponly' => true,
      'cookie_samesite' => 'lax',
   );


    /**
     * Initialisierung der Session
     * @return void
     */

   public static function init():void {
       $time = time()+SESSIONTIME;
       ini_set('session.cookie_lifetime',SESSIONTIME);
       if(session_start(self::$_config)) {
        session_write_close();
       } else
       {
         throw new Exception('[SESSION ERROR] [INIT]');
       }
   }

    /**
     * Setzen der Session mit einem Schluessel-Wert Paar
     * Definiertes SESSION_PREFIX wird dabei vor den jeweiligen Schluessel gesetzt.
     * @param string|int $key
     * @param string|int|float $value
     * @return float|int|string
     */

   public static function set(string|int $key,string|int|float $value):string|int|float {
       session_start(self::$_config);
       $_SESSION[SESSION_PREFIX . $key] = $value;
       session_write_close();
       return $value;
   }

    /**
     * Abrufen eines Wertes mit dem entsprechenden Schluessel und eventuellem Zweitschluessel
     * @param string|int $key
     * @param string|int $secondkey
     * @return mixed
     */

   public static function get(string|int $key, string | int $secondkey = NULL):mixed {
       session_start(self::$_config);
       $return = false;
      if (!is_null($secondkey)) {
         if (isset($_SESSION[SESSION_PREFIX . $key][SESSION_PREFIX .$secondkey])) {
            $return =  $_SESSION[SESSION_PREFIX . $key][SESSION_PREFIX .$secondkey];
             session_write_close();
            return $return;
         }
      } else {
         if (isset($_SESSION[SESSION_PREFIX . $key])) {
             $return = $_SESSION[SESSION_PREFIX . $key];
             session_write_close();
             return $return;
         }
      }
       session_write_close();
       return $return;
   }

    /**
     * Anzeige der Session
     * @return array
     */

   public static function display():array {
       session_start(self::$_config);
       $return = $_SESSION;
       session_write_close();
       return $return;
   }

    /**
     * Entsprechenden Schluessel der Session leeren
     * @param string|int $key
     * @return void
     */

   public static function clear(string|int $key):void {
       session_start(self::$_config);
       unset($_SESSION[SESSION_PREFIX . $key]);
       session_write_close();
   }


    /**
     * Session zerstoeren und aufloesen, aber nur wenn eine Session gestartet wurde.
     * @return void
     */
      public static function destroy():void {

       if(session_status() !== PHP_SESSION_ACTIVE){
           session_start(self::$_config);
           session_unset();
           session_destroy();
           session_write_close();
           setcookie(session_name(),'',0,'/');
           session_regenerate_id(true);
      }

     }

}
