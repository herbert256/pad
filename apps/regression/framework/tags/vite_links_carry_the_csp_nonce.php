<?php

  // Under a policy that asks for a nonce every tag {vite} writes carries it - the
  // stylesheets and the modulepreloads too, where only the scripts did: a stylesheet
  // without it was not applied under a style-src 'nonce'.

  $padViteBuild = 'vitefixture';
  $padCsp       = "script-src 'nonce'; style-src 'nonce'";

?>
