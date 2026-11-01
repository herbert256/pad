<?php

  // slack - a message to a Slack channel through an incoming webhook: its address in
  // SLACK_WEBHOOK_URL, or the recipient's own in a field slack. The @slack@ section, or the
  // plain text, is the message - Slack's mrkdwn, *bold* and <url|link>, as written. Slack
  // reads &amp;, &lt; and &gt; in a message as the characters, so a value keeps them as its
  // field wrote them: a name <ann> shows as written and opens no link. A plain text with HTML
  // in it - written for the mail - goes as its text, every &, < and > of it escaped.

  return [
    'settings' => [ 'url' => [ 'env' => 'SLACK_WEBHOOK_URL', 'to' => 'slack' ] ],
    'send'     => function ( $message, $settings ) {
      $text = ( $message ['markup'] and ! $message ['own'] )
            ? str_replace ( [ '&', '<', '>' ], [ '&amp;', '&lt;', '&gt;' ], $message ['text'] )
            : preg_replace_callback ( '/&(?!(?:amp|lt|gt);)#?[a-z0-9]+;/i',
                                      fn ( $entity ) => html_entity_decode ( $entity [0], ENT_QUOTES | ENT_HTML5, 'UTF-8' ), $message ['rendered'] );
      return padNotifyAnswer ( padNotifyHttp ( 'POST', $settings ['url'], [ 'text' => $text ] ), 'Slack' );
    },
  ];

?>
