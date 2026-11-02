<?php

  // ends_with:a,b - the value ends with one of the texts of the list, as written:
  // ends_with:.pdf,.txt for a file name, ends_with:@example.com for an address of the house.

  return [
    'check'    => function ( $value, $arg ) {
      foreach ( padRuleTexts ( $arg ) as $one )
        if ( str_ends_with ( $value, $one ) )
          return TRUE;
      return FALSE;
    },
    'argument' => fn ( $arg ) => padRuleTexts ( $arg ) ? '' : 'no text - ends_with:.pdf',
    'shown'    => fn ( $arg ) => implode ( ' or ', padRuleTexts ( $arg ) ),
    'message'  => ':label must end with :n'
  ];

?>
