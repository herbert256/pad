<?php

  // A date range posted for real: a post without its departure comes back with the message
  // of padValidate tied to that day's field, a departure before the arrival breaks after: and
  // comes back as posted, and two days in order are stored as the nights between them. The
  // style and the script of the field, and the session's token, are left out of what is
  // compared.

  $bookResult = '';

  foreach ( [ 'padForm=book&stay_from=2026-08-10&stay_to=',
              'padForm=book&stay_from=2026-08-10&stay_to=2026-08-03',
              'padForm=book&stay_from=2026-08-10&stay_to=2026-08-14' ] as $bookPost ) {

    $bookCurl = padCurl ( [ 'url'  => $padGoExt . 'request/form_daterange&padInclude',
                            'post' => $bookPost ] );

    $bookData = preg_replace ( '~<(style|script)\b[^>]*>.*?</\1>|<link\b[^>]*>~s', '', $bookCurl ['data'] );
    $bookData = preg_replace ( '/ value="[0-9a-f]{64}"/', '', $bookData );

    $bookResult .= $bookCurl ['result'] . "\n" . trim ( $bookData ) . "\n\n";

  }

?>
{!bookResult}
