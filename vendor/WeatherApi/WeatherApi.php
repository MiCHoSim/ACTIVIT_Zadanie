<?php
namespace WeatherApi;

use Exception;
use Micho\Utilities\DateTimeUtilities;
use WeatherApi\Connection\Connection;

/**
 * Download weather data
 */
class WeatherApi
{
    /**
     * Names of variables that will come in the request
     */
        const HOURLY_UNITS = 'hourly_units';
            const TEMPERATURE_2M = 'temperature_2m';
        const HOURLY = 'hourly';
            const TIME = 'time';
    /**
     * Settings
     */
    private string $endPoint = '/v1/forecast';
    private string $listMethod = 'GET';
    private string $host = 'api.open-meteo.com';

    private string $latitude;
    private string $longitude;

    private string $url;
    private array $header;

    /**
     ** Instance of Connection
     * @var Connection
     */
    private Connection $connection;

    public function __construct(string $latitude, string $longitude)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->buildUrl();
        $this->buildHeader();
        $this->connection = new Connection();
    }

    /**
     ** Build the request header
     * @return void
     */
    private function buildHeader() : void
    {
        $this->header = array();
    }

    /**
     ** Build the request url
     * @return void
     */
    private function buildUrl() :void
    {
        $this->url = 'https://' . $this->host . $this->endPoint . '?latitude=' . $this->latitude . '&longitude=' . $this->longitude . '&hourly=temperature_2m';
    }

    /**
     ** Get the weather
     * @return array
     * @throws Exception
     */
    public function get(): array
    {
        $requestData = $this->connection->_operation($this->url,$this->header,$this->listMethod);

        if($requestData[Connection::SUCCESS])
        {
            $weather = json_decode($requestData[Connection::RESPONSE],true)[self::HOURLY];

            $temperatureIndex = array_search(DateTimeUtilities::roundActualHour(), $weather[self::TIME]);

            $weatherSelect[self::TEMPERATURE_2M] = $weather[self::TEMPERATURE_2M][$temperatureIndex];
        }
        else
            throw new Exception('Error occured. Code: ' . $requestData[Connection::CODE]);

        return $weatherSelect;
    }
}
/*
 * Autor: MiCHo
 */