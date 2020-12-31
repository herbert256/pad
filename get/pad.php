<?php  

  $curl ['url'] = $GLOBALS['pad_location'] . $parm;

  $curl ['cookies'] ['PADSESSID'] = $GLOBALS['PADSESSID'];   
  $curl ['cookies'] ['PADREQID']  = $GLOBALS['PADREQID'];      

  return include PAD_HOME . 'get/go/curl.php';

?>