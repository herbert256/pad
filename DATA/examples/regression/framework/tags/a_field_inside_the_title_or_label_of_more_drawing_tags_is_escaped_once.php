<?php

  // A field written inside the quoted title, label, alt, button, past= or tab name of these
  // tags is escaped once, as the field alone is: it arrived escaped by its sanitize step and
  // the tag escaped it again - Q&A Å was shown and read out as Q&amp;A &Aring;. Each line
  // counts the texts written right; a tab named by the field is the one active= and open=
  // name with it.

  $t = 'Q&A Å';

?>
