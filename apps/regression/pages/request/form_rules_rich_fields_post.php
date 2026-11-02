<?php

  // The rules on the rich fields of a template are checked before the page's PHP runs, as
  // those of {pad:input}: a post that breaks every one comes back with each field refilled - a
  // password never - its message after its frame and the form's error= above them, and the
  // page's PHP does not store it; a post that keeps them is stored. The style and the script
  // of the fields, and the session's token, are left out of what is compared.

  $richResult = '';

  foreach ( [ [ 'volume' => '95', 'guests' => '9', 'terms' => '0', 'tint' => 'red', 'day' => '2026-02-30', 'at' => '25:00',
                'topics' => 'far too many topics here', 'signed' => '', 'note' => 'a note too long', 'password' => 'short',
                'code' => '12345', 'city' => 'Paris' ],
              [ 'volume' => '50', 'guests' => '2', 'terms' => '1', 'score' => '4', 'tint' => '#1a8a5a', 'day' => '2026-10-31',
                'at' => '09:30', 'topics' => 'pad, php', 'signed' => 'data:image/png;base64,iVBORw0KGgo=', 'note' => 'fine',
                'password' => 'long enough', 'code' => '123456', 'city' => 'Oslo' ] ] as $richPost ) {

    $richCurl = padCurl ( [ 'url'  => $padGoExt . 'request/form_rules_rich_fields&padInclude',
                            'post' => [ 'padForm' => 'trip' ] + $richPost ] );

    $richData = preg_replace ( '~<(style|script)\b[^>]*>.*?</\1>~s', '', $richCurl ['data'] );
    $richData = preg_replace ( '/ value="[0-9a-f]{64}"/', '', $richData );

    $richResult .= $richCurl ['result'] . "\n" . trim ( $richData ) . "\n\n";

  }

?>
{!richResult}
