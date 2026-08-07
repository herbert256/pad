<?php

  // object: - reads the PHP variable called $name, whatever the page's .php file left in scope,
  // and flattens it to an array, so an object or resource can be walked like ordinary PAD data.
  //
  // Through $GLOBALS, because that is where a page's variables are: this file is included from
  // inside padEvalType(), so the variable variable it used to write reached that function's own
  // locals instead - object:myself handed back the evaluator's piped value, and no name a page
  // could define resolved at all.

  return padToArray ( $GLOBALS [$name] ?? '' );

?>
