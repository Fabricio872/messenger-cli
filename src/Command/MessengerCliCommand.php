<?php

namespace App\Command;

use App\Service\InputManager;
use App\Service\Screen;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:messenger-cli',
    description: 'Add a short description for your command',
)]
class MessengerCliCommand extends Command
{
    public function __construct(
        private Screen $screen
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
//            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
//            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $inputManager = new InputManager($input, $output, $this->screen);
        $inputManager->drawEdiTable();

//        $io->write("\033[2J\033[;H");
        return Command::SUCCESS;
    }
}
