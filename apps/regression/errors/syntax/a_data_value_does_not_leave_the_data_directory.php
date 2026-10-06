<?php

  // A value naming a data file is looked up in _data/ and nowhere else: with a .. in it, it
  // walked out of the directory and read - or, for a .php, ran - any file it reached.

  $walk = '../syntax/outside_data_target';

?>
