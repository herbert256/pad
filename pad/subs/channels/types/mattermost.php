<?php

  // mattermost - a message to a Mattermost channel through an incoming webhook: its address,
  // https://<server>/hooks/<id>, in MATTERMOST_WEBHOOK_URL, or the option url - never a field
  // of the recipient, as for slack; the id in it is its credential, which the reports redact.
  // The @mattermost@ section, or the plain text, is the text - Mattermost's markdown as
  // written, **bold** and [label](url) - which a server of 5.0 and later takes up to 16383
  // characters of and splits past that. Mentions are switched off, as for discord: Mattermost
  // notifies on @channel, @all, @here and @name in a webhook's text, so every @ that starts a
  // word as the server reads the words - after anything but a letter, a digit or : . _ - @,
  // or after a run of : . _ - there, which it trims; not the one inside an e-mail address -
  // gets a zero-width space after it, which splits it from the name: the text shows as
  // written, and a value in it pings nobody. The server answers ok; an error is the message
  // of its JSON.

  return [
    'settings' => [ 'url' => [ 'env' => 'MATTERMOST_WEBHOOK_URL', 'address' => TRUE ] ],
    'send'     => fn ( $message, $settings ) => padNotifyAnswer ( padNotifyHttp ( 'POST', $settings ['url'], [
                    'text' => preg_replace ( '/(^|[^\p{L}\p{N}:._@-])([:._-]*+)@(?=[\p{L}\p{N}])/u', "\$1\$2@\u{200B}", $message ['text'] ) ?? $message ['text'] ] ),
                    'Mattermost' ),
  ];

?>
