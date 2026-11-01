<?php

  // A range slider posted for real: a post of a number that is no whole one comes back with
  // the message of padValidate after the frame, tied to that end; a post of two crossed ends -
  // as a form without the script can send them - is stored lowest first and refilled as it
  // came; and a form of method get refills both ends from the query string, as a search
  // form keeps what was asked. The style and the script of the fields, and the session's
  // token, are left out of what is compared.

  $rangeResult = '';

  foreach ( [ [ 'post' => 'padForm=filter&price_from=1.5&price_to=300' ],
              [ 'post' => 'padForm=filter&price_from=400&price_to=200' ],
              [ 'get'  => '&year_from=1950&year_to=1999' ] ] as $rangeAsk ) {

    $rangeCurl = padCurl ( isset ( $rangeAsk ['post'] )
                           ? [ 'url' => $padGoExt . 'request/form_rangeslider&padInclude', 'post' => $rangeAsk ['post'] ]
                           : [ 'url' => $padGoExt . 'request/form_rangeslider&padInclude' . $rangeAsk ['get'] ] );

    $rangeData = preg_replace ( '~<(style|script)\b[^>]*>.*?</\1>|<link\b[^>]*>~s', '', $rangeCurl ['data'] );
    $rangeData = preg_replace ( '/ value="[0-9a-f]{64}"/', '', $rangeData );

    $rangeResult .= $rangeCurl ['result'] . "\n" . trim ( $rangeData ) . "\n\n";

  }

?>
{!rangeResult}
