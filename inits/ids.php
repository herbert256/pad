<?php
 
  $padSesID = $padSesID ?? $_REQUEST ['padSesID'] ?? padRandomString();
  $padRefID  = $padRefID  ?? $padReqID ?? $_REQUEST ['padReqID'] ?? '';
  $padReqID  = $padReqID  ?? padRandomString();

?>