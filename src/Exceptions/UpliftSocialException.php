<?php

namespace UpliftSocial\SDK\Exceptions;

class UpliftSocialException extends \Exception
{
  public function __construct($message = null, $code = null)
  {
    if ($message)
    {
      $this->message = $message;
    }

    if ($code)
    {
      $this->code = $code;
    }
  }
}
