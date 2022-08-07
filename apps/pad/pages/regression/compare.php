<?php

  $title = "Compare";

  $old = pFile_get_contents ( DATA . "regression/$item.html" );
  $new = pad ('reference', "$item");
 
?>