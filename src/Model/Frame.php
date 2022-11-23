<?php

namespace App\Model;

use Symfony\Component\Console\Terminal;

class Frame
{
    private array $lines;

    public static function buildBase(): self
    {
        $terminal = new Terminal();
        return new self($terminal->getWidth(), $terminal->getHeight()-1);
    }

    public function getLines(): array
    {
        return $this->lines;
    }

    public function setLines(array $lines): Frame
    {
        $this->lines = $lines;
        return $this;
    }

    public function __construct(int $width, int $height)
    {
        $chars = str_repeat(' ', $width);
        $this->lines = array_pad([], $height, $chars);
    }

    public function render(): string
    {
        $output = '';
        foreach ($this->lines as $line) {
            $output .= $line;
            $output .= "\n";
        }

        return $output;
    }
}
