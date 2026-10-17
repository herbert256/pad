<?php

  // A named query starting with WITH reads when neither its main statement nor a named
  // select of it starts with a write: replace() in a select list, or the word update in a
  // literal, is none. Any write word anywhere in it was one, so _data/staffReplaced.sql was
  // refused as "must read - it starts with 'with'".

?>
