<?php

namespace UpliftSocial\SDK\Exceptions;

class BadRequestException extends UpliftSocialException
{
  protected $code = 400;

  protected $message = 'Bad Request';

  public function __construct($message = null)
  {
    parent::__construct($message);
  }
}
