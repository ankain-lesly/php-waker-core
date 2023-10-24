<?php

/**
 * User: Dev_Lee
 * Date: 10/20/2023 - Time: 10:12 PM
 */


namespace Devlee\Framework\Waker\Services\Middlewares;

/**
 * @author  Ankain Lesly <leeleslyank@gmail.com>
 * @package  Devlee\PHPMVCCore\BaseMiddleware
 */

abstract class BaseMiddleware
{
  public function __construct()
  {
  }

  // methods to manage activity

  // middleware concepts
  abstract public function caseName(): string;
}
