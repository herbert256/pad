<?php

  $padClientEtag = isset($_SERVER['HTTP_IF_NONE_MATCH'])      ? substr($_SERVER['HTTP_IF_NONE_MATCH'], 1, 22) : '';
  $padClientDate = isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])  ? strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) : 0;
  $padClientGzip = (isset($_SERVER['HTTP_ACCEPT_ENCODING']) and strpos($_SERVER['HTTP_ACCEPT_ENCODING'],'gzip') !== FALSE);
          
?>