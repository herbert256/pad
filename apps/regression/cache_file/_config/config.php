<?php

  $padCommon = FALSE;

  $padCache           = TRUE;
  $padCacheServerType = 'file';
  $padCacheServerAge  = 60;

  // The page a_visitor_with_an_identity_is_never_answered_public fetches: shared caches may
  // keep it, and no PAD cookie goes out with it - with $padCookies on, the padReqID cookie
  // every response sets made each answer private, whoever asked.

  if ( $padPage == 'proxied' ) {
    $padCookies        = FALSE;
    $padCacheProxyAge  = 300;
    $padCacheClientAge = 60;
  }

?>
