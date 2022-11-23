<?php

namespace App\Service;

use PhpSchool\Terminal\IO\BufferedOutput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\StreamableInputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InputManager
{
    private int $linesToDelete = 0;

    public function __construct(
        private InputInterface  $input,
        private OutputInterface $output,
        private Screen          $screen
    )
    {
    }

    public function start(): void
    {
        $this->stream = $this->getInputStream();

        $this->startInteractiveMode();

        $this->displayFrame();

        $this->screen->right();
        $this->displayFrame();

        while (!feof($this->stream) && ($char = fread($this->stream, 1)) != "\n") {
            if ("q" === $char) {
                break;
            } elseif ("\033" === $char) {
                $this->tryCellNavigation($char);
            }
            $this->displayFrame();
        }

        $this->stopInteractiveMode();
    }

    private function displayFrame()
    {
        $this->output->write(sprintf("\033[%dA", $this->linesToDelete));

        $frame = $this->screen->getBaseFrame();
        $this->linesToDelete = count($frame->getLines());
        $this->output->write($frame->render());
    }

    private function tryCellNavigation($char): void
    {
        // Did we read an escape sequence?
        $char .= fread($this->stream, 2);
        if (empty($char[2]) || !in_array($char[2], ['A', 'B', 'C', 'D'])) {
            if (empty($char[2]) || $char[2] == "3") {
                $this->stopInteractiveMode();
                $this->deleteUser();
                $this->startInteractiveMode();
                $this->table();
            }

            // Input stream was not an arrow key.
            return;
        }

        switch ($char[2]) {
            case 'A': // go up!
                $this->screen->up();
                break;
            case 'B': // go down!
                $this->screen->down();
                break;
            case 'C': // go left!
                $this->screen->left();
                break;
            case 'D': // go right!
                $this->screen->right();
                break;
        }
    }

    private function startInteractiveMode(): void
    {
        $this->sttyMode = shell_exec('stty -g');

        // Disable icanon (so we can fread each keypress) and echo (we'll do echoing here instead)
        shell_exec('stty -icanon -echo');
    }

    private function stopInteractiveMode(): void
    {
        shell_exec(sprintf('stty %s', $this->sttyMode));
    }

    protected function getInputStream()
    {
        if (!$this->input instanceof StreamableInputInterface) {
            throw new \Exception('Streamable interface not found');
        }

        if (empty($this->inputStream)) {
            $this->inputStream = $this->input->getStream() ?: STDIN;
        }

        return $this->inputStream;
    }
}
