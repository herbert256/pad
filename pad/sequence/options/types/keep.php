<?php

  // Marker file, never included: its existence is what makes 'keep' a known sequence
  // option name - see exits/done.php, exits/info/options.php and exits/store/check.php.
  //
  // keep, or keep='sequence|parm', turns the sequences named on the tag into filters that
  // let only matching terms through - the counterpart of remove, alongside the make and
  // flag play modes. inits/check/check.php reads it into $pqKeep; plays/inits.php registers
  // a valued form as a play there and then, while a bare keep only sets the mode the
  // sequence-named options after it inherit. Also usable as tag, type or prefix
  // ({keep:even}).

  return TRUE;

?>