<?php

namespace App\Service\Frames;

use App\Model\Frame;
use App\Service\FrameBuilder;
use Symfony\Component\Console\Terminal;

class ChatListSubFrame implements SubFrameInterface
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

    public function userInput(string $input)
    {
        //not used
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
        $termianl = new Terminal();
        $frame = new Frame(round($termianl->getWidth() / 3), $termianl->getHeight() - 1);

        $builder = new FrameBuilder($frame);
        $builder
            ->border()
            ->listItems($this->items, $this->selectedId, $this->active)
            ->textBottomMiddle(sprintf('%s/%s', $this->selectedId + 1, count($this->items)));

        return $builder->getFrame();
    }
}
