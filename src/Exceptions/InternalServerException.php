<?php

namespace UpliftSocial\SDK\Exceptions;

class InternalServerException extends UpliftSocialException
{
  protected $code = 502;

  protected $message = 'Internal Server Error';

  public function __construct($message = null)
  {
    parent::__construct($message);
  }
}
