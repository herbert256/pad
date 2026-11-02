<?php

  // The shared body of section:kind tags, such as {section:hero}. The caller sets
  // $padSectionKind; registered kinds are files in subs/sections/types/.
  //
  // A pair runs twice: the start walk takes the section's slots out of its content and puts
  // them back between markers, reads the rows of data= and opens the collection its
  // {section:item}s hand themselves to; the content renders; the end walk splits the slots off
  // what it rendered to and builds the section from its parts, its text and its items. A
  // single tag is built at once. A {section:item} is collected, not built: it hands its first
  // value, its options and what its content rendered to to the section around it.

  include_once PAD . 'subs/sections/_lib/section.php';

  if ( ! padSectionType ( $padSectionKind ) ) {
    if ( $padCheckSyntax )
      padError ( "there is no section named '" . padMakeSafe ( $padSectionKind, 20 ) . "' - " . implode ( ', ', padSectionTypes () ) );
    return '';
  }

  if ( $padSectionKind == 'item' ) {

    if ( $padWalk [$pad] == 'start' ) {

      if ( padSectionOwner () === FALSE ) {
        if ( $padCheckSyntax )
          padError ( "a {section:item} belongs inside a section that lists items - {section:pricing}, {section:faq}, {section:team} ..." );
        $padContent = '';
        return $padPair [$pad] ? TRUE : '';
      }

      if ( $padPair [$pad] ) {
        $padWalk [$pad] = 'end';
        return TRUE;
      }

      padSectionItemAdd ( padSectionOwner (), NULL );

      return '';

    }

    padSectionItemAdd ( padSectionOwner (), $padContent );

    $padContent = '';

    return TRUE;

  }

  // The section used goes on the xref record, which the reference application shows: the
  // pages that use each section - once, on its first walk.

  if ( $padWalk [$pad] == 'start' ) {

    if ( $padInfoXref ?? FALSE )
      padInfoXref ( 'section', $padSectionKind );

    // The rows data= names, NULL without it. The handling options - sort=, first=, where= ...
    // - work on them, as on any data tag's rows, where they ran after the tag on its one
    // default occurrence: first= was passed over, and a sort= or a where= stopped the page on
    // a field no row had. They run here, so the level does not run them again.

    $padSectionRows = padSectionRows ( $padSectionKind );

    if ( $padSectionRows !== NULL and padSectionHandles () ) {
      $padData [$pad] = padData ( $padSectionRows );
      include PAD . 'handling/handling.php';
      $padSectionRows     = $padElse [$pad] ? [] : $padData [$pad];
      $padData [$pad]     = padDefaultData ();
      $padHandDone [$pad] = TRUE;
    }

    // A list section whose data= found no rows - a query that found none, a where= that let
    // none through - and that writes no items of its own is a miss, so its @else@ shows, as a
    // chart without points does: it stopped the page under the strict check, which is for a
    // template that gives no items at all. Its options are read: a miss uses none of them.

    if ( $padSectionRows !== NULL and ! count ( $padSectionRows ) and padSectionLists ( $padSectionKind )
         and ! ( $padPair [$pad] and padSectionWritesItems ( $padContent ) ) ) {
      foreach ( array_keys ( $padPrm [$pad] ?? [] ) as $padSectionParm )
        padDone ( $padSectionParm );
      return FALSE;
    }

    padSectionOpen ( $padSectionKind, $padContent, $padPair [$pad], $padSectionRows );

    if ( $padPair [$pad] ) {
      $padWalk [$pad] = 'end';
      return TRUE;
    }

    return padSectionBuild ( $padSectionKind, '' );

  }

  $padContent = padSectionBuild ( $padSectionKind, $padContent );

  return TRUE;

?>
