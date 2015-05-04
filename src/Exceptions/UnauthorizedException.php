<?php

namespace ReClickdAPI\Exceptions;

class UnauthorizedException extends \Exception
{
  protected $code = 401;

  protected $message = 'Unauthorized';

  public function __construct($message = null)
  {
    $this->message = $message;
  }
}
