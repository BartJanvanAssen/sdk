<?php

namespace ReClickdAPI;

use GuzzleHttp\Post\PostBody;
use Phalcon\DI;
use GuzzleHttp\Message\Request;
use GuzzleHttp\Client as GuzzleClient;
use ReClickdAPI\Exceptions\BadRequestException;
use ReClickdAPI\Request\RequestMethods;

/**
 * Native PHP Wrapper for the ReClickd API
 *
 * This class can be used as a standalone client for HTTP based requests
 */
class Client
{
  /**
   * @var string The ReClickd API key to be used for requests.
   */
  public static $apiKey;

  /**
   * @var string The base URL for the ReClickd API.
   */
  public static $apiBase = 'https://api.reclickd.com';

  /** @var string */
  public $url;

  public function __construct($apiKey = null)
  {
    if ($apiKey)
    {
      self::$apiKey = $apiKey;
    }
  }

  /**
   * @param string $apiKey
   */
  public static function setApiKey($apiKey)
  {
    self::$apiKey = $apiKey;
  }

  /**
   * @return Client
   * @throws \Exception
   */
  public static function getInstance()
  {
    static $instance;

    if (!isset($instance))
    {
      $instance = new self;
    }

    return $instance;
  }

  /**
   * Makes a GET call to API endpoint
   *
   * @param string $path
   * @param array|null $params
   * @param string|null $apiKey
   *
   * @return \stdClass
   */
  public static function get($path, $params = [], $apiKey = null)
  {
    return self::getResponse(RequestMethods::METHOD_GET, $path, $params, $apiKey);
  }

  /**
   * Makes a POST call to API endpoint
   *
   * @param string $path
   * @param array|null $params
   * @param string|null $apiKey
   *
   * @throws \ReClickdAPI\Exceptions\BadRequestException
   * @throws \ReClickdAPI\Exceptions\UnauthorizedException
   * @throws \Exception
   *
   * @return \stdClass
   */
  public static function post($path, $params = [], $apiKey = null)
  {
    return self::getResponse(RequestMethods::METHOD_POST, $path, $params, $apiKey);
  }

  /**
   * Makes a PUT call to API endpoint
   *
   * @param string $path
   * @param array|null $params
   * @param string|null $apiKey
   *
   * @throws \ReClickdAPI\Exceptions\BadRequestException
   * @throws \ReClickdAPI\Exceptions\UnauthorizedException
   * @throws \Exception
   *
   * @return \stdClass
   */
  public static function put($path, $params = [], $apiKey = null)
  {
    return self::getResponse(RequestMethods::METHOD_PUT, $path, $params, $apiKey);
  }

  /**
   * Makes a PATCH call to API endpoint
   *
   * @param string $path
   * @param array|null $params
   * @param string|null $apiKey
   *
   * @throws \ReClickdAPI\Exceptions\BadRequestException
   * @throws \ReClickdAPI\Exceptions\UnauthorizedException
   * @throws \Exception
   *
   * @return \stdClass
   */
  public static function patch($path, $params = [], $apiKey = null)
  {
    return self::getResponse(RequestMethods::METHOD_PATCH, $path, $params, $apiKey);
  }

  /**
   * Makes a DELETE call to API endpoint
   *
   * @param string $path
   * @param array|null $params
   *
   * @throws \ReClickdAPI\Exceptions\BadRequestException
   * @throws \ReClickdAPI\Exceptions\UnauthorizedException
   * @throws \Exception
   *
   * @return \stdClass
   */
  public static function delete($path, $params = null)
  {
    return self::getResponse(RequestMethods::METHOD_DELETE, $path, $params);
  }

  /**
   * Makes a cURL HTTP request to the API and returns the response
   *
   * @param string $method
   * @param string $path
   * @param array|null $params
   * @param string|null $apiKey
   *
   * @throws \ReClickdAPI\Exceptions\BadRequestException
   * @throws \ReClickdAPI\Exceptions\UnauthorizedException
   * @throws \Exception
   *
   *
   * @return \stdClass
   */
  private function getResponse(
      $method,
      $path,
      $params = [],
      $apiKey = null
  ) {
    $client = new GuzzleClient(
      [
        'base_url' => self::$apiBase
      ]
    );

    if ($params && !is_array($params))
    {
      throw new BadRequestException('Invalid parameters provided');
    }

    if (!$apiKey)
    {
      if (!isset(self::$apiKey))
      {
        throw new BadRequestException(
          'Please provide an access token with your request'
        );
      }

      $apiKey = self::$apiKey;
    }

    $body = $query = null;
    if ($this->methodHasBody($method))
    {
      $body = new PostBody();

      foreach ($params as $field => $value)
      {
        $body->setField($field, $value);
      }
    }
    else
    {
      $query = $params;
    }

    $headers = $this->getAuthHeader($apiKey);

    // Prepare the request
    $request = new Request(
      $method,
      sprintf(
        '%s/%s',
        self::$apiBase,
        ltrim('/', $path)
      ),
      $headers,
      $body,
      []
    );

    if ($query)
    {
      $request->setQuery($query);
    }

    // Get response
    $response = $client->send($request);
    $body = json_decode($response->getBody());

    if (isset($body->data))
    {
      return $body->data;
    }
    else
    {
      throw new \Exception('Error calling ' . $method . ' to: ' . $path);
    }
  }

  /**
   * Gets the Authorization header for the request
   *
   * @param string $apiKey
   *
   * @returns string[]
   */
  public function getAuthHeader($apiKey)
  {
    return [sprintf('Bearer %s', $apiKey)];
  }

  private function methodHasBody($method)
  {
    switch ($method)
    {
      default:
        return false;
        break;

      case RequestMethods::METHOD_POST:
      case RequestMethods::METHOD_PATCH:
      case RequestMethods::METHOD_PUT:
      case RequestMethods::METHOD_DELETE:
        return true;
        break;
    }
  }
}
