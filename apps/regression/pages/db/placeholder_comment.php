<?php

  // A comment in a statement is no quoted literal: an apostrophe in it left the bare
  // placeholders after it counted as quoted, escaped but not quoted, and '99999 or 1=1'
  // joined the statement again - every row was counted. A placeholder in a comment is left
  // as written; one after the comment is filled as ever.

  $dashes = db ( "field count(*) -- the staff's count
                    from staff where salary > {0}", [ '99999 or 1=1' ] );
  $hash   = db ( "field count(*) # the staff's count
                    from staff where salary > {0}", [ '99999 or 1=1' ] );
  $block  = db ( "field count(*) /* the staff's count */ from staff where salary > {0}", [ '99999 or 1=1' ] );
  $after  = db ( "field count(*) from staff where salary > {0} -- it's {1}", [ 0, "x\nor 1=1" ] );
  $minus  = db ( "field 5--{0}", [ 2 ] );

?>
