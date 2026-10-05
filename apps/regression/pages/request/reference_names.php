<?php

  // The reference application's directory and page lists take the reference they show
  // from the request, xref= and item= under DATA/reference/. A name that climbs out with
  // .. is no reference - it listed the repository's root and read any .txt it named - and
  // a name with nothing behind it is a page not found, where both lists ended on a PHP
  // error and a 500.

  $refAsk = fn ( $query ) => padCurl ( $padHost . "reference/?$query&padInclude" ) ['result'];

  $refResult = 'climbing dir: '    . $refAsk ( 'dir&xref=..&item=..' )
             . ', climbing pages: ' . $refAsk ( 'pages&xref=../..&item=apps/regression/pages/request/reference_names' )
             . ', missing dir: '   . $refAsk ( 'dir&xref=tag&item=nosuch' )
             . ', missing pages: ' . $refAsk ( 'pages&xref=tag/pad&item=nosuch' )
             . ', a list: '        . $refAsk ( 'dir&xref[]=tag&item=pad' )
             . ', the real one: '  . $refAsk ( 'pages&xref=tag/pad&item=if' );

?>
