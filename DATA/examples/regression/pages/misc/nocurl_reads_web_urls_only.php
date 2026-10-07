<?php

  // Without ext-curl padNoCurl does the fetching, and it fetches http and https only, as
  // padCurl's protocol options do: a file:// URL - this very page's own answer file - is
  // the 999 failure, where file_get_contents read whatever scheme it was given.

  $nocurl = padNoCurl ( [ 'url' => 'file://' . APP . 'misc/nocurl_reads_web_urls_only.txt',
                          'result' => '999', 'type' => '', 'data' => '' ] );

  $result = $nocurl ['result'];
  $read   = ( $nocurl ['data'] !== '' ) ? 'read' : 'not read';

?>
