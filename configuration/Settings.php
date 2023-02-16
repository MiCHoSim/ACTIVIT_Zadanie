<?php
/**
 * Centrálny súbor na všetky nastavenia
 */
class Settings
{
                                       public static $debug = true;    //či projekt debutujeme na localhoste alebo na produkčnom servery
                                       public static $domain = 'localhost';   // doména, na ktorej bež web
                                       public static $domainName = 'ActivIT';   // Názov domény na ktorej beži web
                                       public static $slogan = 's.r.o.';
                                       public static $db = array ('user' => 'root',
                                                                  'host' => '127.0.0.1',
                                                                  'password' => '',
                                                                  'database' => 'activ-it');    //pristupové údaje
    // Kontakt na autora webu
    public static $authorWebu = 'MiCHo'; // Autor Webu
    public static $authorEmail = 'simalmichal@gmail.com'; // Kontaktný email autor
    public static $authorTel = '0914278743'; // Kontaktny tel autor
}