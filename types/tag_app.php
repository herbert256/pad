<?php

  $pad_tag_base = PAD_APP . "tags/$pad_tag";

  $pad_include_file = "$pad_tag_base.php";
  $pad_return = include PAD_HOME . 'level/app.php';

  if ($pad_return === TRUE and file_exists("$pad_tag_base.html") )
    return pad_html_get ("$pad_tag_base.html");
  else
    return $pad_return;

?>
