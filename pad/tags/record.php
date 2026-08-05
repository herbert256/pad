<?php

  // The {record} tag and, through one-line includes, {field} and {array}: runs the tag's
  // parameter as SQL, using the tag name itself as db()'s command word - so {array "* from
  // users"} becomes db("array * from users"). $padTag [$pad] is read at run time, which is
  // what lets the three tags share this one file; {check} has its own boolean variant.

  return db ( $padTag [$pad] . ' ' . $padParm );

?>