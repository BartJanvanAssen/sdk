<?php

namespace ReClickdAPI\Exceptions;

class InternalServerException extends ReClickdException
{
  protected $code = 502;

  protected $message = 'Internal Server Error';

  public function __construct($message = null)
  {
    $this->message = $message;
  }
}
