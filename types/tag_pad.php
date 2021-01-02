<?php

  $pad_tag_base = PAD_HOME . "tags/$pad_tag";

  $pad_return = include "$pad_tag_base.php";

  if ($pad_return === TRUE and file_exists("$pad_tag_base.html") )
    return pad_html_get ("$pad_tag_base.html");
  else
    return $pad_return;

?>