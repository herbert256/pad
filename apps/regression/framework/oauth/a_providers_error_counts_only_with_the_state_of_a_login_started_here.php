<?php

  // An answer with error= and error_description= is the provider's word only when it brings
  // the state of a login started here: from a crafted link - no state, or a state never kept
  // - it is no answer of a login, and padOAuthError says so in its own words rather than
  // printing what the link wrote as the provider's.

  $options = [ 'client_id' => 'x', 'client_secret' => 'y' ];
  $report  = [];

  padCurlFake ( [ '*' => 500 ] );

  foreach ( [ 'no state' => [], 'a state never kept' => [ 'state' => 'c3RhdGUtbmV2ZXIta2VwdC1oZXJl' ] ] as $label => $state ) {

    $_GET = [ 'error' => 'access_denied', 'error_description' => 'Your account is locked. Call +1-555-0100 to restore it.' ] + $state;

    $report [$label] = [ padOAuthUser ( 'github', $options ), padOAuthError () ];

  }

  $report ['fetches'] = count ( padCurlRecorded () );

  $json = json_encode ( $report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );

?>
