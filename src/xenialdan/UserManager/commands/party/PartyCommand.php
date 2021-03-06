<?php

declare(strict_types=1);

namespace xenialdan\UserManager\commands\party;

use CortexPE\Commando\args\BaseArgument;
use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\exception\SubCommandCollision;
use InvalidArgumentException;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use xenialdan\customui\elements\Button;
use xenialdan\customui\windows\SimpleForm;

class PartyCommand extends BaseCommand
{

    /**
     * This is where all the arguments, permissions, sub-commands, etc would be registered
     * @throws SubCommandCollision
     */
    protected function prepare(): void
    {
        $this->setPermission("usermanager.party");
        $this->registerSubCommand(new PartyCreateCommand("create", "Create a new party"));
        $this->registerSubCommand(new PartyInviteCommand("invite", "Invite a player to the party"));
        $this->registerSubCommand(new PartyKickCommand("kick", "Kick a player from the party"));
        $this->registerSubCommand(new PartyLeaveCommand("leave", "Leave the party"));
        $this->registerSubCommand(new PartyDeleteCommand("delete", "Delete the party"));
        $this->registerSubCommand(new PartyAcceptCommand("accept", "Join a party or allow a player to join the party"));
        $this->registerSubCommand(new PartyDenyCommand("deny", "Deny a player's party join request or a request to join a party"));
        $this->registerSubCommand(new PartyJoinCommand("join", "Request joining a player's party"));
        $this->registerSubCommand(new PartyRenameCommand("rename", "Rename your party"));
        $this->registerSubCommand(new PartyMembersCommand("members", "List members of the party"));
        $this->registerSubCommand(new PartyInfoCommand("info", "List information about the current or a player's party"));
        $this->registerSubCommand(new PartySetOwnerCommand("setowner", "Make a party member the owner of the party"));
        $this->registerSubCommand(new PartyChatCommand("chat", "Adds or removes you from the party chat"));
    }

    /**
     * @param CommandSender $sender
     * @param string $aliasUsed
     * @param BaseArgument[] $args
     * @throws InvalidArgumentException
     */
    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!$sender instanceof Player) {
            $sender->sendMessage("This command is for players only.");
            return;
        }
        if (empty($args)) {
            //TODO remove
            $form = new SimpleForm("Party Options");
            foreach ($this->getSubCommands() as $subCommand) {
                $form->addButton(new Button(ucfirst($subCommand->getName())));
            }
            $form->setCallable(function (Player $player, string $data): void {
                $player->getServer()->dispatchCommand($player, "party " . strtolower($data));
            });
            $sender->sendForm($form);
        }
    }
}
