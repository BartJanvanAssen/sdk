<?php

namespace ReClickdAPI;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Post\PostBody;
use GuzzleHttp\Client as GuzzleClient;
use ReClickdAPI\Exceptions\BadRequestException;
use ReClickdAPI\Exceptions\InternalServerException;
use ReClickdAPI\Exceptions\NotFoundException;
use ReClickdAPI\Exceptions\ReClickdException;
use ReClickdAPI\Exceptions\UnauthorizedException;
use ReClickdAPI\Responses\UserResponse;

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

  // Endpoints
  const ENDPOINT_USER = '/v1/users/';

  // Request Methods
  const METHOD_OPTIONS = 'OPTIONS';
  const METHOD_POST = 'POST';
  const METHOD_HEAD = 'HEAD';
  const METHOD_GET = 'GET';
  const METHOD_PUT = 'PUT';
  const METHOD_PATCH = 'PATCH';
  const METHOD_DELETE = 'DELETE';

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
  public function setApiKey($apiKey)
  {
    self::$apiKey = $apiKey;
  }

  /**
   * Gets a User by ID
   *
   * @param int $id
   * @param string|null $apiKey
   *
   * @throws \ReClickdAPI\Exceptions\BadRequestException
   * @throws \ReClickdAPI\Exceptions\UnauthorizedException
   * @throws \ReClickdAPI\Exceptions\InternalServerException
   * @throws \ReClickdAPI\Exceptions\ReClickdException
   * @throws \Exception
   *
   * @returns \ReClickdAPI\Responses\UserResponse
   */
  public static function getUser($id, $apiKey = null)
  {
    $response = self::getResponse(self::METHOD_GET, self::ENDPOINT_USER.$id, [], $apiKey);

    return new UserResponse($response);
  }

  /**
   * Lists all Users belonging to ReClickd Partner
   *
   * Returns an array of User objects with User ID as key
   *
   * @param string|null $apiKey
   *
   * @throws \ReClickdAPI\Exceptions\BadRequestException
   * @throws \ReClickdAPI\Exceptions\UnauthorizedException
   * @throws \ReClickdAPI\Exceptions\InternalServerException
   * @throws \ReClickdAPI\Exceptions\ReClickdException
   * @throws \Exception
   *
   * @returns \ReClickdAPI\Responses\UserResponse[]
   */
  public static function listUsers($apiKey = null)
  {
    $response = self::getResponse(self::METHOD_GET, self::ENDPOINT_USER, [], $apiKey);

    $users = [];
    foreach ($response as $user)
    {
      $users[$user->id] = new UserResponse($user);
    }

    return $users;
  }

  /**
   * Creates a User
   *
   * @param string $fullName
   * @param string $email
   * @param string|null $apiKey
   *
   * @throws \ReClickdAPI\Exceptions\BadRequestException
   * @throws \ReClickdAPI\Exceptions\UnauthorizedException
   * @throws \ReClickdAPI\Exceptions\InternalServerException
   * @throws \ReClickdAPI\Exceptions\ReClickdException
   * @throws \Exception
   *
   * @returns \ReClickdAPI\Responses\UserResponse
   */
  public static function createUser(
    $fullName,
    $email,
    $apiKey = null
  )
  {
    $post = [
      'fullName' => $fullName,
      'email' => $email
    ];

    $response = self::getResponse(self::METHOD_POST, self::ENDPOINT_USER, $post, $apiKey);

    return new UserResponse($response);
  }

  /**
   * Updates a User
   *
   * @param int $id
   * @param array $params
   * @param string|null $apiKey
   *
   * @throws \ReClickdAPI\Exceptions\BadRequestException
   * @throws \ReClickdAPI\Exceptions\UnauthorizedException
   * @throws \ReClickdAPI\Exceptions\InternalServerException
   * @throws \ReClickdAPI\Exceptions\ReClickdException
   * @throws \Exception
   *
   * @returns \ReClickdAPI\Responses\UserResponse
   */
  public static function updateUser($id, $params, $apiKey = null)
  {
    $response = self::getResponse(self::METHOD_PATCH, self::ENDPOINT_USER.$id, $params, $apiKey);

    return new UserResponse($response);
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
   * @throws \ReClickdAPI\Exceptions\InternalServerException
   * @throws \ReClickdAPI\Exceptions\ReClickdException
   * @throws \Exception
   *
   * @return mixed
   */
  private static function getResponse(
    $method,
    $path,
    $params = [],
    $apiKey = null
  ) {
    if ($params && !is_array($params))
    {
      throw new BadRequestException('Invalid parameters provided');
    }

    $params = $params ?: [];

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

    $client = new GuzzleClient(
      [
        'base_url' => self::$apiBase,
        'defaults' => [
          'headers' => self::getAuthHeader($apiKey)
        ]
      ]
    );

    $request = $client->createRequest(
      $method,
      $path
    );

    if (self::methodHasBody($method))
    {
      $body = new PostBody();

      foreach ($params as $field => $value)
      {
        $body->setField($field, $value);
      }

      $request->setBody($body);
    }
    else
    {
      $request->setQuery($params);
    }

    // Get response
    try
    {
      $response = $client->send($request);

      $body = json_decode($response->getBody());

      if (isset($body->data))
      {
        if ($body->messages == 'error')
        {
          self::handleResponseError($body);
        }

        return $body->data;
      }
    }
    catch (ClientException $e)
    {
      throw new InternalServerException($e->getMessage());
    }

    throw new \Exception('Error calling ' . $method . ' to: ' . $path);
  }

  public static function handleResponseError(\stdClass $response)
  {
    if ($response->data->error_code == 400)
    {
      throw new BadRequestException($response->data->error_message);
    }
    elseif ($response->data->error_code == 401)
    {
      throw new UnauthorizedException($response->data->error_message);
    }
    elseif ($response->data->error_code == 404)
    {
      throw new NotFoundException($response->data->error_message);
    }
    elseif ($response->data->error_code == 502)
    {
      throw new InternalServerException($response->data->error_message);
    }

    throw new ReClickdException($response->data->error_message, $response->data->error_code);
  }

  /**
   * Gets the Authorization header for the request
   *
   * @param string $apiKey
   *
   * @returns string[]
   */
  private static function getAuthHeader($apiKey)
  {
    return [
      'Authorization' => sprintf('Bearer %s', $apiKey)
    ];
  }

  private static function methodHasBody($method)
  {
    switch ($method)
    {
      default:
        return false;
        break;

      case self::METHOD_POST:
      case self::METHOD_PUT:
      case self::METHOD_DELETE:
        return true;
        break;
    }
  }
}
