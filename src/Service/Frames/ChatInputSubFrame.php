<?php

namespace App\Service\Frames;

use App\Model\Frame;
use App\Service\FrameBuilder;
use Symfony\Component\Console\Terminal;

class ChatInputSubFrame implements SubFrameInterface
{
    private int $selectedId = 0;
    private array $items = ['Fabricio', 'dakto', 'dakto', 'dakto', 'dakto'];
    private bool $active = false;

    public function up()
    {
        if ($this->selectedId > 0) {
            $this->selectedId -= 1;
        }
    }

    public function down()
    {
        if ($this->selectedId < count($this->items) - 1) {
            $this->selectedId += 1;
        }
    }

    public function getSelected(): int
    {
        return $this->selectedId;
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
            ->textBottomMiddle($this->active ? 'selected' : '');

        return $builder->getFrame();
    }
}
