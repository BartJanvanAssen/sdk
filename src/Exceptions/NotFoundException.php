<?php

namespace UpliftSocial\SDK\Exceptions;

class NotFoundException extends UpliftSocialException
{
  protected $code = 404;

  protected $message = 'The requested resource could not be found';

  public function __construct($message = null)
  {
    parent::__construct($message);
  }
}
