<?php

  // A search finds a line by the text the console shows of it, its secrets left out: the
  // first characters of a hidden token find nothing, where a search for $myToken = 'ab
  // found the line and one for $myToken = 'ax did not - a search per character spelled
  // the value out.

  $hiddenText = "<?php\n\n  \$myToken = 'abc';\n  \$shown   = 'abc';\n\n?>";

  $hiddenRight = count ( adminSearchLines ( $hiddenText, "myToken = 'ab", 0 ) );
  $hiddenWrong = count ( adminSearchLines ( $hiddenText, "myToken = 'ax", 0 ) );
  $hiddenName  = count ( adminSearchLines ( $hiddenText, 'myToken', 0 ) );
  $hiddenShown = count ( adminSearchLines ( $hiddenText, "shown   = 'ab", 0 ) );

?>
