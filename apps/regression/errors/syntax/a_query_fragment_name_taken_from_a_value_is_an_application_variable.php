<?php

  // A name chosen by a value - {?$sel} - is the name of an application variable or no name
  // at all: one that is markup, a"><b>, would close the attribute it is written into, and
  // even url-encoded it names nothing a page sets. It is refused as {$$x} refuses it.

  $sel = 'a"><b>';

  $GLOBALS ['a"><b>'] = 'x y';

?>
