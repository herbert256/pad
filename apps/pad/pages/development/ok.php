<?php

    $item = $item ?? 'examples/hello';
    
    $curl  = padComplete ($item, '', 1);
    $store = padApp . "regression/$item.html";

    file_put_contents ($store, $curl ['data'], LOCK_EX);

    $padRestart = 'development/regression';

?>