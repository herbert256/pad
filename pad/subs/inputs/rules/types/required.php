<?php

  // required - the field holds something: a text that is not empty once trimmed, a list
  // (a field named tags[]) with at least one item filled in - three fields of guests[] left
  // blank hold none, as no box ticked holds none. It is one of the rules an empty field is
  // judged by, with accepted and the required_if family, which require it as it does.

  return [
    'empty'   => TRUE,
    'check'   => fn ( $value ) => $value !== '',
    'list'    => fn ( $list ) => padRuleItems ( $list ) > 0,
    'message' => ':label is required'
  ];

?>
