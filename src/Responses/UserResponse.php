<?php

namespace UpliftSocial\SDK\Responses;

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
   * User's email address
   *
   * @var string
   */
  public $email;

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
      $this->email = $response->email;
      $this->status = $response->status;
      $this->created = $response->created;
      $this->autoLoginUrl = $response->autoLoginUrl;
    }
  }

  public function toArray()
  {
    return [
      'id' => $this->id,
      'fullName' => $this->fullName,
      'email' => $this->email,
      'statuc' => $this->status,
      'created' => $this->created,
      'autoLoginUrl' => $this->autoLoginUrl
    ];
  }

  /**
   * Get the User ID
   * @return int
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * Get the User's full name
   * @return int
   */
  public function getFullName()
  {
    return $this->fullName;
  }

  /**
   * Get the User's email address
   * @return int
   */
  public function getEmail()
  {
    return $this->email;
  }

  /**
   * Get the status of the user's account
   * @return int
   */
  public function getStatus()
  {
    return $this->status;
  }

  /**
   * Get the created date timestamp
   * @return int
   */
  public function getCreated()
  {
    return $this->created;
  }

  /**
   * Get the URL to use for auto logins
   * @return int
   */
  public function getAutoLoginUrl()
  {
    return $this->autoLoginUrl;
  }
}
