<?php

  // starts_with:a,b - the value begins with one of the texts of the list, as written:
  // starts_with:https:// for a secure address, starts_with:NL,BE for a code of two
  // countries. The list needs a text, or the rule would ask for nothing.

  return [
    'check'    => function ( $value, $arg ) {
      foreach ( padRuleTexts ( $arg ) as $one )
        if ( str_starts_with ( $value, $one ) )
          return TRUE;
      return FALSE;
    },
    'argument' => fn ( $arg ) => padRuleTexts ( $arg ) ? '' : 'no text - starts_with:https://',
    'shown'    => fn ( $arg ) => implode ( ' or ', padRuleTexts ( $arg ) ),
    'message'  => ':label must start with :n'
  ];

?>
