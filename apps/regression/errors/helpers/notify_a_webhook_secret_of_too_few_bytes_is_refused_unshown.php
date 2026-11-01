<?php

  $_ENV ['WEBHOOK_URL']    = 'https://hooks.example.com/pad';
  $_ENV ['WEBHOOK_SECRET'] = 'whsec_dG9vU2hvcnRUb1NpZ24=';

  padNotify ( [ 'id' => 1 ], 'hello', [ 'name' => 'Ann' ], [ 'webhook' ] );

?>
