<?php

namespace App\Model;

class Line
{
    private string $line;
    /** @var array<int, string> $specialMarks */
    private array $specialMarks = [];
    /** @var array<int, self> $subLines */
    private array $subLines = [];

    public function __toString(): string
    {
        $result = $this->getRendered();
        foreach (array_reverse($this->subLines, true) as $position => $subLine) {
            $result = self::stringMerger($result, $subLine, $position);
        }
        return $result;
    }

    public function __construct(string $line)
    {
        $this->setLine($line);
    }

    public function setLine(string $line): self
    {
        $this->line = $line;
        $this->filterSpecial();

        return $this;
    }

    public function addSubLine(self $line, int $startPosition): self
    {
        $this->subLines[$startPosition] = $line;

        return $this;
    }

    public function getRawLength(): int
    {
        return strlen($this->line);
    }

    private function getRendered(): string
    {
        $renderedString = $this->line;
        $specialMarks = array_reverse($this->specialMarks, true);
        foreach ($specialMarks as $position => $specialMark) {
            $renderedString = substr_replace($renderedString, $specialMark, $position, 0);
        }
        return $renderedString;
    }

    private function filterSpecial()
    {
        $matches = [];
        preg_match_all('/<[\s\S]+?>/', $this->line, $matches);

        foreach ($matches[0] as $match) {
            $position = strpos($this->line, $match);
            $this->specialMarks[$position] = $match;
            $start = substr($this->line, 0, $position);
            $end = substr($this->line, $position + strlen($match));

            $this->line = $start . $end;
        }
    }

    private static function stringMerger(string $string1, Line $string2, int $position): string
    {
        return substr_replace($string1, $string2, $position, $string2->getRawLength());
    }
}
