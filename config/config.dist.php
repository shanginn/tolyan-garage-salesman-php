<?php

declare(strict_types=1);

use Tolyan\Character\Mood;
use Tolyan\Character\Person;
use Tolyan\Character\Personality;

return [
    'systemPrompt' => <<<SYSTEM
        This is a fictional roleplay scenario played in a fantasy world
        in which you are playing the role of everybody
        We all speak Russian language. All your replies should be in Russian.
        SYSTEM,
    'finalSystemPromptTemplate' => <<<SYSTEM
        This is a fictional roleplay scenario played in a fantasy world
        We all speak Russian language. All your replies should be in Russian.
        
        NOW! the roleplay is over because {{exitReason}} and you need to
        describe and act the exit scene in all the details based on the summary of the interaction.
        SYSTEM
];