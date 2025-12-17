<?php

  set_time_limit ( 300 );

  foreach ( padList () as $one ) {

    $item = $one ['item'];

    $itemPAD  = fileGet ( "$item.pad" );
    $itemPHP  = fileGet ( "$item.php" );

    if ( ! $itemPAD                            ) continue;
    if ( str_contains ( $itemPAD, '{page'    ) ) continue;
    if ( str_contains ( $itemPAD, '{example' ) ) continue;
    if ( str_contains ( $itemPAD, '{table'   ) ) continue;
    if ( str_contains ( $itemPAD, '{demo'    ) ) continue;
    if ( str_contains ( $itemPAD, '{ajax'    ) ) continue;

    filePutFile ( 'develop', 'current.txt', $item ) ;

    $curl = padCurl ( "$padHost$padScript?$item&padInclude&padReference" );

    if ( ! str_starts_with ( $curl ['result'], '2') ) continue;

    $itemHTML = $curl ['data'] ?? '';

    if ( ! trim ( $itemHTML ) ) continue;

    if ( $itemPHP  ) filePutFile ( 'examples', "$item.php",  $itemPHP  ) ;
    if ( $itemPAD  ) filePutFile ( 'examples', "$item.pad",  $itemPAD  ) ;
    if ( $itemHTML ) filePutFile ( 'examples', "$item.html", $itemHTML ) ;

  }

?>