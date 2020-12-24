<?php

  $name_base = PAD_HOME . "tags/$name";

  $pad_return = include "$name_base.php";

  if ($pad_return === TRUE and file_exists("$name_base.html") )
    return pad_get_html ("$name_base.html");
  else
    return $pad_return;

?>