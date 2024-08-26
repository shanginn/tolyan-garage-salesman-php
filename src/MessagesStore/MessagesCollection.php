<?php

declare(strict_types=1);

namespace Tolyan\MessagesStore;

use Doctrine\Common\Collections\ArrayCollection;

class MessagesCollection extends ArrayCollection implements MessagesCollectionInterface {}