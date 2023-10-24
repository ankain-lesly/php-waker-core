<?php

/**
 * User: Dev_Lee
 * Date: 10/14/2023 - Time: 9:57 AM
 */

namespace Devlee\Framework\Waker\Commands;

use Devlee\CliTool\Commands\Command;
use Devlee\CliTool\Input\Input;
use Devlee\CliTool\Input\InputInterface;
use Devlee\CliTool\Output\OutputInterface;
use Devlee\Framework\Waker\Generators\ModelGenerator;

/**
 * @author  Ankain Lesly <leeleslyank@gmail.com>
 * @package  php-framework >> waker
 */

class ModelCommand extends Command
{
  public function __construct(private string $app_path, string $name = null)
  {
    parent::__construct($name);
    $this->app_path = $this->app_path . "/app/models/";
  }
  protected function configure()
  {
    // $this->ignoreValidationErrors();
    $this
      ->setName('make:model')
      ->setHintText('make:model <ModelName>')
      ->setDescription('Generates a new App Model >> Post')
      ->addArgument("ModelName", Input::ARGUMENT_REQUIRED, "The name of the model to be generated.")
      ->addOption("tablename", true, Input::OPTION_VALUE_REQUIRED, "Name of Table in Database")
      ->addOption("force", true, Input::OPTION_VALUE_NONE, "Override existing model if available")
      ->addOption("properties", true, Input::OPTION_VALUE_REQUIRED, "Database properties to use in model. separated by comma ,")
      ->addOption("validate", false, Input::OPTION_VALUE_NONE, "Implement a data validation schema object for the given model,")
      ->setHelp(
        <<<'EOF'
The <info>%command.name%</info> command generates a new Application Model file with default content:

  <info>%command.full_name%</info>
  using folder structure app/models/Post.

  php waker make:model <ModelName> || Post

EOF
      );
  }

  private function validateModelName(string $modelName, OutputInterface $output)
  {
    $error = false;
    if (strlen($modelName) < 3) {
      $output->error('Model name less than 3 Characters.');
      $error = true;
    } elseif (!preg_match('/^[a-zA-Z_][a-zA-Z0-9]+$/', $modelName)) {
      $output->error(sprintf('Model name "%s" is invalid.', $modelName));
      $error = true;
    }
    if ($error) {
      $output->writeln(">>\t Example: Post");
      exit;
    }
  }
  public function execute(InputInterface $input, OutputInterface $output): int
  {
    $ModelName = ucfirst($input->getArgument('ModelName'));
    $this->validateModelName($ModelName, $output);
    $ModelName = str_replace("model", '', $ModelName);
    $ModelName = str_replace("Model", '', $ModelName);

    $output->newline();
    $output->writelnWarning(">> Generating App Model.. ");
    $output->writelnInfo($ModelName);
    $output->newline();

    if (!is_dir($this->app_path)) {
      $output->error('Error Accessing App Model Directory. get --help');
      exit;
    }

    $filename = $this->app_path . $ModelName . '.php';
    $override = $input->getOption('force');

    if (file_exists($filename) && $override === false) {
      $output->error('>> Model already exists ');
      $output->writeln('   use --force OR -f to override model if necessary');
      exit;
    }

    $options = array(
      'tablename' => $input->getOption('tablename'),
      'properties' => $input->getOption('properties'),
      'validate' => $input->getOption('validate'),
    );

    $generator = new ModelGenerator($ModelName, $options);
    $fileContent = $generator->generate();

    file_put_contents($filename, $fileContent) or $output->error('Error writing file');

    if ($override) {
      $output->warning("Model updated successfully.");
    } else {
      $output->success("Model generated successfully.");
    }

    return 1;
  }
  private function getContent(InputInterface $input, OutputInterface $output): string
  {
    $content = 'Test content override';


    return $content;
  }
}
