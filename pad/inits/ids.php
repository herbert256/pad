<?php
 
  $PADSESSID = $PADSESSID ?? $_REQUEST ['PADSESSID'] ?? pad_random_string();
  $PADREFID  = $PADREFID  ?? $PADREQID ?? $_REQUEST ['PADREQID'] ?? '';
  $PADREQID  = $PADREQID  ?? pad_random_string();

?>