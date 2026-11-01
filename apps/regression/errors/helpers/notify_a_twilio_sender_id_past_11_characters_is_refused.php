<?php

  // Synthetic credentials; this case must reject the sender before sending.

  $_ENV ['TWILIO_ACCOUNT_SID'] = 'AC' . str_repeat ( '0', 32 );
  $_ENV ['TWILIO_AUTH_TOKEN']  = str_repeat ( 't', 32 );
  $_ENV ['TWILIO_FROM']        = 'My Shop Online';

  padNotify ( [ 'id' => 1, 'phone' => '+31612345678' ], 'hello', [ 'name' => 'Ann' ], [ 'twilio' ] );

?>
