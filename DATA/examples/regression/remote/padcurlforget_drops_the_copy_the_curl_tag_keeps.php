<?php

  // padCurlForget ( $url ) drops the copy {curl $url, ttl=} keeps: the tag kept it under its
  // input array, [ 'url' => ... ], and padCurlForget of the address deleted nothing - the
  // page showed the old answer for the rest of the ttl after the source changed.

  $forgetUrl = $padHost . 'regression/remote/?stamp&padInclude&k=forget';

  padCurlForget ( $forgetUrl );

?>
