<?php

namespace Micho;

use PDO;

/**
 ** Wraper pre lahšiu prácu s databázou s použitim PDO a automatyckým zabezpečením parametrov (premenných) v dopytoch
 * Class Db
 * @package Micho
 */
class Db 
{
    /**
     * @var PDO Databazové spojenie 
     */
    private static $connection;
    
    /**
     * @var array Zakladné nastavenie ovladača
     */
    private static $nastavenie = array(
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
    PDO::ATTR_EMULATE_PREPARES => false,);  // nastavenie pre databazu
    
    /**
     ** Pripojenie sa k databázi pomocou zadaých údajov
     * @param string $hostitel Hostiteľ
     * @param string $uzivatel Uživateľské meno
     * @param string $heslo Heslo
     * @param string $databaza Názov databázi
     */
    public static function connection($hostitel, $uzivatel, $heslo, $databaza)
    {
        if (!isset(self::$connection))    //zistenie ci existuje spojenie
        {
            self::$connection = @new PDO("mysql:host=$hostitel;dbname=$databaza", $uzivatel, $heslo, self::$nastavenie); //vytvorenie spojenia k databaze
        }        
    }
    
    /**
     ** Spusti dopyt a vrati z neho prvý riadok
     * @param string $query Dopyt sql
     * @param array $param Parametre k dopytu
     * @return mixed Pole výsledkov alebo FALSE
     */
    public static function queryOneRow($query, $param = array())
    {
        $value = self::$connection->prepare($query); // Hodnota a Dopyt sa predajú oddelene Kvôli => "SQL injection"
        $value->execute($param);
        return $value->fetch();
    }
    
    /**
     ** Spustí dopyt a vrati z neho všetky riadky
     * @param string $query Dopyt sql
     * @param array $param Parametre k dopyu
     * @return mixed Pole výsledkov alebo FALSE
     */
    public static function queryAllRows($query, $param = array())
    {
        $value = self::$connection->prepare($query); // Hodnota a Dopyt sa predajú oddelene Kvôli => "SQL injection"
        $value->execute($param);
        return $value->fetchAll();
    }
    
    /**
     ** Spustí dopyt a vrati z neho prvý stlpce prvého riadku
     * @param string $query Dopyt sql
     * @param array $parameter Parametre k dopyt
     * @return mixed Prvá hodnota výsledkov alebo FALSE
     */
    public static function queryAlone($query, $parameter = array())
    {
        $resul = self::queryOneRow($query, $parameter);
        return isset($resul[0]) ? $resul[0] : false;
    }
    
    /**
     ** Spusti dopyt a vráti počet ovplivnených riadkov
     * @param string $query Dopyt sql
     * @param array $parameters Parametre k dopytu
     * @return int Počet ovplyvnených riadkov
     */
    public static function query($query, $parameters = array()) : int
    {
        $value = self::$connection->prepare($query); // Hodnota a Dopyt sa predajú oddelene Kvôli => "SQL injection"
        $value->execute($parameters);
        return $value->rowCount();
    }

    /**
     ** Vloží do tabulky nový riadok ako data za asociativného pola ale v pripade konflikut aktualizje stary
     * @param string $table Názov Tabuľky
     * @param array $data Asociativne pole z datmi
     * * @param bool $updateOld Či chem v pripade dupkikatu upraviŤ starý
     * @return int Počet ovplyvnených riadkov
     */
    public static function insert($table, $data = array(), bool $updateOld = false) //vlozi clanky do databaze
    {
        $query = 'INSERT INTO ' . $table;

        if(isset($data[0]) && is_array($data[0])) // ak je prichadzajui parameter pole ulozim vŠetky riadky
        {
            $query .= ' (' . implode(', ', array_keys($data[0])) . ')';
            $query .= ' VALUES ';
            $param = '';
            $lastItem = count($data);
            $val = '(' . str_repeat('NULLIF(?,""), ', sizeOf($data[0])-1) . 'NULLIF(?,""))';
            foreach ($data as $parameter)
            {
                $query .= $val;
                if(0 !== --$lastItem)
                    $query .= ', ';
                $param .= implode('_;_', $parameter) . '_;_';
            }
            $param = explode('_;_', $param);
            array_pop($param);
        }
        else
        {
            $query .= ' (' . implode(', ', array_keys($data)) . ')';
            $query .= ' VALUES (' . str_repeat('?, ', sizeOf($data)-1) . '?)';
            $param = $data;
            $data[0] = $data; // pridané hovli tomu že keď robim if($updateOld) ---- tak  chyba na  count($section[0]) musi to byť pole
        }
        //ak chcem v pripade duplikatu aktualizovať stary
        if($updateOld)
        {
            $query .= ' ON DUPLICATE KEY UPDATE ';
            $lastItem = count($data[0]);
            foreach ($data[0] as $key => $parameter)
            {
                $query .= $key . ' = values(' . $key . ')';
                if(0 !== --$lastItem)
                    $query .= ', ';
            }
        }
        self::query($query, array_values($param));
        return Db::returnLastId();
    }

    /**
     ** Zmeni riadok v tabulke tak, aby obsahoval data z asociativného poľa
     * @param string $table Názov tabuľky
     * @param array $values Asociatívne pole z dátami
     * @param string $condition Časť SQL dopytu s podmienkov vrátane WHERE
     * @param array $parameters Parametre dopytu
     * @return int Počet ovplyvnených riadkov
     */
    public static function update($table, $values = array(), $condition, $parameters = array())
    {
        return self::query("UPDATE `$table` SET `".
                implode('` = ?, `', array_keys($values)).
                "` = ? " . $condition,
                array_merge(array_values($values), $parameters));
    }
    
    /**
     * @return string Vracia ID posledného vloženého záznamu
     */
    public static function returnLastId()
    {
        return self::$connection->lastInsertId(); //vrati id posledneho vloženého zaznamu
    } 
    
    /**
     ** Vytvori páry z dopyty
     * @param string $dopyt Dopyt na dB
     * @param string $klucStlpec  Klúč riadku, ktorý bude kľúčom výstupného poľa
     * @param string $hodnotaStlpec hodnoty
     * @param array $parametre Klúč riadku, ktorý bude hodnotou výstupného poľa
     * @return array pary
     */
    public static function dopytPary($dopyt, $klucStlpec, $hodnotaStlpec, $parametre = array())
    {
        return Pole::ziskajPary(self::queryAllRows($dopyt, $parametre), $klucStlpec, $hodnotaStlpec);
    }
    
    /**
     ** Uloženie viacerých riadkov do DB súčastne
     * @param string $tabulka nazov tabuľky
     * @param array $parametre paramatre v poli
     * @return int Počet ovplyvnených riadkov
     */
    public static function vlozVsetko($tabulka, $parametre = array())
    {
        $parameter = array();
        $dopyt = rtrim("INSERT INTO `$tabulka` (`".
        implode('`, `', array_keys($parametre[0])).
        "`) VALUES " . str_repeat('(' . str_repeat('?,', sizeOf($parametre[0])-1)."?), ", sizeOf($parametre)), ', ');
        
        foreach ($parametre as $riadky)
        {
            $parameter = array_merge($parameter, array_values($riadky));
        }
        return self::query($dopyt, $parameter);
    }
    
    /**
     ** Začne transakciu
     */
    public static function startTransaction()
    {
        self::$connection->beginTransaction();
    }
    
    /**
     ** Dokončí transakciu
     */
    public static function endTransaction()
    {
        self::$connection->commit();
    }
    
    /**
    ** Stornuje transakciu
    */
    public static function vratSpat()
    {
        self::$connection->rollBack();
    }
}
