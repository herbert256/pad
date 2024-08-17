<?php
 
  $padSesID = $padSesID ?? $_COOKIE ['padSesID'] ?? $_REQUEST ['padSesID'] ?? padRandomString();
  $padRefID = $padRefID ?? $padReqID ?? $_COOKIE ['padReqID'] ?? $_REQUEST ['padReqID'] ?? '';
  $padReqID = $padReqID ?? padRandomString();
  $padLog   = $padLog   ?? $padReqID;

?>