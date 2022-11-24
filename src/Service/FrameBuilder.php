<?php

namespace App\Service;

use App\Model\Frame;
use App\Model\Line;

class FrameBuilder
{
    public function __construct(
        private Frame $frame
    )
    {
    }

    public function border(): self
    {
        $lines = $this->frame->getLines();
        $lineCount = count($lines);
        foreach ($lines as $i => &$line) {
            if ($i == 0 || $i == $lineCount - 1) {
                $line->setLine(str_repeat('-', strlen($line)));
                $line->setLine(substr_replace($line, '+', 0, 1));
                $line->setLine(substr_replace($line, '+', -1, 1));
            } else {
                $line->setLine(substr_replace($line, '|', 0, 1));
                $line->setLine(substr_replace($line, '|', -1, 1));
            }
        }
        return $this;
    }

    public function rightLine(): self
    {
        $lines = $this->frame->getLines();
        foreach ($lines as $i => &$line) {
            $line->setLine(substr_replace($line, '|', -1, 1));
        }
        return $this;
    }

    public function leftLine(): self
    {
        $lines = $this->frame->getLines();
        foreach ($lines as $i => &$line) {
            $line->setLine(substr_replace($line, '|', 0, 1));
        }
        return $this;
    }

    public function topLine(): self
    {
        $lines = $this->frame->getLines();
        foreach ($lines as $i => &$line) {
            if ($i == 0) {
                $line->setLine(str_repeat('-', strlen($line)));
                $line->setLine(substr_replace($line, '+', 0, 1));
                $line->setLine(substr_replace($line, '+', -1, 1));
            }
        }
        return $this;
    }

    public function bottomLine(): self
    {
        $lines = $this->frame->getLines();
        $lineCount = count($lines);
        foreach ($lines as $i => &$line) {
            if ($i == $lineCount - 1) {
                $line->setLine(str_repeat('-', strlen($line)));
                $line->setLine(substr_replace($line, '+', 0, 1));
                $line->setLine(substr_replace($line, '+', -1, 1));
            }
        }
        return $this;
    }

    public function textBottomMiddle(string $text): self
    {
        $lines = $this->frame->getLines();
        $lastLine = $lines[array_key_last($lines)];

        $lines[array_key_last($lines)]->setLine(substr_replace(
            $lastLine,
            $text,
            round((strlen($lastLine) - strlen($text)) / 2, 0),
            strlen($text)
        ));
        return $this;
    }

    public function listItems(array $items, int $selectedId = null, bool $active = false): self
    {
        $lines = $this->frame->getLines();
        $lineCount = count($lines);

        foreach ($lines as $i => &$line) {
            if ($i != $lineCount - 1 && $i != 0 && array_key_exists($i - 1, $items)) {
                $line->setLine(substr_replace($line, $items[$i - 1], 1, strlen($items[$i - 1])));
                if ($i - 1 == $selectedId) {
                    if ($active) {
                        $line = $line->setLine(substr_replace($line, '<fg=black;bg=green>', 1, 0));
                        $line = $line->setLine(substr_replace($line, '</>', -1, 0));
                    } else {
                        $line = $line->setLine(substr_replace($line, '<fg=black;bg=white>', 1, 0));
                        $line = $line->setLine(substr_replace($line, '</>', -1, 0));
                    }
                }
            }
        }
        return $this;
    }

    public function addFrame(int $x, int $y, Frame $frame): self
    {
        $topLines = $frame->getLines();
        $newLines = [];
        foreach ($topLines as $key => $line) {
            $newLines[$key + $y] = $line;
        }
        /** @var array<int, Line> $topLines */
        $topLines = $newLines;

        $bottomLines = $this->frame->getLines();
        foreach ($bottomLines as $key => &$line) {
            if (array_key_exists($key, $topLines)) {
                $line->addSubLine($topLines[$key], $x);
            }
        }
        return $this;
    }

    public function getFrame(): Frame
    {
        return $this->frame;
    }
}
