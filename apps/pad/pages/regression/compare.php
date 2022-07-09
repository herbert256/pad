<?php

  $title = "Compare";

  $old = pad_file_get_contents ( DATA . "regression/$item.html" );
  $new = pad ('reference', "$item");
 
?>