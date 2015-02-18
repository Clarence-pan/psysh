<?php

namespace Psy\TabCompletion\Matcher;

use Psy\Command\Command;

/**
 * A Psy Command tab completion Matcher.
 *
 * This matcher provides completion for all registered Psy Command names and
 * aliases.
 */
class CommandsMatcher extends AbstractMatcher
{
    /** @type string[] */
    protected $commands = array();

    /**
     * CommandsMatcher constructor.
     *
     * @param Command[] $commands
     */
    public function __construct(array $commands)
    {
        $this->setCommands($commands);
    }

    /**
     * Set Commands for completion.
     *
     * @param Command[] $commands
     */
    public function setCommands(array $commands)
    {
        $names = array();
        foreach ($commands as $command) {
            $names = array_merge(array($command->getName()), $names);
            $names = array_merge($command->getAliases(), $names);
        }
        $this->commands = $names;
    }

    /**
     * {@inheritDoc}
     */
    public function getMatches(array $tokens, array $info = array())
    {
        $input = $this->getInput($tokens);

        return array_filter($this->commands, function ($command) use ($input) {
            return AbstractMatcher::startsWith($input, $command);
        });
    }

    /**
     * {@inheritDoc}
     */
    public function hasMatched(array $tokens)
    {
        $token = array_pop($tokens);
        $prevToken = array_pop($tokens);

        switch (true) {
            case self::tokenIs($prevToken, self::T_NEW):
                return false;
            case self::hasToken(array(self::T_OPEN_TAG, self::T_STRING), $token):
                return true;
        }

        return false;
    }
}
