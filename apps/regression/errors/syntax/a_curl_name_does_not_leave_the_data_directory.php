<?php

  // {curl} takes a name without a scheme for a data file of _data/ first: with a .. in it,
  // it walked out of the directory and read - or, for a .php, ran - any file it reached.

  $walk = '../syntax/outside_data_target';

?>
