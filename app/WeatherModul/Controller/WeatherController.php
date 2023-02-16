<?php

namespace App\WeatherModul\Controller;

use App\BaseModul\System\Controller\Controller;
use App\WeatherModul\Model\WeatherManager;

/**
 * Class WeatherController
 */
class WeatherController extends Controller
{
    /**
     * @var WeatherManager of WeatherApi
     */
    private WeatherManager $weatherManager;

    public function __construct()
    {
        $this->weatherManager = new WeatherManager();
    }

    /**
     ** Data processing for the weather view
     * @return void
     * @Action
     */
    public function index()
    {
        $this->data['weatherData'] = $this->weatherManager->getWeather();

        $this->data['menu'] = [
            ['class' => 'active', 'title' => 'Weather forecast', 'href' => 'weather'],
            ['class' => '', 'title' => 'City Administration', 'href' => 'city/administration']
        ];

        $this->data['subview'] = '../app/WeatherModul/View/Weather/weather';
        $this->view = '../vendor/Micho/layout';
    }
}
/*
 * Autor: MiCHo
 */