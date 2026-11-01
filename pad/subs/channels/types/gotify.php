<?php

  // gotify - a message to a self-hosted Gotify server (gotify.net), which pushes it to the
  // phones and browsers its user is signed in on: the server in GOTIFY_URL - never the
  // recipient's - and an application's token in GOTIFY_TOKEN, sent as a bearer token in the
  // Authorization header, which the server reads beside its own X-Gotify-Key: the header the
  // reports redact by its name, and never the query's token=, which a proxy's log keeps. The
  // title goes as the title, the @gotify@ section, or the plain text, as the message, shown as
  // plain text (client::display text/plain - Gotify's markdown would load an image a value
  // names), and the url of the message as the address a click opens (client::notification).
  // The option priority, 0 to 10 as Gotify's table has it - 0 no notification, 8 and up sound
  // and vibration - overrides the application's own. A token is letters, digits, ., _ and -:
  // the old form A and 14 more, the new gtfya. and its key.

  return [
    'settings' => [ 'server'   => [ 'env' => 'GOTIFY_URL', 'address' => TRUE ],
                    'token'    => [ 'env' => 'GOTIFY_TOKEN', 'pattern' => '/^[A-Za-z0-9._-]{8,512}$/D', 'secret' => TRUE,
                                    'is' => 'Gotify application token - letters, digits, ., _ and -' ],
                    'priority' => [ 'default' => '', 'pattern' => '/^([0-9]|10)$/D', 'is' => 'Gotify priority - 0 to 10' ] ],
    'send'     => function ( $message, $settings ) {
      $body = [ 'title' => $message ['title'], 'message' => $message ['text'], 'extras' => [ 'client::display' => [ 'contentType' => 'text/plain' ] ] ];
      if ( (string) $settings ['priority'] !== '' )
        $body ['priority'] = (int) $settings ['priority'];
      if ( $message ['url'] !== '' )
        $body ['extras'] ['client::notification'] = [ 'click' => [ 'url' => $message ['url'] ] ];
      return padNotifyAnswer ( padNotifyHttp ( 'POST', rtrim ( (string) $settings ['server'], '/' ) . '/message', $body,
                                               [ 'Authorization' => 'Bearer ' . $settings ['token'] ] ), 'Gotify' );
    },
  ];

?>
