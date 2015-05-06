<?php

namespace ReClickdAPI\Exceptions;

class NotFoundException extends ReClickdException
{
  protected $code = 404;

  protected $message = 'The requested resource could not be found';

  public function __construct($message = null)
  {
    $this->message = $message;
  }
}
