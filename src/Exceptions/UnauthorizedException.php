<?php

namespace UpliftSocial\SDK\Exceptions;

class UnauthorizedException extends UpliftSocialException
{
  protected $code = 401;

  protected $message = 'No valid API key provided';

  public function __construct($message = null)
  {
    parent::__construct($message);
  }
}
