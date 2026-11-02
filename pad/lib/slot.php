<?php

  // Named slots and declared parameters for custom tags - an application or _common tag, a
  // {pad:define} or {pad:macro} of the template, or an _include snippet used as a pair: the tags whose template takes the caller's content
  // in at @content@.
  //
  //   {card title='Revenue'}                    _tags/card.pad
  //     <strong>{$revenue}</strong>             {pad:parms title, tone='info'}
  //     {pad:slot 'footer'}                         <div class="card {#tone}">
  //       <a href="?reports">Report</a>           <h2>{#title}</h2>  @content@
  //     {/pad:slot}                                   {pad:slot 'footer'}<footer>@content@</footer>{/pad:slot}
  //   {/card}                                   </div>
  //
  // A {pad:slot} pair standing directly in a custom tag's content - not inside another tag - is
  // a fill: when the tag's level opens, padSlotTake takes it out of the content and keeps
  // it under its name, in $padSlot for that level, so every use of the tag has its own and
  // nested cards cannot overwrite each other's. What is left of the content goes to
  // @content@ as before.
  //
  // Every other {pad:slot} is a place in the template where a fill goes - tags/slot.php. It
  // belongs to the use of the tag it renders in (padSlotOwner). A fill renders where its
  // slot stands, as the content at @content@ does; a fill is the caller's text, so a slot
  // inside a fill - a template passing one of its own slots on to a tag it uses - belongs
  // to the caller's tag, and so does a {#parameter} inside it ($padSlotFrom marks the
  // level that renders a fill, padFieldFirstParmTag looks past it).
  //
  // {pad:parms title, tone='info'} in a tag's template declares its parameters: a name alone is
  // required, name=default fills in what the caller left out. Once a tag declares them, a
  // parameter it was given and does not declare is reported under the strict check - the
  // typo nothing else would catch, since a custom tag reads its options from its template.

  // The types whose template merges the caller's content - the tags that can have slots.

  function padSlotType ( $type ) {

    return in_array ( $type, [ 'app', 'common', 'include', 'define', 'macro' ] );

  }

  // Takes the fills out of a custom tag's content: every {pad:slot 'name'}...{/pad:slot} that stands
  // directly in it, and {pad:slot 'name'/} as an explicitly empty fill. Returns them by name;
  // $content keeps the rest.
  //
  // The pairs are looked for with the content of the text pairs written over with spaces -
  // the same length, so the places found are the content's (padTextSpans, lib/template.php):
  // what an {pad:ignore} wraps, a {widget:highlight} sample or a {widget:markdown ignore} is text, as the
  // closer search of the tag and the bodies of {pad:define} and {pad:macro} read it. A {/pad:slot} shown
  // in a sample of a fill closed the fill there, and the sample's {pad:ignore} never closed:
  // {card}{pad:slot 'f'}{pad:ignore}{/pad:slot}{/pad:ignore}{/pad:slot}{/card} failed with "the pair {pad:ignore}
  // never closes".

  function padSlotTake ( &$content ) {

    $fills = [];

    if ( ! is_string ( $content ) or ! str_contains ( $content, '{pad:slot' ) )
      return $fills;

    $scan = $content;

    foreach ( padTextSpans ( $content ) as [ $from, $to ] )
      $scan = substr_replace ( $scan, str_repeat ( ' ', $to - $from ), $from, $to - $from );

    $taken = [];

    foreach ( padPairScan ( $scan, 'pad:slot' ) as $pair ) {

      if ( $pair ['depth'] or ! padPairTopLevel ( $scan, $pair ['start'] ) )
        continue;

      $name = padPairName ( $pair ['parms'] );

      if ( $name === '' )
        continue;

      $fills [$name] = $pair ['single'] ? '' : substr ( $content, $pair ['inner'], $pair ['close'] - $pair ['inner'] );
      $taken []      = $pair;

    }

    foreach ( array_reverse ( $taken ) as $pair )
      $content = substr ( $content, 0, $pair ['start'] ) . substr ( $content, $pair ['end'] );

    return $fills;

  }

  // The use of a custom tag a {pad:slot} or {pad:parms} at level $from belongs to: the nearest level
  // below it that opened a custom tag. A level that renders a fill hands the search on to
  // below the tag the fill was given to - the fill is the caller's text.

  function padSlotOwner ( $from = NULL ) {

    global $pad, $padSlot, $padSlotFrom;

    $from = $from ?? $pad;

    for ( $i = $from - 1; $i > 0; $i-- ) {

      if ( isset ( $padSlotFrom [$i] ) ) {
        $i = $padSlotFrom [$i];
        continue;
      }

      if ( isset ( $padSlot [$i] ) )
        return $i;

    }

    return FALSE;

  }

  // The fill given for a slot of the tag at level $owner, or NULL when the caller gave none.
  // Callable from a tag's PHP too, with the tag's own level.

  function padSlotFill ( $name, $owner = NULL ) {

    global $pad, $padSlot;

    $owner = $owner ?? $pad;

    return $padSlot [$owner] ['fills'] [$name] ?? NULL;

  }

  // {pad:parms title, subtitle='', tone='info'}: applies the declaration to the tag at level
  // $owner - a required name the caller left out is an error, a default fills in for what
  // was not given, and the declared names are kept for the strict sweep of level/unread.php.

  function padParmsDeclare ( $owner ) {

    global $padSlot, $padPrm, $padTag;

    $declared = [];

    foreach ( padAttrsItems () as [ $name, $expr ] ) {

      if ( $name === '' ) {

        $name = trim ( $expr );

        if ( ! padValidVar ( $name ) ) {
          padError ( "{pad:parms} declares names - '$name' is not one" );
          continue;
        }

        $declared [] = $name;

        if ( ! array_key_exists ( $name, $padPrm [$owner] ) )
          padError ( "the {" . $padTag [$owner] . "} needs the parameter $name=" );

      } else {

        $declared [] = $name;

        if ( ! array_key_exists ( $name, $padPrm [$owner] ) )
          $padPrm [$owner] [$name] = padEval ( $expr );

      }

      padDoneAt ( $owner, $name );

    }

    $padSlot [$owner] ['declared'] = array_merge ( $padSlot [$owner] ['declared'] ?? [], $declared );

  }

?>
