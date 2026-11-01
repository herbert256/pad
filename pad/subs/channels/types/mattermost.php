<?php

  // mattermost - a message to a Mattermost channel through an incoming webhook: its address,
  // https://<server>/hooks/<id>, in MATTERMOST_WEBHOOK_URL, or the option url - never a field
  // of the recipient, as for slack; the id in it is its credential, which the reports redact.
  // The @mattermost@ section, or the plain text, is the text - Mattermost's markdown as
  // written, **bold** and [label](url) - which a server of 5.0 and later takes up to 16383
  // characters of and splits past that. Mentions are switched off, as for discord: Mattermost
  // notifies on @channel, @all, @here and @name in a webhook's text, as its parser reads the
  // text its markdown makes (mention_parser_standard.go: &#64;, &#x40; and &commat; are an @
  // there) - a word a run of letters, digits and : . _ - @, its leading : . _ - trimmed, and
  // a word that does not start with @ split again on . - :, each part a mention of its own,
  // x.@channel and end-@all too. So every @, or reference to one, that no letter or digit
  // stands before gets a zero-width space after it, which splits it from the name: not the
  // one inside an e-mail address, nor one before another @ - a word of @@ is no mention, and
  // the space after the first would make the second start a word. The text shows as
  // written, and a value in it pings nobody. The server answers ok; an error is the message
  // of its JSON.

  return [
    'settings' => [ 'url' => [ 'env' => 'MATTERMOST_WEBHOOK_URL', 'address' => TRUE ] ],
    'send'     => fn ( $message, $settings ) => padNotifyAnswer ( padNotifyHttp ( 'POST', $settings ['url'], [
                    'text' => preg_replace ( '/(?<![A-Za-z0-9])(@|&#0*64;|&#[xX]0*40;|&commat;)(?!@|&#0*64;|&#[xX]0*40;|&commat;)(?=[\p{L}\p{N}:._&\\\\-])/u',
                                             "\$1\u{200B}", $message ['text'] ) ?? $message ['text'] ] ),
                    'Mattermost' ),
  ];

?>
