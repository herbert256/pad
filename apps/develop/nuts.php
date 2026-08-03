<?php

  $title = "Everything";

  foreach ( padAppsList () as $one ) {
    $item = $one ['item'];
    $items [$item] ['item'] = $item;
  }

  ksort ($items);

?>