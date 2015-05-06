<?php

namespace ReClickdAPI\Responses;

class UserResponse
{
  /**
   * User's identification
   *
   * @var int
   */
  public $id;

  /**
   * User's full name
   *
   * @var string
   */
  public $fullName;

  /**
   * Status of Users account
   *
   * Can take values: active, suspended
   *
   * @var string
   */
  public $status;

  /**
   * Timestamp of user creation date
   *
   * @var int
   */
  public $created;

  /**
   * URL of auto login endpoint
   *
   * @var string
   */
  public $autoLoginUrl;

  public function __construct(\stdClass $response = null)
  {
    if ($response && is_object($response))
    {
      $this->id = $response->id;
      $this->fullName = $response->fullName;
      $this->status = $response->status;
      $this->created = $response->created;
      $this->autoLoginUrl = $response->autoLoginUrl;
    }
  }
}
