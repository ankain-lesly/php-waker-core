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

class ControllerGenerator
{
  public function __construct(private string $controllerName, private array $options = [])
  {
    $defaults = [
      'methods' => false,
    ];

    $this->options = array_merge($defaults, $this->options);
  }

  /**
   * Generates a controller class
   * 
   * @return string
   */
  public function generate(): string
  {
    $generatedMethods = "";
    $content = (string) $this->getBody(ucfirst($this->controllerName));


    $OptionMethods = $this->options['methods'] ?? false;
    if ($OptionMethods) {
      $OptionMethods = explode(',', $OptionMethods);

      while ($method = array_shift($OptionMethods)) {
        $method = trim($method);

        if (!$this->validateMethodName($method)) {
          echo "\n\033[1;45m Invalid method name <$method>. Method not created. \033[0m \n";
          continue;
        }
        $method = ucfirst($method);
        $generatedMethods .= $this->getMethod($method);
        echo "\n\033[0;32m >> Method Generated: [$method]. \033[0m \n";
      }
    } else {
      $method = "Index";
      $generatedMethods .= $this->getMethod($method);
      echo "\n\033[0;32m >> Method Generated: [$method]. \033[0m \n";
    }

    $content = \str_replace('{{methods}}', $generatedMethods, $content);
    return $content;
  }

  /**
   * Generates a default class class structure
   * 
   * @return string
   */
  private function getBody(string $controllerName): string
  {
    $body = <<<EOF
<?php

namespace App\Controllers;

use Devlee\PHPRouter\Request;
use Devlee\PHPRouter\Response; 

/**
 * Generated Controller
 */
class $controllerName
{
  public function __construct()
  {
  }
  {{methods}}
}

EOF;
    return $body;
  }

  /**
   * Generate a class method if parsed
   * 
   * @return string
   */
  private function getMethod(string $methodName): string
  {
    $method = <<<'EOF'

  // {{methodName}}
  public function {{methodName}}(Request $req, Response $res)
  {
    // Set Page Title
    $res->setPageTitle('{{methodName}} Page');

    // Content here...
  }

EOF;

    $method = \str_replace("{{methodName}}", $methodName, $method);
    return $method;
  }


  /**
   * Generate a class method if parsed
   * 
   * @return string
   */
  private function validateMethodName(string $methodName): bool
  {
    if (!preg_match('/^[a-zA-Z][a-zA-Z0-9]+$/', $methodName)) {
      return false;
    }
    return true;
  }
}
