<?php

/**
 * User: Dev_Lee
 * Date: 10/16/2023 - Time: 6:00 PM
 */

namespace Devlee\Framework\Waker\Generators;

/**
 * @author  Ankain Lesly <leeleslyank@gmail.com>
 * @package  php-framework >> waker
 */

class RouteGenerator
{
  public function __construct(private string $routeName, private array $options = [])
  {
    $defaults = [
      'methods' => false,
    ];

    $this->options = array_merge($defaults, $this->options);
  }

  /**
   * Generates a route file
   * 
   * @return string
   */
  public function generate(): string
  {
    $content = (string) $this->getBody(ucfirst($this->routeName));

    $content = \str_replace('{{routeName}}', $this->routeName, $content);
    return $content;
  }

  /**
   * Generates a default class class structure
   * 
   * @return string
   */
  private function getBody(): string
  {
    $body = <<<'EOF'
<?php

use Devlee\WakerRouter\Router;

// Custom Route {{routeName}}
${{routeName}} = Router::useRoute();

${{routeName}}->get('/pageOne', "handler-one");
${{routeName}}->post('/pageTwo', "handler-two");

EOF;
    return $body;
  }
}
