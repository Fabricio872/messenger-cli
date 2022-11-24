<?php

namespace App\Service\Frames;

use App\Model\Frame;
use App\Service\FrameBuilder;
use Symfony\Component\Console\Terminal;

class ChatInputSubFrame implements SubFrameInterface
{
    private bool $active = false;
    private string $input = '';

    public function up()
    {
        //not used
    }

    public function down()
    {
        //not used
    }

    public function userInput(string $input)
    {
        if ($input == "\x7F") {
            $this->input = substr($this->input, 0, -1);
        } else {
            $this->input .= $input;
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
            ->singleLine($this->input . $this->active ? "<bg=green> </>" : "<bg=white> </>", 1, 1);

        return $builder->getFrame();
    }
}
