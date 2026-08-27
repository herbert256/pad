<?php

  // Overview of the Common suite - the regression/common tests, the pages that use the _common
  // application: {example}, {demo}, {table}, the {block} snippet. Every test is fetched over
  // HTTP, against the outcome recorded beside it. Test here reruns this suite; a page load
  // reads the last run, because running means one request per test. The pages that need
  // nothing from _common are the Pages suite, one menu entry back.

  getSuitePage ( 'common' );

?>