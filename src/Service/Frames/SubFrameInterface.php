<?php

namespace App\Service\Frames;

use App\Model\Frame;

interface SubFrameInterface
{
    public function up();

    public function down();

//    public function getSelected(): int;

    public function setActive(bool $active = false);

    public function get(): Frame;

    public function userInput(string $input);
}