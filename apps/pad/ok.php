<?php
    
$curl  = getPage ($item);
$store = padApp . "_regression/$item.html";

file_put_contents ($store, $curl ['data'], LOCK_EX);

$padRestart = $item;

padRedirect ( "show&item=$item" );

?>