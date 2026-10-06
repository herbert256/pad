<?php

  // Every method HTTP allows comes back as it was sent, upper-cased - a hyphen and all - so
  // the engine, which asks padRequestMethod too, sees an unusual method for what it is: its
  // CSRF check counts M-SEARCH among the unsafe ones. Only a method that is no token at all
  // reads as GET.

  $methodAsk = fn ( $method ) => trim ( padCurl ( [
    'url'     => $padGoExt . 'helpers/requests_method_show&padInclude',
    'options' => [ 'CUSTOMREQUEST' => $method ]
  ] ) ['data'] );

  $methods = implode ( ' ', array_map ( $methodAsk, [ 'GET', 'post', 'M-SEARCH', 'PROPFIND' ] ) );

?>
