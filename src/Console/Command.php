<?php

namespace  Osoobe\Utilities\Console;

use Carbon\Carbon;
use Illuminate\Console\Command as ParentCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class Command extends ParentCommand {

    /**
     * Indicates whether the timer should be shown after the command output message.
     * 
     * @uses Carbon\Carbon::now     Start time of the command, which is used to output
     *                              how long it took to complete the command.
     *
     * @var bool
     */
    protected $timer = false;

    /**
     * Execute the console command.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface  $input
     * @param  \Symfony\Component\Console\Output\OutputInterface  $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $start_time = Carbon::now();
        $result = parent::execute($input, $output);
        if ( $this->timer ) {
            echo PHP_EOL, "Took ", $start_time->longAbsoluteDiffForHumans(), " to complete", PHP_EOL;
        }
        return $result;
    }

}


?>