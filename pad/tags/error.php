<?php

  // {error 'message'} raises a PAD error from the template. What that means is up to
  // $padErrorAction - a dump, a stop, a log entry or nothing at all; padError() itself
  // returns FALSE, so when the request survives the tag takes its @else@ branch.

  return padError ($padParm);

?>