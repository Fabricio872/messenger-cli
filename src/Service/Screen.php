<?php

namespace App\Service;

use App\Model\Frame;
use App\Service\Frames\ChatInputSubFrame;
use App\Service\Frames\ChatListSubFrame;
use App\Service\Frames\ChatMessagesSubFrame;
use App\Service\Frames\SubFrameInterface;
use Symfony\Component\Console\Terminal;

class Screen
{
    private Frame $frame;
    private string $activeSubFrame = ChatListSubFrame::class;
    /**
     * @var array<string, SubFrameInterface>
     */
    private array $subFrames;

    public function __construct(
        ChatListSubFrame     $chatListFrame,
        ChatMessagesSubFrame $chatMessagesFrame,
        ChatInputSubFrame    $chatInputSubFrame
    )
    {
        $this->frame = Frame::buildBase();
        $this->subFrames = [
            ChatListSubFrame::class => $chatListFrame,
            ChatMessagesSubFrame::class => $chatMessagesFrame,
            ChatInputSubFrame::class => $chatInputSubFrame
        ];
    }

    public function getBaseFrame(): Frame
    {
        $builder = new FrameBuilder($this->frame);
        $terminal = new Terminal();

        $this->selectedFrame();

        $builder->addFrame(0, 0, $this->subFrames[ChatListSubFrame::class]->get());
        $builder->addFrame(
            round($terminal->getWidth() / 3) - 1,
            0,
            $this->subFrames[ChatMessagesSubFrame::class]->get()
        );
        $builder->addFrame(
            round($terminal->getWidth() / 3) - 1,
            $terminal->getHeight() - 4,
            $this->subFrames[ChatInputSubFrame::class]->get()
        );

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
        $position = array_search($this->activeSubFrame, array_keys($this->subFrames), true);
        $values = array_values($this->subFrames);

        if ($position > 0) {
            $previous = $values[$position - 1];
        } else {
            $previous = $values[array_key_last($values)];
        }

        $this->activeSubFrame = get_class($previous);
        $this->selectedFrame();
    }

    public function right()
    {
        $position = array_search($this->activeSubFrame, array_keys($this->subFrames), true);
        $values = array_values($this->subFrames);

        if ($position + 1 < count($this->subFrames)) {
            $next = $values[$position + 1];
        } else {
            $next = $values[array_key_first($values)];
        }

        $this->activeSubFrame = get_class($next);
        $this->selectedFrame();
    }

    private function selectedFrame(): SubFrameInterface
    {
        $this->setAllFramesInactive();

        /** @var SubFrameInterface $activeSubFrame */
        $activeSubFrame = $this->subFrames[$this->activeSubFrame];
        $activeSubFrame->setActive(true);

        return $activeSubFrame;
    }

    private function setAllFramesInactive(): void
    {
        array_map(function (SubFrameInterface $item) {
            $item->setActive(false);
        }, $this->subFrames);
    }
}
