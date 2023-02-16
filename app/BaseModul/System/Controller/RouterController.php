<?php


namespace App\BaseModul\System\Controller;

use App\BaseModul\System\Model\ManagerController;
use Settings;

/**
 ** Smerovač, na ktorý sa uživateľ dostane po zadaní URL adresy Spracuje ceku app: Hlavné rozloženie stránky a podstránky
 * Class RouterController
 * @package App\BaseModul\System\Controller
 */
class RouterController extends Controller
{
    /**
     * @var array Načitaný kontrolér pole
     */
    public static $subPageControllerArray;

    /**
     ** Smerovanie načitavania stránky
     * @param string $parameters Reťazec url adresy
     */
    public function index(string $parameters)
    {
        $parsedUrl = $this->parseUrl($parameters);

        $this->fullPageRequest($parsedUrl); // spracovávame požiadavky na kontroléry celej stránky
    }

    /**
     ** Spracuje žiadosť o kontroléry App
     * @param array $parsedUrl Pole parametrov z URL
     */
    private function fullPageRequest(array $parsedUrl)
    {
        // Kontrolér pre podstránky
        $this->loadSubPageController($parsedUrl);
        $this->data['subpageController'] = $this->subPageController;

        // <head> nastaveni premenných pre šablonu
        $this->data['title'] = self::$subPageControllerArray[ManagerController::TITLE];
        $this->data['domain'] = Settings::$domain;
        $this->data['description'] = self::$subPageControllerArray[ManagerController::DESCRIPTION];
        $this->data['author'] = Settings::$authorWebu;
        $this->data['domainName'] = Settings::$domainName;

        // <body> nastaveni premenných pre šablonu
        $this->data['messages'] = $this->getMessages();

        $this->data['menu'] = [
            ['class' => $parsedUrl[0] === 'weather' || $parsedUrl[0] === 'city' ? 'active' : '' , 'title' => 'Zadanie 1 (weather)', 'href' => 'weather'],
            ['class' => $parsedUrl[0] === 'multiple-form' ? 'active' : '' , 'title' => 'Zadanie 2 (form)', 'href' => 'multiple-form/save']
        ];

        $this->view = 'layout'; // nastavenie hlavného pohladu/šablony
    }

    /**
     ** Spracuje žiadosť na API
     * @param string $parametre Pole paremetrov z URL
     */
    private function processApiRequest(string $parametre)
    {
        $polozky = explode('-', $parametre); //rozbitie mennych priestorov podľa "-"
        array_splice($polozky, count($polozky) - 1, 0, 'Kontroler'); // pridanie "Kontroler"

        $kontrolerCesta = 'App\\' . implode('\\', $polozky);
        $kontrolerCesta .= 'Kontroler';

        if (preg_match('/^[a-zA-Z\d\\\\]*$/u', $kontrolerCesta))// bezpečnostná kontrola cesty
        {
            $kontroler = new $kontrolerCesta(true);
            $kontroler->callActionFromParameters($parametre, true);
            $kontroler->vypisPohlad();
        }
        else
            $this->redirect('error');
    }

    /**
     ** Naparsuje URL adresu podľa lomitok a vráti pole parametrov
     * @param string $url URL adresa
     * @return array Naparsovana URL adresa
     */
    private function parseUrl(string $url) : array
    {
        $parsedUrl = parse_url($url); // naparsuje jednotlive časti URL adresy do asociativného pola

        $parsedUrl['path'] = ltrim($parsedUrl['path'], '/'); // odstráni začiatočné lomítko

        self::$currentUrl = $parsedUrl['path'] = trim($parsedUrl['path']); // odstránenie bielich znakov okolo adresy

        return explode('/', $parsedUrl['path']); // rozbitie reťazca podľa lomítok
    }

    /**
     ** Načitanie vnoreného kontroléra
     * @param array $parsedUrl Pole parametrov pre kontrolér, pokiaľ niejaké má
     */
    public function loadSubPageController(array $parsedUrl)
    {
        $managerController = new ManagerController(); // vytvorenie instancie modelu pre správu kontrolérov
        $controllerUrl = array_shift($parsedUrl);
        if (empty($controllerUrl))
            $controllerUrl = 'weather';

        self::$subPageControllerArray = $managerController->loadController($controllerUrl); // ziskanie kontoléru podľa URL
        if (!self::$subPageControllerArray[ManagerController::CONTROLLER_PATH]) // pokiaľ nebol kontrolér s danou URL nájdeny, Presmeruje na ChybaController
            $this->redirect('error');
        $pathController = 'App\\' . self::$subPageControllerArray[ManagerController::CONTROLLER_PATH] . 'Controller';

        $this->subPageController = new $pathController(); // instancia vnoreného kontroléra
        $this->subPageController->callActionFromParameters($parsedUrl);
    }
}