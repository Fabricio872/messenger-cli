<?php

namespace App\Model;

use Symfony\Component\Console\Terminal;

class Frame
{
    /** @var array<int, Line> $lines */
    private array $lines;

    public static function buildBase(): self
    {
        $terminal = new Terminal();
        return new self($terminal->getWidth(), $terminal->getHeight() - 1);
    }

    /**
     * @return array|Line[]
     */
    public function getLines(): array
    {
        return $this->lines;
    }

    public function setLines(array $lines): Frame
    {
        $this->lines = array_map(function (string $line) {
            return new Line($line);
        }, $lines);
        return $this;
    }

    public function __construct(int $width, int $height)
    {
        for ($i = 0; $i < $height; $i++) {
            $this->lines[] = new Line(str_repeat(' ', $width));
        }
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
