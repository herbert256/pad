<?php
 
  $PADSESSID = $PADSESSID ?? $_REQUEST ['PADSESSID'] ?? padRandomString();
  $PADREFID  = $PADREFID  ?? $PADREQID ?? $_REQUEST ['PADREQID'] ?? '';
  $PADREQID  = $PADREQID  ?? padRandomString();

?>