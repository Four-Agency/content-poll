<?php declare(strict_types = 1);

namespace Drupal\content_polls;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface defining a content poll entity type.
 */
interface ContentPollInterface extends ContentEntityInterface, EntityOwnerInterface, EntityChangedInterface {

}
