<?php

  // pad help: the commands there are.

  cliOut ( <<<'TEXT'
pad - the PAD command

  pad new <app>                         a new application: apps/<app>/ and www/<app>/index.php
  pad new <app>/<dir>/<page>            a new page in it: <page>.php and <page>.pad
  pad serve [port] [host] [--mount=x]   PHP's built-in server over www/, no Apache needed
  pad render <app> [page] [name=value]  a page of any application to stdout
  pad lint <app> [dir]                  every page rendered under the strict check, errors listed
  pad help                              this list

Without a command word, pad runs the cli application: pad [page].
TEXT );

  return 0;

?>
