<?php

namespace Luthfi\SimpleKickAll\tasks;

use pocketmine\scheduler\Task;
use Luthfi\SimpleKickAll\Main;

class KickAllTask extends Task {

    private $plugin;

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
    }

    public function onRun(): void {
        $this->plugin->kickAllPlayers();
    }
}
