<?php

/**
 * User: Dev_Lee
 * Date: 10/11/2023 - Time: 4:12 PM
 * Updated: 10/18/2023 - Time 4:40 AM
 */

namespace Devlee\Framework\Waker;

use Devlee\ErrorTree\ErrorTree;
use Devlee\Framework\Waker\Services\BaseAppInterface;
use Devlee\Framework\Waker\Services\TwigEngine;
use Devlee\Framework\Waker\Services\WakerRunner;
use Devlee\WakerCLI\WakerCLI;
use Devlee\WakerRouter\Router;
use Dotenv\Dotenv;
use Twig\Extra\Intl\IntlExtension;
use Twig\Loader\FilesystemLoader;

/**
 * @author  Ankain Lesly <leeleslyank@gmail.com>
 * @package  php-mvc-framework 
 */

class Application implements BaseAppInterface
{
  /** 
   *  Twig Environment and Template Engin
   * 
   * @property TwigEngine $twig
   */
  public TwigEngine $twig;

  /** 
   * An instance of the Application Class
   * 
   * @property TwigEngine $twig 
   */
  public static Application $app;

  /** 
   * Container WakerCLI Runner for the application
   * 
   * @property TwigEngine $twig 
   */
  private WakerRunner $runner;
  private bool $isWakerRunnerInitialized = false;

  /** 
   * An instantiated property of the Main App Router
   * 
   * @property Router $router 
   */
  public function __construct(private Router $router, public string $root_path)
  {
    self::$app = $this;
    $this->boot();
  }

  /**
   * Setup Application dependencies
   * 
   * @method boot 
   */
  public function boot(array $options = [])
  {
    $dotenv = Dotenv::createImmutable($this->root_path);
    $dotenv->load();

    $this->twig = new TwigEngine(
      new FilesystemLoader(VIEWS_PATH),
      [
        'cache' => STORAGE_PATH . '/cache',
        'auto_reload' => true,
      ]
    );
    // $twig->addExtension(new IntlExtension());

    $this->router->setTemplateEngine($this->twig);
  }

  /**
   * Starts App Router Service
   * 
   * @method run 
   */
  public function run()
  {
    try {
      $this->router->resolve();
    } catch (\Throwable $e) {
      ErrorTree::RenderError($e);
      // HandleExceptions::DisplayExceptionErrors($e);
    }
  }

  /**
   * Runs WakerCLI Application
   * 
   * @return null
   */
  public function runWaker()
  {
    $this->runner = new WakerRunner($this->root_path);
    $this->runner->run();
    $this->isWakerRunnerInitialized = true;
  }

  /**
   * Returns an instance of the WakerCLI Object
   * 
   * @return WakerCLI
   */
  public function getWaker(): ?WakerCLI
  {
    if (!$this->isWakerRunnerInitialized) {
      die("WackerCLI is not initialize: RunWaker first");
    }
    return $this->runner?->getWakerCLI();
  }
}
