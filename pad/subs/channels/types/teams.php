<?php

  // teams - a message to a Microsoft Teams channel through a workflow's webhook ("Post to a
  // channel when a webhook request is received"): its address in TEAMS_WEBHOOK_URL, or the
  // recipient's own in a field teams. The message is an Adaptive Card - the title in bold
  // above the @teams@ section, or the plain text.

  return [
    'settings' => [ 'url' => [ 'env' => 'TEAMS_WEBHOOK_URL', 'to' => 'teams' ] ],
    'send'     => fn ( $message, $settings ) => padNotifyAnswer ( padNotifyHttp ( 'POST', $settings ['url'], [
                    'type'        => 'message',
                    'attachments' => [ [
                      'contentType' => 'application/vnd.microsoft.card.adaptive',
                      'content'     => [
                        '$schema' => 'http://adaptivecards.io/schemas/adaptive-card.json',
                        'type'    => 'AdaptiveCard',
                        'version' => '1.4',
                        'body'    => [ [ 'type' => 'TextBlock', 'text' => $message ['title'], 'weight' => 'Bolder', 'size' => 'Medium', 'wrap' => TRUE ],
                                       [ 'type' => 'TextBlock', 'text' => $message ['text'], 'wrap' => TRUE ] ] ] ] ] ] ), 'Teams' ),
  ];

?>
