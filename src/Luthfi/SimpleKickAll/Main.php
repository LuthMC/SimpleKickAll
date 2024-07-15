<?php

# Github: https://github.com/LuthMC

namespace Luthfi\SimpleKickAll;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;
use Luthfi\SimpleKickAll\tasks\KickAllTask;

class Main extends PluginBase {

    public function onEnable(): void {
        $this->saveDefaultConfig();
        $this->getLogger()->info(TextFormat::GREEN . "KickAll Enabled");
    }

    public function onDisable(): void {
        $this->getLogger()->info(TextFormat::RED . "KickAll Disabled");
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {
        switch ($command->getName()) {
            case "kickall":
                $this->kickAllPlayers();
                $sender->sendMessage(TextFormat::GREEN . "All players have been kicked from the server.");
                return true;
            case "kickalltimer":
                if (isset($args[0])) {
                    $time = $this->parseTime($args[0]);
                    if ($time > 0) {
                        $this->getScheduler()->scheduleDelayedTask(new KickAllTask($this), $time * 20);
                        $sender->sendMessage($this->translateColors(str_replace("{time}", $args[0], $this->getConfig()->get("timer-message", "All players will be kicked in {time}."))));
                        return true;
                    } else {
                        $sender->sendMessage(TextFormat::RED . "Invalid time format. Use s for seconds or m for minutes.");
                    }
                } else {
                    $sender->sendMessage(TextFormat::RED . "Usage: /kickalltimer <time>");
                }
                return true;
            default:
                return false;
        }
    }

    public function kickAllPlayers(): void {
        $kickMessage = $this->translateColors($this->getConfig()->get("kick-message", "You have been kicked from the server."));
        foreach ($this->getServer()->getOnlinePlayers() as $player) {
            $player->kick($kickMessage);
        }
    }

    private function parseTime(string $timeStr): int {
        $lastChar = strtolower($timeStr[-1]);
        $timeValue = substr($timeStr, 0, -1);

        if (!is_numeric($timeValue)) {
            return 0;
        }

        $timeValue = (int) $timeValue;

        switch ($lastChar) {
            case 's':
                return $timeValue;
            case 'm':
                return $timeValue * 60;
            default:
                return 0;
        }
    }

    private function translateColors(string $message): string {
        return str_replace(
            ["§0", "§1", "§2", "§3", "§4", "§5", "§6", "§7", "§8", "§9", "§a", "§b", "§c", "§d", "§e", "§f", "§k", "§l", "§m", "§n", "§o", "§r"],
            [TextFormat::BLACK, TextFormat::DARK_BLUE, TextFormat::DARK_GREEN, TextFormat::DARK_AQUA, TextFormat::DARK_RED, TextFormat::DARK_PURPLE, TextFormat::GOLD, TextFormat::GRAY, TextFormat::DARK_GRAY, TextFormat::BLUE, TextFormat::GREEN, TextFormat::AQUA, TextFormat::RED, TextFormat::LIGHT_PURPLE, TextFormat::YELLOW, TextFormat::WHITE, TextFormat::OBFUSCATED, TextFormat::BOLD, TextFormat::STRIKETHROUGH, TextFormat::UNDERLINE, TextFormat::ITALIC, TextFormat::RESET],
            $message
        );
    }
}
