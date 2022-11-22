<?php

namespace App\Service;

use App\Model\Frame;

class FrameBuilder
{
    public function __construct(
        private Frame $frame
    )
    {
    }

    private function stringCounter(string $string): int
    {
        return strlen(preg_replace('/<[\s\S]+?>/', '', $string));
    }

    public function border(): self
    {
        $lines = $this->frame->getLines();
        $lineCount = count($lines);
        foreach ($lines as $i => &$line) {
            if ($i == 0 || $i == $lineCount - 1) {
                $lines[$i] = str_repeat('-', $this->stringCounter($line));
                $lines[$i] = substr_replace($line, '+', 0, 1);
                $lines[$i] = substr_replace($line, '+', -1, 1);
            } else {
                $lines[$i] = substr_replace($line, '|', 0, 1);
                $lines[$i] = substr_replace($line, '|', -1, 1);
            }
        }
        $this->frame->setLines($lines);
        return $this;
    }

    public function rightLine(): self
    {
        $lines = $this->frame->getLines();
        foreach ($lines as $i => &$line) {
            $lines[$i] = substr_replace($line, '|', -1, 1);
        }
        $this->frame->setLines($lines);
        return $this;
    }

    public function leftLine(): self
    {
        $lines = $this->frame->getLines();
        foreach ($lines as $i => &$line) {
            $lines[$i] = substr_replace($line, '|', 0, 1);
        }
        $this->frame->setLines($lines);
        return $this;
    }

    public function topLine(): self
    {
        $lines = $this->frame->getLines();
        $lineCount = count($lines);
        foreach ($lines as $i => &$line) {
            if ($i == 0) {
                $lines[$i] = str_repeat('-', $this->stringCounter($line));
                $lines[$i] = substr_replace($line, '+', 0, 1);
                $lines[$i] = substr_replace($line, '+', -1, 1);
            }
        }
        $this->frame->setLines($lines);
        return $this;
    }

    public function bottomLine(): self
    {
        $lines = $this->frame->getLines();
        $lineCount = count($lines);
        foreach ($lines as $i => &$line) {
            if ($i == $lineCount - 1) {
                $lines[$i] = str_repeat('-', $this->stringCounter($line));
                $lines[$i] = substr_replace($line, '+', 0, 1);
                $lines[$i] = substr_replace($line, '+', -1, 1);
            }
        }
        $this->frame->setLines($lines);
        return $this;
    }

    public function textBottomMiddle(string $text): self
    {
        $lines = $this->frame->getLines();
        $lastLine = $lines[array_key_last($lines)];

        $lines[array_key_last($lines)] = substr_replace(
            $lastLine,
            $text,
            round(($this->stringCounter($lastLine) - $this->stringCounter($text)) / 2, 0),
            $this->stringCounter($text)
        );
        $this->frame->setLines($lines);
        return $this;
    }

    public function listItems(array $items, int $selectedId = null, bool $active = false): self
    {
        $lines = $this->frame->getLines();
        $lineCount = count($lines);

        foreach ($lines as $i => &$line) {
            if ($i != $lineCount - 1 && $i != 0 && array_key_exists($i - 1, $items)) {
                $lines[$i] = substr_replace($line, $items[$i - 1], 1, $this->stringCounter($items[$i - 1]));
                if ($i - 1 == $selectedId) {
                    if ($active) {
                        $lines[$i] = substr_replace($line, '<fg=black;bg=green>', 1, 0);
                        $lines[$i] = substr_replace($line, '</>', -1, 0);
                    } else {
                        $lines[$i] = substr_replace($line, '<fg=black;bg=white>', 1, 0);
                        $lines[$i] = substr_replace($line, '</>', -1, 0);
                    }
                }
            }
        }
        $this->frame->setLines($lines);
        return $this;
    }

    public function addFrame(int $x, int $y, Frame $frame): self
    {
        $topLines = $frame->getLines();
        $newLines = [];
        foreach ($topLines as $key => $line) {
            $newLines[$key + $y] = $line;
        }
        $topLines = $newLines;

        $bottomLines = $this->frame->getLines();
        foreach ($bottomLines as $key => &$line) {
            if (array_key_exists($key, $topLines)) {
                $line = substr_replace($line, $topLines[$key], $x, $this->stringCounter($topLines[$key]));
            }
        }
        $this->frame->setLines($bottomLines);
        return $this;
    }

    public function getFrame(): Frame
    {
        return $this->frame;
    }
}
