<?php

  // A broken UTF-8 byte in the text or in the search words - a visitor's ?q=%FF - is a
  // character that stands in for it, where preg_split answered FALSE and the request ended.

  $q = "wor\xFF";
  $x = "abc\xFFdef ghi jkl mno";

?>
