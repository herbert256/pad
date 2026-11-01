<?php

  // Workers AI runs a model under an account, a part of its address: without
  // CLOUDFLARE_ACCOUNT_ID (and no url=) there is no address to ask.

  padAi ( 'Hello?', [ 'provider' => 'cloudflare', 'key' => 'cf-test' ] );

?>
