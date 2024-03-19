<?php

namespace Drupal\content_polls\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\content_polls\Entity\ContentPoll;
use Drupal\content_polls\Entity\ContentPollResponse;
use Symfony\Component\HttpFoundation\Cookie;
use Exception;

class PollController extends ControllerBase
{
  public function submit(Request $request)
  {
    try {
      $params = json_decode($request->getContent());
      $poll = ContentPoll::load($params->pollId);
      if ($poll) {
        $pollOptions = $poll->get('poll_options');
        if (count($pollOptions) > 0) {
          $found = false;
          foreach ($pollOptions as $pollOption) {
            if ($pollOption->entity->id() === $params->optionId) {
              $found = true;
              continue;
            }
          }
          if ($found) {
            $response = ContentPollResponse::create([
              'poll' => $params->pollId,
              'response' => $params->optionId
            ]);
            $response->save();
            $completion_render_array = $poll->completion_text->view('full');
            $completion_text = \Drupal::service('renderer')->renderRoot($completion_render_array)->__toString();
            $jsonResponse = new JsonResponse(['success' => true, 'completion_text' => $completion_text]);
            $cookie = new Cookie('poll_' . $params->pollId, $params->optionId, time() + (86400 * 365));
            $jsonResponse->headers->setCookie($cookie);
            return $jsonResponse;
          } else {
            throw ('Selected choice was not found in this poll');
          }
        } else {
          throw ('Poll has no options');
        }
      } else {
        throw ('No poll found with that ID');
      }
    } catch (Exception $e) {
      return new JsonResponse(['success' => false, 'error' => $e]);
    }
  }
}
