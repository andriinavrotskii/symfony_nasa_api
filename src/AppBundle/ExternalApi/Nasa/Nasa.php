<?php

namespace AppBundle\ExternalApi\Nasa;

use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;


class Nasa
{
    protected $container;

    /**
     * Nasa constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @return array|GuzzleResponse|mixed
     * @throws NasaException
     */
    public function getNeo()
    {
        $startDate = (new \DateTime())
            ->modify("-{$this->container->getParameter('nasa_request_period_days')} day")
            ->format('Y-m-d');
        $endDate = (new \DateTime())
            ->format('Y-m-d');

        $url = 'https://api.nasa.gov/neo/rest/v1/feed?'
            . 'start_date=' . $startDate
            . '&end_date=' . $endDate
            . '&detailed=false'
            . '&api_key=' . $this->container->getParameter('nasa_api_key');

        return $this->checkAndReturnResponse($this->apiRequest($url));
    }

    /**
     * @param $url
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws NasaException
     */
    protected function apiRequest($url)
    {
        try {
            $client = new Guzzle;
            $response = $client->request('GET', $url);

            if ($response->getStatusCode() !== 200) {
                throw new NasaException("NASA API error: Status code " . $response->getStatusCode());
            }

            return $response;

        } catch (RequestException $e) {
            throw new NasaException("NASA API error: " . $e->getMessage());
        }
    }

    /**
     * @param GuzzleResponse $response
     * @return array|GuzzleResponse|mixed
     * @throws NasaException
     */
    protected function checkAndReturnResponse(GuzzleResponse $response)
    {
        if (!$response->getBody()) {
            throw new NasaException("NASA API error: Response body is empty");
        }

        $response = json_decode((string) $response->getBody());

        if (!property_exists($response, "near_earth_objects")) {
            throw new NasaException("NASA API error: near_earth_objects not found in response");
        }

        $response = (array) $response->near_earth_objects;

        if (count($response) !== $this->container->getParameter('nasa_request_period_days')+1) {
            throw new NasaException("NASA API error: near_earth_objects not valid");
        }

        return $response;
    }
}