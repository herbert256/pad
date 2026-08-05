<?php

  // Marker file, never included: its existence is what makes 'remove' a known sequence
  // option name - see exits/done.php, exits/info/options.php and exits/store/check.php.
  //
  // remove, or remove='sequence|parm', turns the sequences named on the tag into filters
  // that drop the matching terms - the counterpart of keep, alongside the make and flag
  // play modes. inits/check/check.php reads it into $pqRemove; plays/inits.php registers a
  // valued form as a play there and then, while a bare remove only sets the mode the
  // sequence-named options after it inherit. Also usable as tag, type or prefix
  // ({remove:odd}).

  return TRUE;

?>