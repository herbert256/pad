<?php

  // An attribute name is what HTML makes of it: a quote inside a name is part of the name -
  // x"action and x'action are attributes of their own - a name may start with = , and a >
  // inside a quoted value does not end the tag. Each form below posts to the action after
  // them, elsewhere.example for the first four, here for the last; the names read as the
  // action took the first three for forms posting here and handed them the session's
  // token, and the tag cut at the quoted > put the token inside the title.

  $padCsrf = TRUE;

?>
