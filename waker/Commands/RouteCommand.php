<?php

/**
 * User: Dev_Lee
 * Date: 10/14/2023 - Time: 9:57 AM
 */

namespace Devlee\Framework\Waker\Commands;

use Devlee\WakerCLI\Commands\Command;
use Devlee\WakerCLI\Input\Input;
use Devlee\WakerCLI\Input\InputInterface;
use Devlee\WakerCLI\Output\OutputInterface;
use Devlee\Framework\Waker\Generators\RouteGenerator;

/**
 * @author  Ankain Lesly <leeleslyank@gmail.com>
 * @package  php-framework >> waker
 */

class RouteCommand extends Command
{
  public function __construct(private string $app_path, string $name = null)
  {
    parent::__construct($name);
    $this->app_path = $this->app_path . '/routes/';
  }
  protected function configure()
  {
    // $this->ignoreValidationErrors();
    $this
      ->setName('add:route')
      ->setHintText('add:route <RouteName>')
      ->setDescription('Generate a new custom route file >> PostRoutes')
      ->addArgument("RouteName", Input::ARGUMENT_REQUIRED, "The name of the route to generated.")
      ->addOption("force", true, Input::OPTION_VALUE_NONE, "Override existing route files if available")
      ->setHelp(
        <<<'EOF'
The <info>%command.name%</info> command generates a new Application Custom Route file with default content:

  <info>%command.full_name%</info>

  php waker add:route <RouteName> || PostRoutes

EOF
      );
  }

  private function validateRouteNames(string $routeName, OutputInterface $output)
  {
    $error = false;
    if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9]+$/', $routeName)) {
      $output->error(sprintf('Routes name "%s" is invalid.', $routeName));
      $error = true;
    }
    if ($error) {
      $output->writeln(">>\t Example: PostRoutes");
      exit;
    }
  }
  public function execute(InputInterface $input, OutputInterface $output): int
  {
    $routeName = ucfirst($input->getArgument('RouteName'));
    $this->validateRouteNames($routeName, $output);

    $output->newline();
    $output->writelnWarning(">> Generating route file.. ");
    $output->writelnInfo($routeName);
    $output->newline();

    if (!is_dir($this->app_path)) {
      $output->error('Error Accessing routes Directory. get --help');
      exit;
    }

    $filename = $this->app_path . $routeName . '.php';
    $override = $input->getOption('force');

    if (file_exists($filename) && $override === false) {
      $output->error('>> Route already exists ');
      $output->writeln('   use --force OR -f to override route file if necessary');
      exit;
    }

    $options = array(
      'methods' => $input->getOption('methods'),
    );

    $generator = new RouteGenerator($routeName, $options);
    $fileContent = $generator->generate();

    file_put_contents($filename, $fileContent) or $output->error('Error writing file');

    if ($override) {
      $output->warning("Route updated successfully.");
    } else {
      $output->success("Route generated successfully.");
    }

    return 1;
  }
  private function getContent(InputInterface $input, OutputInterface $output): string
  {
    $content = 'Test content override';


    return $content;
  }
}
