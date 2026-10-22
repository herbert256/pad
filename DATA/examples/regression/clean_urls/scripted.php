<?php

  // A page in a bracketed route directory runs a script of that directory's _scripts/ -
  // the path holds [year], which glob() read as a character class.

  $scripted = trim ( padCurl ( $padHost . 'regression/clean_urls/index.php/yearly/2026/show?padInclude' ) ['data'] );

?>
