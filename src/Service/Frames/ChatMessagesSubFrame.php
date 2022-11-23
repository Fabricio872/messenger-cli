<?php

namespace App\Service\Frames;

use App\Model\Frame;
use App\Service\FrameBuilder;
use Symfony\Component\Console\Terminal;

class ChatMessagesSubFrame implements SubFrameInterface
{
    private int $selectedId = 0;
    private array $itemsMy = ['jou', 'sup', 'hello', 'dakto', 'dakto'];
    private array $itemsTheirs = ['jou', 'sup', 'hello', 'dakto', 'dakto'];
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
        $termianl = new Terminal();
        $frame = new Frame(round(($termianl->getWidth() / 3) * 2) + 1, $termianl->getHeight() - 3);

        $builder = new FrameBuilder($frame);
        $builder
            ->border()
            ->listItems($this->itemsMy, $this->selectedId, $this->active);

        return $builder->getFrame();
    }
}
