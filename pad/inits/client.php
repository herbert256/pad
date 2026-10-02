<?php

  // Reads what the browser already has and what it can accept, into three globals the exits
  // consult when deciding on a response.
  //
  // $padClientEtag is the If-None-Match value with its quotes stripped, compared against the
  // freshly computed $padEtag by exits/output/web.php to answer 304 instead of resending the
  // page; $padClientDate is If-Modified-Since as a timestamp; $padClientGzip records whether
  // the response may be compressed - Accept-Encoding read with its quality values by
  // padAcceptsEncoding (lib/output.php), so gzip;q=0 is the refusal it is.

  $padClientEtag = isset($_SERVER['HTTP_IF_NONE_MATCH'])      ? substr($_SERVER['HTTP_IF_NONE_MATCH'], 1, 22) : '';
  $padClientDate = isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])  ? strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) : 0;
  $padClientGzip = padAcceptsEncoding ( $_SERVER['HTTP_ACCEPT_ENCODING'] ?? '', 'gzip' );

?>