<?php
 
  $PADSESSID = $PADSESSID ?? $_REQUEST ['PADSESSID'] ?? pRandom_string();
  $PADREFID  = $PADREFID  ?? $PADREQID ?? $_REQUEST ['PADREQID'] ?? '';
  $PADREQID  = $PADREQID  ?? pRandom_string();

?>