<?php

  // property: - returns a property of the nearest enclosing non-tag level, the values a tag
  // publishes about itself such as count, current, first and last.
  //
  // The 1 is the level to start looking from, and it has to be there: without it the search
  // begins at the level asking, which is the tag the expression is being evaluated for, so
  // {echo '' | property:current} read the {echo} rather than the loop around it and answered 0
  // where the tag form {property:current} answered the row number. types/property.php passes it,
  // and so does eval/single/parm.php for the same reason.

  return padTagValue ( $name, 1 );

?>