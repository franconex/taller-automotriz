<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Env;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Process\Process;

use function Illuminate\Support\php_binary;

#[AsCommand(name: 'serve')]
class ServeCommand extends Command
{
    protected $name = 'serve';

    protected $description = 'Serve the application on the PHP development server';

    public function handle()
    {
        $host = $this->input->getOption('host') ?? Env::get('SERVER_HOST', '127.0.0.1');
        $port = $this->input->getOption('port') ?? Env::get('SERVER_PORT', 8000);

        $server = file_exists($this->laravel->basePath('server.php'))
            ? $this->laravel->basePath('server.php')
            : $this->laravel->basePath('vendor/laravel/framework/src/Illuminate/Foundation/resources/server.php');

        $this->components->info("Server running on [http://{$host}:{$port}].");
        $this->comment('  <fg=yellow;options=bold>Press Ctrl+C to stop the server</>');
        $this->newLine();

        $process = new Process([
            php_binary(),
            '-S',
            "{$host}:{$port}",
            $server,
        ], public_path());

        $process->setTimeout(null);

        $this->trap(fn () => [SIGTERM, SIGINT, SIGHUP, SIGUSR1, SIGUSR2, SIGQUIT], function ($signal) use ($process) {
            if ($process->isRunning()) {
                $process->stop(10, $signal);
            }
            exit;
        });

        $process->run(function ($type, $buffer) {
            //
        });

        return $process->getExitCode();
    }

    protected function getOptions()
    {
        return [
            ['host', null, InputOption::VALUE_OPTIONAL, 'The host address to serve the application on', Env::get('SERVER_HOST', '127.0.0.1')],
            ['port', null, InputOption::VALUE_OPTIONAL, 'The port to serve the application on', Env::get('SERVER_PORT')],
        ];
    }
}
