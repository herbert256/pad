<?php

  // The border@tag property: TRUE on the first and on the last occurrence of a level.
  //
  // The or of the first and last property files; middle.php is its negation. Like every
  // file here it is included by padTag() (lib/field/tag.php) or padAtProperty()
  // (at/_lib/at.php), which supply $padIdx - the level being asked about - and take the
  // include's return value as the property value.

  return (
    (include PAD . "properties/first.php")
      or
    (include PAD . "properties/last.php")
  );

?>