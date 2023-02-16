<?php


namespace App\BaseModul\System\Model;

use Micho\Db;
use Micho\Utilities\ArrayUtilities;


/**
 ** Trieda poskytuje metódy pre správu kontrolérov v redakčnom systéme
 * Class SpravaPoziadaviekManazer
 * @package App\ZakladModul\SpravaPoziadaviek\Model
 */
class ManagerController
{
    /**
     * Názov Tabuľky pre Spracovanie Kontrolérov
     */
    const TABLE_NAME = 'controller';

    /**
     * Konštanty Databázy 'kontroler'
     */
    const CONTROLLER_ID = 'controller_id';
    const TITLE = 'title';
    const URL = 'url';
    const DESCRIPTION = 'description';
    const CONTROLLER_PATH = 'controller_path';

    /**
     ** Načíta kontrolér z Db a uloží ho do statickej vlastnosti $kontroler
     * @param string $url Url kontroléra
     */
    public function loadController(string $url)
    {
        $keys = array (self::CONTROLLER_ID, self::TITLE, self::URL, self::DESCRIPTION, self::CONTROLLER_PATH); // názvy stĺpcov,ktoré chcem z tabuľky načitať

       return $this->getController($url, $keys);
    }

    /**
     ** Vráti kontrolér z db podľa jeho URL
     * @param string $url Url kontroléra
     * @param array $keys Klúče Ktoré chcem načitať
     * @return array|mixed Pole s kontrolérom alebo FALSE pri neúspechu
     */
    public function getController(string $url, array $keys) :array|false
    {
        $data = Db::queryOneRow('SELECT ' . implode(', ',$keys) . ' 
                                    FROM ' . self::TABLE_NAME . ' WHERE url = ?', array($url));

        return is_array($data) ? ArrayUtilities::filterKeys($data, $keys) : $data;
    }

    /**
     ** Vráti zoznam kontrolérov v db
     * @return mixed Zoznam kontrolérov
     */
    public function vratKontrolery()
    {
        return Db::queryAllRows('SELECT kontroler_id, titulok, url, popisok, kontroler
                                      FROM kontroler ORDER BY kontroler_id DESC ');
    }

    /**
     ** Odstráni kontrolér
     * @param string $url URL kontoléru
     */
    public function odstranKontroler($url)
    {
        Db::query('DELETE FROM kontroler WHERE url = ?', array($url));
    }
}