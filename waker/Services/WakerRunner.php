<?php

/**
 * User: Dev_Lee
 * Date: 10/16/2023 - Time: 6:00 PM
 */

namespace Devlee\Framework\Waker\Services;

use Devlee\Framework\Waker\Commands\ControllerCommand;
use Devlee\Framework\Waker\Commands\ModelCommand;
use Devlee\Framework\Waker\Commands\RouteCommand;
use Devlee\WakerCLI\WakerCLI;

/**
 * @author  Ankain Lesly <leeleslyank@gmail.com>
 * @package  php-framework >> waker
 */

class WakerRunner
{
  private WakerCLI $waker;

  public function __construct(public string $path)
  {
    $this->waker = new WakerCLI("WakerCLI", "1.5.0 BETA");
    $this->waker->add(new ControllerCommand(app_path: $this->path));
    $this->waker->add(new ModelCommand(app_path: $this->path));
    $this->waker->add(new RouteCommand(app_path: $this->path));
  }

  /**
   * Runs the WakerCLI Application
   * 
   * @return null
   */
  public function run()
  {
    $this->waker->run();
  }

  /**
   * Returns an instance of the WakerCLI Object
   * 
   * @return null
   */
  public function getWakerCLI(): WakerCLI
  {
    return $this->waker;
  }
}
