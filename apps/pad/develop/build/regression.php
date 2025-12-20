<?php

  set_time_limit ( 60 );

  foreach ( padList () as $one ) {

    $item = $one ['item'];

    filePutFile ( 'develop', 'regression.txt', $item ) ;

    $store  = "regression/DATA/$item.html";
    $check  = "$item.pad";
    $source = fileGet ( $check );
    $old    = fileGet ($store);
    $curl   = padCurl ( "$padHost$padScript?$item&padInclude&padReference" );
    $good   = str_starts_with ( $curl ['result'], '2');
    $new    = $curl ['data'] ?? '';
    $new    = str_replace ( "\r\n", "\n", $new );

    if     ( ! $good                            ) $status = 'error';
    elseif ( ! file_exists ($store)             ) $status = 'new';
    elseif ( ! trim ($new)                      ) $status = 'empty';
    elseif ( strpos($source, '{example')        ) $status = 'skip' ;
    elseif ( strpos($source, '{ajax')           ) $status = 'skip' ;
    elseif ( strpos($source, 'random')          ) $status = 'random' ;
    elseif ( strpos($source, 'shuffle')         ) $status = 'random' ;
    elseif ( strpos($source, 'chance')          ) $status = 'random' ;
    elseif ( strpos($new,    'padAjax')         ) $status = 'skip' ;
    elseif ( $old == $new                       ) $status = 'ok';
    else                                          $status = 'warning';

    filePutFile ( 'regression', "$item.txt", $status ) ;

    if ( $status == 'new' )
      filePutFile ( 'regression', "$item.html", $new ) ;

    if ( ! $good or ! $new                    ) continue
    if ( str_contains ( $source, '{page'    ) ) continue;
    if ( str_contains ( $source, '{example' ) ) continue;
    if ( str_contains ( $source '{table'    ) ) continue;
    if ( str_contains ( $source '{demo'     ) ) continue;
    if ( str_contains ( $source '{ajax'     ) ) continue;

    $itemPHP  = fileGet ( "$item.php" );

    if ( $itemPHP  ) filePutFile ( 'examples', "$item.php",  $itemPHP ) ;
    if ( $itemPAD  ) filePutFile ( 'examples', "$item.pad",  $source  ) ;
    if ( $itemHTML ) filePutFile ( 'examples', "$item.html", $new     ) ;

  }

  if ( isset ($build) )
    return;

  padRedirect ( "regression" );

?>