<?php

namespace App\WeatherModul\Model;

use App\CityModul\Model\CityTable;
use Exception;
use WeatherApi\WeatherApi;

/**
 * Class WeatherManager
 */
class WeatherManager
{
    /**
     ** Load cities and get weather
     * @return array
     * @throws Exception
     */
    public function getWeather() : array
    {
        $cityTable = new CityTable();

        $cities = $cityTable->get();

        $weatherData = array();
        foreach ($cities as $city)
        {
            $this->weatherApi = new WeatherApi($city[CityTable::LATITUDE], $city[CityTable::LONGITUDE]);
            $weatherData[$city[CityTable::NAME]] = $this->weatherApi->get();
        }
        return $weatherData;
    }
}
/*
 * Autor: MiCHo
 */