<?php

  $deep [1] [1] [1] [1] [1] ['abc'] = '123';

  $deep [1] [1] [1] [1] [2] ['klm'] ['xyz'] = '789';

  // The lenient walk under test: what nothing claims stays in the output as written.

  $padCheckSyntax = FALSE;

?>