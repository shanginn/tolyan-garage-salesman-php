<?php

declare(strict_types=1);

namespace Tolyan\MessagesStore;

use Doctrine\Common\Collections\Collection;
use Tolyan\Openai\ChatCompletion\Message\MessageInterface;

/**
 * @extends Collection<MessageInterface>
 */
interface MessagesCollectionInterface extends Collection {}