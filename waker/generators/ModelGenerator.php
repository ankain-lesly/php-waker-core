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

class ModelGenerator
{
  public function __construct(private string $modelName, private array $options = [])
  {
    $defaults = [
      'tablename' => false,
      'properties' => [],
      'validate' => false,
    ];

    if ($this->options['properties'] ?? false) {
      $this->options['properties'] = explode(',', $this->options['properties']);
    }
    $this->options = array_merge($defaults, $this->options);
  }

  /**
   * Generates a Model class
   * 
   * @return string
   */
  public function generate(): string
  {
    $properties = "";
    $tablename = "";
    $schema_namespace = "";
    $validation_schema = "";

    $content = (string) $this->getBody($this->modelName);

    $OptionTablename = $this->options['tablename'] ?? false;
    $OptionProperties = $this->options['properties'] ?? false;

    // Generating Tablename Method
    if ($OptionTablename) {
      if (!$this->validateDBProperty($OptionTablename)) {
        echo "\n\033[1;45m Invalid Tablename <$OptionTablename>. \033[0m \n";
        exit;
      }
      $tablename = $this->getTablename($OptionTablename);
    }

    // Generating Model Properties
    if ($OptionProperties) {
      while ($property = array_shift($OptionProperties)) {
        $property = trim($property);

        if (!$this->validateDBProperty($property)) {
          echo "\n\033[1;45m Invalid property name <$property>. property not created. \033[0m \n";
          continue;
        }
        $properties .= "  protected string $" . $property . " = '';\n";
        echo "\n\033[0;32m >> property Generated: [$property]. \033[0m \n";
      }
    }

    // Generating Validation Schema
    if ($this->options['validate'] ?? false) {
      $schema_namespace = "use Devlee\PHPMVCCore\Services\ObjectSchema;\n";
      $validation_schema = $this->generateValidationSchema($this->options['properties'] ?? []);
    }

    $content = \str_replace('{{tablename}}', $tablename, $content);
    $content = \str_replace('{{properties}}', $properties, $content);
    $content = \str_replace('{{schema_namespace}}', $schema_namespace, $content);
    $content = \str_replace('{{validation_schema}}', $validation_schema, $content);
    return $content;
  }

  /**
   * Generates a default class class structure
   * 
   * @return string
   */
  private function getBody(string $modelName): string
  {
    $body = <<<'EOF'
<?php

namespace App\Models;

use Devlee\PHPMVCCore\DB\Model;
{{schema_namespace}}

/**
 * Generated Model
 */
class {{model}} extends Model
{
  // Database Properties 
  {{properties}}
  
  // Settings
  public string $order_by = 'id';     // Database property
  public string $direction = 'DESC';  // DESC|ASC
  
  {{tablename}}
  
  {{validation_schema}}
}
EOF;
    $body = \str_replace("{{model}}", $modelName, $body);
    return $body;
  }

  /**
   * Generate a class method if parsed
   * 
   * @return string
   */
  private function getTablename(string $tablename): string
  {
    $method = <<<'EOF'

  // Set Tablename
  public static function tableName(): string
  {
    return '{{tablename}}';
  }
EOF;
    $method = \str_replace("{{tablename}}", $tablename, $method);
    return $method;
  }


  /**
   * Generate a class method if parsed
   * 
   * @return string
   */
  private function validateDBProperty(string $methodName): bool
  {
    if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]+$/', $methodName)) {
      return false;
    }
    return true;
  }

  /**
   * Generate a data validation schema
   * 
   * @return string
   */
  private function generateValidationSchema(array $properties): string
  {
    $validateMethod = <<<'EOL'
  // Initializing Validation Schema
  public function validate(array $data)
  {
    $schema = new ObjectSchema($this);
    $schema->setValidationRules($this->rules());

    return $schema->validate($data);
  }


EOL;

    $validateRules = <<<'EOL'
  // Model Validation Rules
  public function rules(): array
  {
    return [ {{rules}} 
    ];
  }
EOL;
    $rules = "";
    foreach ($properties as $property) {
      $rules .= "\n \t    '$property' => 'required',";
    }

    $validateRules = str_replace("{{rules}}", $rules, $validateRules);

    return $validateMethod . $validateRules;
  }
}
