<?php

namespace ReClickdAPI\Exceptions;

class BadRequestException extends ReClickdException
{
  protected $code = 400;

  protected $message = 'Bad Request';

  public function __construct($message = null)
  {
    $this->message = $message;
  }
}
