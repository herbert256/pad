<?php

    $item = $item ?? 'examples/hello';
    
    $curl  = getPage ($item);
    $store = padApp . "_regression/$item.html";

    padFileChkDir     ( $store );
    padFileChkFile    ( $store );
    file_put_contents ( $store, $curl ['data'], LOCK_EX );

    $padRestart = 'development/regression';

?>