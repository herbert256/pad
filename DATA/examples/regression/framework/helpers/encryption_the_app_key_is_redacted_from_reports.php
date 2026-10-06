<?php

  // The error reports and dumps show the globals with every secret redacted: the
  // application key is one - whoever reads it can forge every signed link and open every
  // sealed value.

  $redacted = json_encode ( padRedact ( [ 'padAppKey' => 'base64:a2tra2tra2tra2tra2tra2tra2tra2tra2tra2tra2s=',
                                          'app_key'   => 'secret', 'padApp' => 'shop' ] ) );

?>
