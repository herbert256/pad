<?php

  // {check "users where email='x@y.z'"} asks the database whether such a row exists and
  // returns TRUE or FALSE, so the content renders on a hit and the @else@ branch
  // otherwise. The tag name is used as the db() command word, which is why CHECK takes a
  // bare table and WHERE clause instead of a SELECT.

  return db ( $padTag [$pad] . ' ' . $padParm ) ? TRUE : FALSE;

?>