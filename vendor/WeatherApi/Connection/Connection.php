<?php
namespace WeatherApi\Connection;

use Exception;


/**
 ** Creating a connection to load data
 */
class Connection
{
    /**
     * Request constants
     */
    const SUCCESS = 'success';
    const CODE = 'code';
    const RESPONSE = 'response';
    const REQUEST_ID = 'requestId';

    /**
     * @var CurlRequest Instance
     */
    private CurlRequest $curlRequest;

    public function __construct()
    {
        $this->curlRequest = new CurlRequest();
    }

    /**
     ** Performs a request for Data
     * @param string $url request Url
     * @param array $header header
     * @param string $method resuest Method GET/POST...
     * @param array $params Parametre
     * @return array|mixed
     * @throws Exception
     */
    public function _operation(string $url, array $header, string $method, array $params = array())
    {
        switch (strtolower($method))
        {
            case "get":
                if (!empty($params))
                {
                    $url .= "?";
                    foreach ($params as $k => $v)
                    {
                        $url .= "{$k}=".rawurlencode($v)."&";
                    }
                    $url = rtrim($url, "&");
                }
                break;
            case "put":case"post":case"delete":
                if (!empty($params))
                {
                    $data = json_encode($params);
                    $this->curlRequest->setOption(CURLOPT_POST, true);
                    $this->curlRequest->setOption(CURLOPT_POSTFIELDS, $data);
                }
                break;
            default:
                throw new Exception("Unknown verb {$method}.");
        }
        $this->curlRequest->setOption(CURLOPT_URL, $url);
        $this->curlRequest->setOption(CURLOPT_HTTPHEADER, $header);
        $this->curlRequest->setOption(CURLOPT_CUSTOMREQUEST, strtoupper($method));

        return $this->_executeRequest();
    }

    /**
     ** Executes the request
     * @return array request data
     */
    public function _executeRequest()
    {
        $response = $this->curlRequest->execute();
        $requestId = $this->curlRequest->requestId;

        $response_info = $this->curlRequest->getInfo();
        $this->curlRequest->close();

        if (!preg_match("/^(2|3)\d{2}$/", $response_info["http_code"]))
        {
            $requestId = 0;
            $json = json_decode($response, true);
            if (!is_null($json))
                if (array_key_exists(self::REQUEST_ID, $json))
                    $requestId = json_decode($response, true)[self::REQUEST_ID];

            return array(self::SUCCESS => false,
                self::CODE => $response_info["http_code"],
                self::RESPONSE => $response,
                self::REQUEST_ID => $requestId);
        }
        else
            return array(self::SUCCESS => true,
                self::CODE => $response_info["http_code"],
                self::RESPONSE => $response,
                self::REQUEST_ID => $requestId);
    }
}