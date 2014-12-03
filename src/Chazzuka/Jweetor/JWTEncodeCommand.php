<?php namespace Chazzuka\Jweetor;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

class JWTEncodeCommand extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'jweetor:encode';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create authorization token for an audience';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $app = $this->getLaravel();

        $this->line("generating jwt token");



        $token = $app['jwt']->encode($this->option('aud'), $this->option('sub'), $this->option('secret'), $this->option('kid'));

        $this->info($token);
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['aud', null, InputOption::VALUE_REQUIRED, 'Audience'],
            ['sub', null, InputOption::VALUE_REQUIRED, 'Subject'],
            ['secret', 'sec', InputOption::VALUE_REQUIRED, 'Secret Key'],
            ['kid', null, InputOption::VALUE_OPTIONAL, 'Secret Key Identifier'],
        ];
    }

}
