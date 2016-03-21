<?php

namespace UpliftSocial\SDK\Responses;

class UpdateUserResponse
{
  /**
   * Did the operation complete successfully
   *
   * @var bool
   */
  public $success;

  public function __construct(\stdClass $response = null)
  {
    if ($response && is_object($response))
    {
      $this->success = $response->success;
    }
  }

  public function toArray()
  {
    return [
      'success' => $this->success
    ];
  }

  /**
   * Did the operation complete successfully
   *
   * @return bool
   */
  public function getSuccess()
  {
    return $this->success;
  }
}
