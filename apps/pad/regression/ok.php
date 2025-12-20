<?php

$curl = getPage ($item);

filePutFile ( 'regression', "$item.html", $curl ['data'] );
filePutFile ( 'regression', "$item.txt",  'ok'           );

include APP . 'regression/go.php';

?>