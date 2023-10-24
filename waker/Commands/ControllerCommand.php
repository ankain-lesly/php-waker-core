<?php

/**
 * User: Dev_Lee
 * Date: 10/14/2023 - Time: 9:57 AM
 */

namespace Devlee\Framework\Waker\Commands;

use Devlee\Framework\Waker\Generators\ControllerGenerator;
use Devlee\WakerCLI\Commands\Command;
use Devlee\WakerCLI\Input\Input;
use Devlee\WakerCLI\Input\InputInterface;
use Devlee\WakerCLI\Output\OutputInterface;

/**
 * @author  Ankain Lesly <leeleslyank@gmail.com>
 * @package  php-framework >> waker
 */

class ControllerCommand extends Command
{
  public function __construct(private string $app_path, string $name = null)
  {
    parent::__construct($name);
    $this->app_path = $this->app_path . '/app/controllers/';
  }
  protected function configure()
  {
    // $this->ignoreValidationErrors();
    $this
      ->setName('make:controller')
      ->setHintText('make:controller <ControllerName>')
      ->setDescription('Generate a new App Controller >> PostController')
      ->addArgument("ControllerName", Input::ARGUMENT_REQUIRED, "The name of the controller to be generated.")
      ->addOption("force", true, Input::OPTION_VALUE_NONE, "Override existing controller if available")
      ->addOption("methods", true, Input::OPTION_VALUE_REQUIRED, "Generate a class methods insight controller")
      ->setHelp(
        <<<'EOF'
The <info>%command.name%</info> command generates a new Application Controller file with default content:

  <info>%command.full_name%</info>
  using folder structure app/controllers/PostController.

  php waker make:controller <ControllerName> || PostController

EOF
      );
  }

  private function validateControllerName(string $controllerName, OutputInterface $output)
  {
    $error = false;
    if (!str_contains(strtolower($controllerName), 'controller')) {
      $output->error(sprintf('Controller name "%s" is invalid.', $controllerName));
      $error = true;
    } elseif (strlen($controllerName) < 12) {
      $output->error('Controller name less than 12 Characters.');
      $error = true;
    } elseif (!preg_match('/^[a-zA-Z_][a-zA-Z0-9]+$/', $controllerName)) {
      $output->error(sprintf('Controller name "%s" is invalid.', $controllerName));
      $error = true;
    }
    if ($error) {
      $output->writeln(">>\t Example: TestController");
      exit;
    }
  }
  public function execute(InputInterface $input, OutputInterface $output): int
  {
    $controllerName = ucfirst($input->getArgument('ControllerName'));
    $this->validateControllerName($controllerName, $output);

    $output->newline();
    $output->writelnWarning(">> Generating controller..");
    $output->writelnInfo($controllerName);
    $output->newline();

    if (!is_dir($this->app_path)) {
      $output->error('Error Accessing Controllers Directory. get --help');
      exit;
    }

    $filename = $this->app_path . $controllerName . '.php';
    $override = $input->getOption('force');

    if (file_exists($filename) && $override === false) {
      $output->error('>> Controller already exists ');
      $output->writeln('   use --force OR -f to override controller if necessary');
      exit;
    }

    $options = array(
      'methods' => $input->getOption('methods'),
    );

    $generator = new ControllerGenerator($controllerName, $options);
    $fileContent = $generator->generate();

    file_put_contents($filename, $fileContent) or $output->error('Error writing file');

    if ($override) {
      $output->warning("Controller updated successfully.");
    } else {
      $output->success("Controller generated successfully.");
    }

    return 1;
  }
  private function getContent(InputInterface $input, OutputInterface $output): string
  {
    $content = 'Test content override';


    return $content;
  }
}
