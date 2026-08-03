<?php

  $title = "Everything";

  foreach ( padAppsList () as $key => $one ) {
    $items [$key] ['app']  = $one ['app'];
    $items [$key] ['item'] = $one ['item'];
  }

  ksort ($items);

?>