<?php

  // Evaluates a play built by the 'make' strategy: returns whatever the type's own make.php
  // derives from the candidate, which for a {keep} or {remove} doubles as the membership
  // test - an unchanged value means the candidate was already on that sequence.

  return include PT . "$pqSeq/make.php";

?>