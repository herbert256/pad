<?php

  // A short list put in order by hand - the sortable widget: a to-do list ranked, the
  // tracks of a playlist, the steps of a recipe. Every {pad:tab} is an item, its parameter the
  // item's key, its content what the item shows; the order is posted with the form the list
  // stands in.
  //
  //   {pad:form 'ranking'}
  //     {widget:sortable 'priority', label='The tasks, most urgent first'}
  //       {tasks}{pad:tab $id}{$title}{/pad:tab}{/tasks}
  //     {/widget:sortable}
  //     <button>Save the order</button>
  //   {/pad:form}
  //
  //   if ( padPosted ( 'ranking' ) ) {
  //     asort ( $priority );                     // [ key => place ], the places from 1
  //     $order = array_keys ( $priority );       // the keys, first to last
  //   }
  //
  // Each item posts its place under its key - priority[a1]=1, priority[b7]=2 - the same with
  // a script and without one: without scripting each item shows a number field, its place,
  // which the visitor changes by hand. The list is sorted by those numbers, and items of one
  // number keep the order they had, as asort () sorts them: an item given the number of
  // another lands beside it on the side it came from - after it when moved up, before it
  // when moved down - and 0 puts an item first, a number past the last puts it last. What
  // comes back is a proposal: the application checks that its keys are the ones it wrote
  // and keeps the order itself, the form's CSRF token guarding the post.
  // A form that comes back - a field elsewhere in it failed its rules - shows the items in the
  // order posted, when the post names every item once.
  //
  // With scripting a small script, once per page with the CSP nonce, takes the number fields
  // out of sight and keeps them filled, and gives every item three controls: a grip to drag
  // the item with a mouse, a pen or a finger - the item follows the pointer from place to
  // place, Escape puts it back - and Move up and Move down buttons, one click a place, for a
  // pointer that cannot drag. The grip works from the keyboard as well: Space or Enter picks
  // the item up, the arrow keys, Home and End move it, Space or Enter puts it down, Escape
  // puts it back where it was, and leaving the grip puts it down. Every move is said in a
  // live region - 'Write the report, place 3 of 5' - and the grip and the buttons are named
  // by the item's text. After a move a pad-sortable event bubbles from the list, the keys in
  // their new order in its detail, and every number field whose place changed sends change.
  //
  // padSortable        the list, its items and their controls
  // padSortableKeys    the items checked: a key each, never twice
  // padSortableBack    the items in the order a form that came back posted, when it did
  // padSortableStyle   the CSS, www/assets/widgets/sortable.css, once per page
  // padSortableScript  the script, www/assets/widgets/sortable.js, once per page

  include_once PAD . 'subs/widgets/_lib/tabs.php';

  function padSortable ( $name, $items, $label ) {

    $id    = padWidgetId ( 'sortable', [ $name, array_column ( $items, 'label' ) ] );
    $count = count ( $items );
    $list  = padWidgetAttr ( $label );
    $field = padWidgetAttr ( $name );
    $out   = '';

    foreach ( $items as $index => $item ) {

      // The item's name is the text it shows: the <style> an effect or a widget inside it
      // writes - the first of its kind on the page - and a <script> go with their content.

      $key  = padWidgetAttr ( $item ['label'] );
      $text = preg_replace ( '#<(style|script)\b[^>]*>.*?</\1\s*>#is', '', $item ['content'] );
      $text = trim ( preg_replace ( '/\s+/u', ' ', html_entity_decode ( strip_tags ( $text ), ENT_QUOTES | ENT_HTML5, 'UTF-8' ) ) );
      $text = padWidgetAttr ( $text !== '' ? mb_strimwidth ( $text, 0, 60, '...', 'UTF-8' ) : $item ['label'] );
      $n    = $index + 1;
      $top  = ( $n == 1 ) ? ' aria-disabled="true"' : '';
      $end  = ( $n == $count ) ? ' aria-disabled="true"' : '';

      $out .= padProtect ( "<li class=\"pad-sortable-item\" data-pad-key=\"$key\" data-pad-name=\"$text\">"
                         . "<button type=\"button\" class=\"pad-sortable-grip\" aria-label=\"Move $text\" aria-describedby=\"$id-help\"><span aria-hidden=\"true\"></span></button>"
                         . '<div class="pad-sortable-content">' )
            . $item ['content']
            . padProtect ( '</div><span class="pad-sortable-moves">'
                         . "<button type=\"button\" class=\"pad-sortable-up\" aria-label=\"Move $text up\"$top><span aria-hidden=\"true\"></span></button>"
                         . "<button type=\"button\" class=\"pad-sortable-down\" aria-label=\"Move $text down\"$end><span aria-hidden=\"true\"></span></button>"
                         . '</span>'
                         . "<label class=\"pad-sortable-place\">Place <input type=\"number\" name=\"{$field}[$key]\" value=\"$n\" step=\"1\" required aria-label=\"Place of $text\"></label>"
                         . '</li>' );

    }

    return padSortableStyle ()
         . padProtect ( "<div class=\"pad-sortable\" id=\"$id\">"
                      . "<p class=\"pad-sortable-help\" id=\"$id-help\" hidden>Drag the item by its grip, or move it with its buttons. On the grip, Space"
                      . ' picks it up, the arrow keys move it, Space puts it down and Escape puts it back.</p>'
                      . "<ol class=\"pad-sortable-list\" aria-label=\"$list\">" )
         . $out
         . padProtect ( '</ol><div class="pad-sortable-live" aria-live="assertive"></div></div>' )
         . padSortableScript ();

  }

  // The items checked: every one has a key of letters, digits, _, -, . and : - the key it is
  // posted under, name[key] - and no two have one; FALSE, named under the strict check, when
  // one has not.

  function padSortableKeys ( $items ) {

    global $padCheckSyntax;

    $seen = [];

    foreach ( $items as $item ) {

      $key = (string) $item ['label'];

      if ( ! preg_match ( '/^[A-Za-z0-9_.:-]{1,64}$/D', $key ) ) {
        if ( $padCheckSyntax )
          padError ( "a {pad:tab} of a {widget:sortable} is named by its key - letters, digits, _, -, . and :, like {pad:tab 'a1'} - not '"
                     . padMakeSafe ( $key, 30 ) . "'" );
        return FALSE;
      }

      if ( isset ( $seen [$key] ) ) {
        if ( $padCheckSyntax )
          padError ( "the {widget:sortable} has two items with the key '$key' - every {pad:tab} names its own" );
        return FALSE;
      }

      $seen [$key] = TRUE;

    }

    return TRUE;

  }

  // The items in the order a form that came back posted them - name[key]=place, a whole
  // number, for every item and for nothing else - sorted by place, items of one place in the
  // order they had; the items as they are when the form did not come back or posted
  // something else.

  function padSortableBack ( $name, $items ) {

    $source = padFormSource ();

    if ( $source === NULL )
      return $items;

    [ $found, $posted ] = padFormFind ( $source, $name );

    if ( ! $found or ! is_array ( $posted ) or count ( $posted ) != count ( $items ) )
      return $items;

    $places = [];

    foreach ( $items as $index => $item ) {
      $place = $posted [ $item ['label'] ] ?? NULL;
      if ( ! is_string ( $place ) or ! preg_match ( '/^\s*-?\d{1,6}\s*$/D', $place ) )
        return $items;
      $places [$index] = (int) $place;
    }

    uksort ( $items, fn ( $a, $b ) => ( $places [$a] <=> $places [$b] ) ?: ( $a <=> $b ) );

    return array_values ( $items );

  }

  function padSortableStyle () {

    return padWidgetStyle ( 'sortable',
      [ 'accent'  => [ 'var(--accent,#2a78d6)', 'var(--accent,#5598e7)' ],
        'text'    => [ 'var(--text,#1f1f1d)', 'var(--text,#ecebe6)' ],
        'muted'   => [ 'var(--dim,#62615c)', 'var(--dim,#a9a8a0)' ],
        'line'    => [ 'var(--line,#e4e3df)', 'var(--line,#3a3a37)' ],
        'surface' => [ 'var(--panel,#ffffff)', 'var(--panel,#1f1f1d)' ],
        'hover'   => [ 'var(--accent-soft,#eef3fb)', 'var(--accent-soft,#22303f)' ] ],
      padWidgetAsset ( 'sortable.css' ) );

  }

  function padSortableScript () {

    return padWidgetScript ( 'sortable', padWidgetAsset ( 'sortable.js' ) );

  }

?>
