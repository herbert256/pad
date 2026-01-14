<?php

  include APP . 'clean.php';

  padDeleteDataDir ( DAT . 'reference'  );
  padDeleteDataDir ( DAT . 'regression' );
  padDeleteDataDir ( DAT . 'dumps'      );
  padDeleteDataDir ( DAT . 'temp'       );
  padDeleteDataDir ( DAT . 'examples'   );

  set_time_limit ( 60 );

  foreach ( padAppsList () as $one ) {

    extract ( $one );

    $store = DAT . "regression/$app/$item.html";

    $source = padFileGet ( APPS . "$app/$item.pad" );
    $curl   = padCurl ( "$padHost/$app?$item&padExamples&padReference&padInclude" );
    $old    = padFileGet ( $store );

    $good = str_starts_with ( $curl ['result'], '2');
    $new  = str_replace     ( "\r\n", "\n", $curl ['data'] );

    if     ( ! $good                    ) $status = 'error';
    elseif ( ! file_exists ($store)     ) $status = 'new';
    elseif ( ! trim ($new)              ) $status = 'empty';
    elseif ( strpos($source, 'random')  ) $status = 'random' ;
    elseif ( strpos($source, 'shuffle') ) $status = 'random' ;
    elseif ( strpos($source, 'chance')  ) $status = 'random' ;
    elseif ( $old == $new               ) $status = 'ok';
    else                                  $status = 'warning';

    if ( $status == 'new' )
      padFilePut ( $store, $new ) ;

    padFilePut ( str_replace ( '.html', '.txt', $store ), $status ) ;

    if ( ! str_starts_with ( $curl ['result'], '2' ) )
      continue;

    if ( str_contains ( $source, '{page'    ) ) continue;
    if ( str_contains ( $source, '{example' ) ) continue;
    if ( str_contains ( $source, '{ajax'    ) ) continue;
    if ( str_contains ( $source, '{table' ) ) continue;
    if ( str_contains ( $source, '{demo'  ) ) continue;
 
    if ( file_exists ( APPS . "$app/$item.php" ) )
      padFilePut ( "examples/$app/$item.php",  padFileGet ( APPS . "$app/$item.php" ) );

    padFilePut ( "examples/$app/$item.pad",  padTidySmall ( $source,        TRUE ) );
    padFilePut ( "examples/$app/$item.html", padTidySmall ( $curl ['data'], TRUE ) );

  }

  padRedirect ( 'errors' );

?>