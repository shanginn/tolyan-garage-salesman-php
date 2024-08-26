<?php

declare(strict_types=1);

namespace Tolyan\MessagesStore;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Tolyan\Openai\ChatCompletion\Message\MessageInterface;

final class MessagesStore
{
    /**
     * @var Collection<int,MessagesCollectionInterface<MessageInterface>>
     */
    private Collection $store;

    public function __construct()
    {
        $this->store = new ArrayCollection();
    }

    public function add(int $chatId, MessageInterface $message): void
    {
        $this->getOrInit($chatId)->add($message);
    }

    public function get(int $chatId): MessagesCollectionInterface
    {
        return $this->getOrInit($chatId);
    }

    public function clear(int $chatId): void
    {
        $this->init($chatId);
    }

    private function getOrInit(int $chatId): MessagesCollectionInterface
    {
        if (!$this->store->containsKey($chatId)) {
            $this->init($chatId);
        }

        return $this->store->get($chatId);
    }

    private function init(int $chatId): void
    {
        $this->store->set($chatId, new MessagesCollection());
    }
}