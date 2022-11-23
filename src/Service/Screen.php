<?php

namespace App\Service;

use App\Model\Frame;
use App\Service\Frames\ChatInputSubFrame;
use App\Service\Frames\ChatListSubFrame;
use App\Service\Frames\ChatMessagesSubFrame;
use App\Service\Frames\SubFrameInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Console\Terminal;
use Symfony\Contracts\Cache\ItemInterface;

class Screen
{
    private Frame $frame;
    private string $activeSubFrame = ChatListSubFrame::class;
    /**
     * @var ArrayCollection<string, SubFrameInterface>
     */
    private ArrayCollection $subFrames;

    public function __construct(
        ChatListSubFrame     $chatListFrame,
        ChatMessagesSubFrame $chatMessagesFrame,
        ChatInputSubFrame    $chatInputSubFrame
    )
    {
        $this->frame = Frame::buildBase();
        $this->subFrames = new ArrayCollection([
            ChatListSubFrame::class => $chatListFrame,
            ChatMessagesSubFrame::class => $chatMessagesFrame,
            ChatInputSubFrame::class => $chatInputSubFrame
        ]);
    }

    public function getBaseFrame(): Frame
    {
        $builder = new FrameBuilder($this->frame);
        $terminal = new Terminal();

        $this->selectedFrame();

        $builder->addFrame(0, 0, $this->subFrames->get(ChatListSubFrame::class)->get());
        $builder->addFrame(round($terminal->getWidth() / 3) - 1, 0, $this->subFrames->get(ChatMessagesSubFrame::class)->get());
//        $builder->addFrame(
//            round($terminal->getWidth() / 3) - 1,
//            $terminal->getHeight() - 4,
//            $this->subFrames->get(ChatInputSubFrame::class)->get()
//        );

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
    }

    public function right()
    {
        $this->subFrames->next();
        if (!$this->subFrames->key()) {
            $this->subFrames->first();
        }
        $this->activeSubFrame = $this->subFrames->key();
    }

    private function selectedFrame(): SubFrameInterface
    {
        $this->setAllFramesInactive();

        /** @var SubFrameInterface $activeSubFrame */
        $activeSubFrame = $this->subFrames->get($this->activeSubFrame);
        $activeSubFrame->setActive(true);

        return $activeSubFrame;
    }

    private function setAllFramesInactive(): void
    {
        $this->subFrames->forAll(function (string $key, SubFrameInterface $item) {
            $item->setActive(false);
        });
    }
}
