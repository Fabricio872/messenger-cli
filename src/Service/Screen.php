<?php

namespace App\Service;

use App\Model\Frame;
use App\Service\Frames\ChatListFrame;
use App\Service\Frames\FrameInterface;

class Screen
{
    private Frame $frame;
    private string $activeFrame = ChatListFrame::class;

    public function __construct(
        private ChatListFrame $chatListFrame
    )
    {
        $this->frame = Frame::buildBase();
    }

    public function getBaseFrame(): Frame
    {
        $builder = new FrameBuilder($this->frame);

        $this->selectedFrame();
        $builder->addFrame(0, 0, $this->chatListFrame->get());

        return $this->frame;
    }

    public function up()
    {
        $this->selectedFrame()->up();
    }

    public function down()
    {
        $this->selectedFrame()->down();
    }

    public function left()
    {
        if ($this->cursor[1] < $this->cursorEnd[1] - 1) {
            $this->cursor[1]++;
        }
        $this->table();
    }

    public function right()
    {
        if ($this->cursor[1] > 0) {
            $this->cursor[1]--;
        }
        $this->table();
    }

    private function selectedFrame(): FrameInterface
    {
        switch ($this->activeFrame) {
            case ChatListFrame::class:
                $this->chatListFrame->setActive(true);
                return $this->chatListFrame;
        }
    }
}
