<?php

  // Custom tags written in the template itself - tags/define.php, tags/macro.php.
  //
  //   {define 'card'}                              {macro 'entry', label, type='text'}
  //     {parms title, tone='info'}                   <label>{$label}
  //     <div class="card {#tone}">                     <input type="{$type}" name="{$label}">
  //       <h2>{#title}</h2> @content@                </label>
  //     </div>                                     {/macro}
  //   {/define}
  //                                                {entry 'E-mail', 'email'}
  //   {card title='Revenue'}{$revenue}{/card}      {entry label='Name'}
  //
  // A {define} is a custom tag as an _tags/name.pad file is one: its parameters are read as
  // {#title}, {parms} declares them, @content@ takes the caller's content in and {slot}s
  // work as they do there. A {macro} is called as a function is: its parameters, declared
  // on the macro, are given in their order or by name and are fields - {$label} - with the
  // defaults filling in what the call left out. Both keep the source unrendered at the
  // definition and render it, in the place and with the fields of each use, every time
  // they are used. Defined before the first use, as {content} and {data} are: the tag
  // only exists once its definition has been walked.
  //
  // The two stores are $padDefineStore and $padMacroStore; lib/type.php resolves a name
  // against them as it does against the other stores, define: and macro: assert the kind.

  // What a {define} or {macro} stores, and the checks of its name - shared by the two tags.

  function padMacroName ( $tag ) {

    global $pad, $padOpt, $padPair, $padCheckSyntax;

    if ( ! $padPair [$pad] and $padCheckSyntax )
      padError ( "the {" . $tag . "} needs its body - {" . $tag . " 'name'}...{/" . $tag . "}" );

    $name = trim ( (string) ( $padOpt [$pad] [1] ?? '' ) );

    if ( $name === '' or ! padValidName ( $name ) ) {
      if ( $padCheckSyntax )
        padError ( "the {" . $tag . "} needs a name - {" . $tag . " 'card'}" );
      return '';
    }

    // A name that is already a tag would never be reached - resolution finds the tag first -
    // and one that is the other kind's would be reached as that kind.

    if ( $padCheckSyntax ) {

      if ( file_exists ( PAD . "tags/$name.php" ) )
        padError ( "the {" . $tag . "} name '$name' is already a built-in tag - {" . $name . "} will never reach it" );

      if ( padAppTagCheck ( $name ) )
        padError ( "the {" . $tag . "} name '$name' is already an application tag - {" . $name . "} will never reach it" );

      if ( $GLOBALS ['padCommon'] and padCommonCheck ( $name ) )
        padError ( "the {" . $tag . "} name '$name' is already a tag of _common - {" . $name . "} will never reach it" );

      $other = ( $tag == 'define' ) ? 'padMacroStore' : 'padDefineStore';

      if ( isset ( $GLOBALS [$other] [$name] ) )
        padError ( "the name '$name' is already a {" . ( $tag == 'define' ? 'macro' : 'define' ) . "}" );

    }

    return $name;

  }

  // The parameters a {macro} declares: [ name, default expression or NULL ] in their order,
  // read as written - the items after the name, as {parms} and {attrs} read theirs.

  function padMacroDeclare () {

    global $pad;

    $declared = [];
    $first    = TRUE;

    foreach ( padAttrsItems () as [ $name, $expr ] ) {

      if ( $first ) {
        $first = FALSE;
        continue;
      }

      if ( $name === '' )
        [ $name, $expr ] = [ trim ( $expr ), NULL ];

      if ( ! padValidVar ( $name ) or padMacroReserved ( $name ) ) {
        padError ( "the {macro} declares parameter names - '" . padMakeSafe ( $name, 30 ) . "' is not one"
                   . ( padMacroReserved ( $name ) ? ' (it is an option of every tag)' : '' ) );
        continue;
      }

      $declared [] = [ $name, $expr ];

      padDone ( $name );

    }

    return $declared;

  }

  // A name the engine reads as an option of any tag - content=, sort=, first= ... - which
  // would act on the macro's use instead of reaching it.

  function padMacroReserved ( $name ) {

    return file_exists ( PAD . "options/$name.php" ) or file_exists ( PAD . "handling/types/$name.php" );

  }

  // The fields of one use of a {macro}: the values given in their order, then those given
  // by name, then the defaults - evaluated now, where the macro is used. A required
  // parameter left out, a value too many and a name the macro does not declare are errors.

  function padMacroRow ( $name ) {

    global $pad, $padOpt, $padParms, $padPrm, $padMacroStore, $padSlot;

    $macro = $padMacroStore [$name];
    $row   = [];
    $names = array_column ( $macro ['parms'], 0 );

    $given = 0;
    foreach ( $padParms [$pad] as $one )
      if ( $one ['padPrmKind'] == 'parm' )
        $given = max ( $given, (int) $one ['padPrmName'] );

    if ( $given > count ( $names ) )
      padError ( "the macro {" . $name . "} takes " . count ( $names ) . " parameter"
                 . ( count ( $names ) == 1 ? '' : 's' ) . ", it was given $given" );

    foreach ( $macro ['parms'] as $index => [ $parm, $expr ] ) {

      if ( $index < $given )
        $row [$parm] = $padOpt [$pad] [$index+1];
      elseif ( array_key_exists ( $parm, $padPrm [$pad] ) )
        $row [$parm] = $padPrm [$pad] [$parm];
      elseif ( $expr !== NULL )
        $row [$parm] = padEval ( $expr );
      else {
        padError ( "the macro {" . $name . "} needs the parameter $parm" );
        $row [$parm] = '';
      }

      padDone ( $parm );

    }

    // The strict sweep (level/unread.php) names a parameter the macro does not declare.

    $padSlot [$pad] ['declared'] = $names;

    return $row;

  }

?>
