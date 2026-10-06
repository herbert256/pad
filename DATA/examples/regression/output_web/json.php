<?php

  // A page that declares itself JSON. The index fetches it whole - no padInclude - so the
  // exit pass that tidies HTML gets its chance at it, and must leave it alone.

  $padContentType = 'application/json';

?>
