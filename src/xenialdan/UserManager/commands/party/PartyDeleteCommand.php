<?php

declare(strict_types=1);

namespace xenialdan\UserManager\commands\party;

use CortexPE\Commando\args\BaseArgument;
use CortexPE\Commando\BaseSubCommand;
use InvalidArgumentException;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use xenialdan\customui\windows\ModalForm;
use xenialdan\UserManager\models\Party;
use xenialdan\UserManager\UserStore;

class PartyDeleteCommand extends BaseSubCommand
{

    /**
     * This is where all the arguments, permissions, sub-commands, etc would be registered
     */
    protected function prepare(): void
    {
        $this->setPermission("usermanager.party.delete");
    }

    /**
     * @param CommandSender $sender
     * @param string $aliasUsed
     * @param BaseArgument[] $args
     * @throws InvalidArgumentException
     */
    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        $user = UserStore::getUser($sender);
        if ($user === null) {
            $sender->sendMessage("DEBUG: null");
            return;
        }
        $party = Party::getParty($user);
        if (!$party instanceof Party) {
            $user->getPlayer()->sendMessage(TextFormat::RED . "You are in no party");
            return;
        }
        if ($party->getOwnerId() !== $user->getId()) {
            $user->getPlayer()->sendMessage(TextFormat::RED . "You are not the owner of this party");
            return;
        }

        $form = new ModalForm("Delete Party?", "Do you want to delete the party \"{$party->getName()}\"?", "Yes", "No");
        $form->setCallable(function (Player $player, bool $data) use ($party, $user): void {
            if ($data) {
                self::delete($party);
            }
        });
        $sender->sendForm($form);
    }

    /**
     * @param Party $party
     */
    private static function delete(Party $party): void
    {
        $party->getOwner()->getPlayer()->sendMessage(TextFormat::AQUA . "You deleted the party \"{$party->getName()}\"!");
        foreach ($party->getMembers() as $member) {
            $member->getPlayer()->sendMessage(TextFormat::GOLD . "The party \"{$party->getName()}\" was deleted by the owner!");
            #$party->removeMember($member);//TODO check if this is needed
        }
        Party::removeParty($party);
    }
}