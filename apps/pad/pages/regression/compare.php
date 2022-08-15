<?php

  $title = "Compare";

  $old = padFileGetContents ( DATA . "regression/$item.html" );
  $new = pad ('reference', "$item");
 
?>