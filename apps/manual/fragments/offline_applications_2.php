<?php

  // padWasm() is TRUE on PHP compiled to WebAssembly - in
  // a browser, an application packed by pad pack - and
  // FALSE on a server.

  $where = padWasm () ? 'in this browser' : 'on the server';

  $mail  = padWasm () ? 'kept until the application is online'
                      : 'sent at once';

?>
