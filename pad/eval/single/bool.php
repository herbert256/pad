<?php

  // bool: - returns a named boolean from the bool store that {bool ...} and the toBool option
  // fill in, so {if bool:isAdmin} reads in an expression what {bool:isAdmin} reads as a tag. A
  // bare word that names a bool store resolves to this kind too. An unset flag is FALSE, as
  // options/bool.php has it. flag.php reads the same store under its older name.

  global $padBoolStore;

  return $padBoolStore [$name] ?? FALSE;

?>