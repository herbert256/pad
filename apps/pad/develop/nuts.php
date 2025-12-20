<?php

  $title = "Everything";

  foreach ( padList () as $one ) {
    $item = $one ['item'];
    $items [$item] ['item'] = $item;
  }

  ksort ($items);

?>