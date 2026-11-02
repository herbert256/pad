<?php

  // The rich form fields: the files of subs/inputs/types/ (padKinds, lib/subsystem.php) - a kind is
  // added by adding its PHP file there, and its script and style of the same name in
  // www/assets/inputs/. The rest of the inputs is the subsystem in subs/inputs/, its library
  // subs/inputs/_lib/ loaded when a page writes one.

  function padInputTypes () {

    return padKinds ( 'inputs' );

  }

  function padInputType ( $name ) {

    return padKind ( 'inputs', $name );

  }

  // The name a field posts under. A kind that posts a list - a multiselect - posts it as
  // name[], so PHP hands the page every choice: {input:multiselect 'interests'} posts interests[],
  // and interests[] written out stays as it is. That name is the one the list is judged by -
  // the rules of the template read before the page's PHP runs (lib/form.php
  // padFormRulesField), padValidate, the browser's checker - and the field asks it here too,
  // so the three cannot part. Every other tag posts under the name it is given. $tag is the
  // tag as written: multiselect, input:multiselect, input.

  function padInputPosts ( $tag, $name ) {

    $name = (string) $name;
    $kind = str_starts_with ( (string) $tag, 'input:' ) ? substr ( (string) $tag, 6 ) : (string) $tag;

    if ( $kind == 'multiselect' and $name !== '' and ! str_ends_with ( $name, '[]' ) )
      return $name . '[]';

    return $name;

  }

?>
