<?php

/**
 * User: Dev_Lee
 * Date: 10/11/2023 - Time: 4:12 PM 
 * Updated: 10/18/2023 - Time 4:40 AM
 */

namespace Devlee\Framework\Waker\Services;

/**
 * @author  Ankain Lesly <leeleslyank@gmail.com>
 * @package  php-mvc-framework
 */

interface BaseAppInterface
{
  public function boot();
  public function run();
}
