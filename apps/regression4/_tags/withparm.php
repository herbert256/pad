<?php

  // Fixture for the parm: cases: a tag whose template reads its own parameter back. Returning
  // TRUE keeps the tag itself from printing - an include with no return value gives 1, which
  // would land in the page.

  return TRUE;

?>