<?php

  // Disabled membership predicate for octahedral: the x on the filename keeps it out of
  // the way, since nothing looks for xbool.php - build/include.php never loads it and
  // neither pqBuild() nor build/check.php counts the type as having a bool.php.
  //
  // It answers "is $n octahedral?" the expensive way, by rendering the sequence up to $n
  // through padCode and searching the printed terms. What supersedes it is build/check.php,
  // which for this type answers from the precomputed PADoctahedral table in generated.php,
  // falling back to the same generate-and-search through pqArray(); generation itself uses
  // the closed form in function.php.

  function pqBoolOctahedral( $n, $p=0 ) {

    $text = padCode ( "{sequence octahedral, stop=$n, try=$n}{\$sequence},{/sequence}" );
    $arr  = explode ( ',', $text );

    return in_array ( $n, $arr );

  }

?>