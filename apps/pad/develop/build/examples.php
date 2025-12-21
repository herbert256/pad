<?php

  fileDeleteDir ( 'examples' );
  
  set_time_limit ( 60 );

  foreach ( padList () as $one ) {

    $item = $one ['item'];

    filePutFile ( 'develop', 'examples.txt', $item ) ;

    $source = fileGet ( "$item.pad" );
    $php    = fileGet ( "$item.php" );
    $curl   = padCurl ( "$padHost$padScript?$item&padInclude&padExamples" );
    $good   = str_starts_with ( $curl ['result'], '2');
    $new    = $curl ['data'] ?? '';
    $new    = str_replace ( "\r\n", "\n", $new );

    if ( ! $good or ! $new                    ) continue;
    if ( str_contains ( $source, '{page'    ) ) continue;
    if ( str_contains ( $source, '{example' ) ) continue;
    if ( str_contains ( $source, '{table'   ) ) continue;
    if ( str_contains ( $source, '{demo'    ) ) continue;
    if ( str_contains ( $source, '{ajax'    ) ) continue;

    if ( $php    ) filePutFile ( 'examples', "$item.php",  $php    ) ;
    if ( $source ) filePutFile ( 'examples', "$item.pad",  $source ) ;
    if ( $new    ) filePutFile ( 'examples', "$item.html", $new    ) ;

  }
 
  rename ( APP . 'examples/DATA', APP . 'examples/DATA' );

?>