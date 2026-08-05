<?php

  // {exists '/path/to/file'} is a plain file test: TRUE when the path is there, so the
  // content renders only then and the @else@ branch otherwise. The path is used exactly
  // as given - none of PAD's app, data or _include lookups apply.

  return file_exists ( $padParm );

?>