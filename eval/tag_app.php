 <?php

  $name_base = PAD_APP . "tags/$name";

  $pad_include_file = "$name_base.php";
  $pad_return = include PAD_HOME . 'level/app.php';

  if ($pad_return === TRUE and file_exists("$name_base.html") )
    return pad_get_html ("$name_base.html");
  else
    return $pad_return;

?>