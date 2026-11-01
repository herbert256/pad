<?php

  // discord - a message to a Discord channel through a webhook: its address in
  // DISCORD_WEBHOOK_URL, or the option url - never a field of the recipient, as for slack. The
  // @discord@ section, or the plain text, is the content - Discord's markdown as written, cut
  // at its 2000 characters; mentions are switched off, so a value in the text pings nobody.

  return [
    'settings' => [ 'url' => [ 'env' => 'DISCORD_WEBHOOK_URL', 'address' => TRUE ] ],
    'send'     => fn ( $message, $settings ) => padNotifyAnswer ( padNotifyHttp ( 'POST', $settings ['url'], [
                    'content'          => mb_strimwidth ( $message ['text'], 0, 2000, '...', 'UTF-8' ),
                    'allowed_mentions' => [ 'parse' => [] ] ] ), 'Discord' ),
  ];

?>
