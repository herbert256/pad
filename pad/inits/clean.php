<?php

  // Gives the request a clean output buffer to render into.
  //
  // Any buffers left open by the entry point, by a previous run or by stray echo output are
  // closed and their contents discarded (padEmptyBuffers() collects into $padIgnored, which
  // nothing reads), then a single fresh buffer is opened. Everything a page prints from here
  // on is captured rather than sent, which is what lets exits/ decide the response - tidy it,
  // gzip it, write it to a file, or answer with a 304.

  padEmptyBuffers ( $padIgnored );

  ob_start();

?>