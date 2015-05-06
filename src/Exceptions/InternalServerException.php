<?php

namespace ReClickdAPI\Exceptions;

class InternalServerException extends \Exception
{
  protected $code = 502;

  protected $message = 'You must supply a valid access token to use this resource';

  public function __construct($message = null)
  {
    $this->message = $message;
  }
}
