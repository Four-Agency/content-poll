<?php

declare(strict_types=1);

namespace Drupal\content_polls;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Defines the access control handler for the content poll entity type.
 *
 * phpcs:disable Drupal.Arrays.Array.LongLineDeclaration
 *
 * @see https://www.drupal.org/project/coder/issues/3185082
 */
final class ContentPollAccessControlHandler extends EntityAccessControlHandler
{

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account): AccessResult
  {
    return match ($operation) {
      'view' => AccessResult::allowedIfHasPermissions($account, ['view content_poll', 'administer content_poll'], 'OR'),
      'update' => AccessResult::allowedIfHasPermissions($account, ['edit content_poll', 'administer content_poll'], 'OR'),
      'delete' => AccessResult::allowedIfHasPermissions($account, ['delete content_poll', 'administer content_poll'], 'OR'),
      default => AccessResult::neutral(),
    };
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL): AccessResult
  {
    return AccessResult::allowedIfHasPermissions($account, ['create content_poll', 'administer content_poll'], 'OR');
  }
}
