<?php


namespace Khatfield\LaravelTimezoneDb;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Collection;
use Khatfield\LaravelTimezoneDb\Exceptions\TimezoneDbException;

class TimezoneDb
{
    protected $api_key;
    protected $premium;
    protected $client;
    protected $connected = false;
    private $base_url    = 'http://api.timezonedb.com/v2.1/';
    private $premium_url = 'http://vip.timezonedb.com/v2.1/';

    /**
     * TimezoneDb constructor.
     *
     * @param Collection $config
     */
    public function __construct($config)
    {
        $this->api_key = $config->get('timezonedb.api_key');
        $this->premium = $config->get('timezonedb.premium');

        $base_url = $this->premium !== false ? $this->premium_url : $this->base_url;

        if(!empty($this->api_key)) {
            $this->client    = new Client(
                [
                    'base_uri' => $base_url,
                ]
            );
            $this->connected = true;
        }
    }

    /**
     * @param string $endpoint
     * @param array  $data
     * @param string $method
     *
     * @return mixed
     * @throws TimezoneDbException
     *
     */
    protected function _doRequest($endpoint, $data, $method = 'get')
    {
        if(!$this->connected) {
            throw new TimezoneDbException('No Credentials Available for TimezoneDB');
        }

        $headers = [
            'Accept' => 'application/json',
        ];

        $query = [
            'key'    => $this->api_key,
            'format' => 'json',
        ];

        $method  = strtoupper($method);
        $options = compact('headers', 'query');

        if($method === 'POST') {
            $options['form_params'] = $data;
        } elseif($method === 'GET') {
            $options['query'] = array_merge($options['query'], $data);
        }

        try {
            $done = false;
            $return = [];

            while(!$done){
                $response = $this->client->request($method, $endpoint, $options);

                $result = json_decode($response->getBody()->getContents());

                if($result->status != 'OK'){
                    throw new TimezoneDbException('Error getting data from TimezoneDB: ' . $result->message);
                } else {
                    $return = array_merge($return, $result->zones);
                }

                if(intval($result->currentPage) === intval($result->totalPage)) {
                    $done = true;
                } else {
                    if(isset($options['query']['page'])){
                        $options['query']['page']++;
                    } else {
                        $options['query']['page'] = 2;
                    }
                }
            }

            return collect($return);

        } catch(GuzzleException $e) {
            throw new TimezoneDbException('Error Processing TimezoneDB Request: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * @param      $city
     * @param      $country
     * @param null $region
     *
     * @return Collection
     * @throws TimezoneDbException
     */
    public function getTimezoneByCity($city, $country, $region = null)
    {
        $data = [
            'by' => 'city',
            'city' => $city,
            'country' => $country,
        ];

        if(!is_null($region) && strcasecmp($country, 'us') === 0){
            $data['region'] = $region;
        }

        return $this->_doRequest('get-time-zone', $data);

    }


}