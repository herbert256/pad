<?php

  // Fixtures for _cases/tags/returns.php: a PHP function whose name is written as a tag becomes
  // that tag, and what it returns decides what the level does. One function per kind of return
  // value, which is what the manual's "Tag return values" page demonstrates.

  function regTrue   () { return TRUE;          }
  function regFalse  () { return FALSE;         }
  function regNull   () { return NULL;          }
  function regList   () { return [ 1, 2, 3 ];   }
  function regEmpty  () { return [];            }
  function regString () { return 'Hello';       }

?>
