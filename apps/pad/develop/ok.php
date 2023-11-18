<?php
    
$curl  = getPage ($item);
$store = padApp . "_regression/$item.pad";

padFileChkDir     ( $store );
padFileChkFile    ( $store );
file_put_contents ($store, $curl ['data'], LOCK_EX);

$padRestart = $item;

padRedirect ( "develop" );

?>