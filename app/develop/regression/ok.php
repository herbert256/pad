<?php
    
$curl = getPage ($item);

$store = DATA . "_regression/$item.html";
padFileChkDir     ( $store );
padFileChkFile    ( $store );
file_put_contents ( $store, $curl ['data'] );

$store =  DATA . "_regression/$item.txt";
$status = 'ok';
padFileChkDir     ( $store );
padFileChkFile    ( $store );
file_put_contents ( $store, $status ) ;

if ( str_contains ( $item, 'sequence' ) )
  padRedirect ( 'sequence/manual/regression', [ 'go' => 'regression' ] );
else  
  padRedirect ( "develop/regression" );

?>