<?php

  // A fetch the engine makes for a visitor from elsewhere says so (padSelfFetchHeaders) - but
  // only one whose address began with $padHost did: this site under the other spelling of
  // its host, or by its address, still arrived from loopback with nothing forwarded and was
  // taken for this machine's own, its {debug} boxes shown to the visitor.

  $curl = padCurl ( [ 'url'     => $padHost . 'regression/pages/?request/fetching_debugged_inner_by_another_name&padInclude',
                      'headers' => [ 'X-Forwarded-For' => '203.0.113.9' ] ] );

  $shown = str_contains ( $curl ['data'], 'pad-debug' ) ? 'YES' : 'no';

?>
