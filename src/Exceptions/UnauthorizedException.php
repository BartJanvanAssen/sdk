<?php

namespace ReClickdAPI\Exceptions;

class UnauthorizedException extends \Exception
{
  protected $code = 401;

  protected $message = 'No valid API key provided';

  public function __construct($message = null)
  {
    $this->message = $message;
  }
}
