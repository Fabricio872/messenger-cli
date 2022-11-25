<?php

namespace App\Service\Frames;

use App\Model\Frame;
use App\Service\FrameBuilder;
use Symfony\Component\Console\Terminal;

class ChatInputSubFrame implements SubFrameInterface
{
    private bool $active = false;
    private string $input = '';
    private int $cursorPosition = 0;

    public function up()
    {
        if ($this->cursorPosition > 0) {
            $this->cursorPosition--;
        }
    }

    public function down()
    {
        if ($this->cursorPosition < strlen($this->input)) {
            $this->cursorPosition++;
        }
    }

    public function userInput(string $input)
    {
        if ($input == "\x7F") {
            $this->backspace();
        } elseif ($input == "\n") {
            $this->enter();
        } else {
            $this->input .= $input;
            $this->cursorPosition++;
        }
    }

    public function getSelected(): int
    {
        return 0;
    }

    public function setActive(bool $active = false)
    {
        $this->active = $active;
    }

    public function get(): Frame
    {
        $terminal = new Terminal();
        $frame = new Frame(round(($terminal->getWidth() / 3) * 2) + 1, 3);

        $builder = new FrameBuilder($frame);
        $builder
            ->border()
            ->singleLine(sprintf($this->getStringWithCursor(), $this->input, ' '), 1, 1);

        return $builder->getFrame();
    }

    private function getStringWithCursor(): string
    {
        $template = $this->active ? "%s<bg=green>%s</>%s" : "%s<bg=white>%s</>%s";

        $pre = substr($this->input, 0, $this->cursorPosition);
        $in = substr($this->input, $this->cursorPosition, 1);
        $after = substr($this->input, $this->cursorPosition + 1);

        return sprintf($template, $pre, $in == '' ? ' ' : $in, $after);
    }

    private function backspace()
    {
        $pre = substr($this->input, 0, $this->cursorPosition - 1);
        $after = substr($this->input, $this->cursorPosition);
        $this->input = $pre . $after;
        if ($this->cursorPosition > 0) {
            $this->cursorPosition--;
        }
    }

    private function enter()
    {
        $this->cursorPosition = 0;
        $this->input = '';
    }
}
