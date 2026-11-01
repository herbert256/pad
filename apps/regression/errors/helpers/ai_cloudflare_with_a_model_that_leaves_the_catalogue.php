<?php

  // The model of Workers AI is a part of its address: a name that is not @<source>/<author>/<name>
  // - here one that would climb out of /ai/run/ - is refused before anything is asked.

  $_ENV ['CLOUDFLARE_ACCOUNT_ID'] = '023e105f4ecef8ad9ca31a8372d0c353';

  padAi ( 'Hello?', [ 'provider' => 'cloudflare', 'key' => 'cf-test', 'model' => '@cf/../../tokens/verify' ] );

?>
