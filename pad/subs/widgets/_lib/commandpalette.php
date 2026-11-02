<?php

  // A search over the commands of an application - the commandpalette widget: a button, and
  // Ctrl+K or ⌘K, open a dialog with a search box over a list of links and buttons; typing
  // narrows the list, the arrow keys choose, Enter runs the chosen one.
  //
  //   {widget:commandpalette 'Go to'}
  //     <p>Pages</p>
  //     <a href="?orders">Orders</a>
  //     <a href="?customers">Customers</a>
  //     <p>Actions</p>
  //     <a href="?orders/new">New order</a>
  //   {/widget:commandpalette}
  //   {widget:commandpalette data='commands'/}         rows: label, link, group, keywords
  //
  // The dialog is a modal <dialog> opened as {widget:modal} opens its own (subs/widgets/_lib/
  // modal.php): the button's invoker commands, a small script where the browser has none,
  // and with scripting off a link to the dialog's id that the CSS shows as an overlay
  // (:target) - the whole list then, links to follow, and no search box, which only a
  // script can make work. The commands are what renders between the tags - links, buttons,
  // a line of text (<p>) heading a group, a rule (<hr>) ending one - and with data= rows of
  // a store, the page's array or a _data file: a row's label (or text, name), its link (or
  // href, url), its group, a heading over the rows of one group, a rule before a row of
  // none after a group, and its keywords, words that find it too. A link of a row of another
  // scheme than http, https and mailto is left out, as a row without a label or a link is.
  //
  // A small script, once per page with the CSP nonce, makes the search - the WAI-ARIA
  // combobox pattern with a list box: the box is a combobox, the list a listbox of options,
  // the focus stays in the box and the option chosen is its active descendant. A word typed
  // keeps the commands whose text or keywords hold it - every word, upper and lower case and
  // accents alike - in their order, a heading only over a group that kept one; the arrow
  // keys move the choice round the list, Enter runs it - a link followed, a button clicked -
  // and closes the dialog, a click does the same. Escape, a click beside the dialog and the
  // shortcut again close it; the focus goes back to where it was, the box empty for the next
  // time. The shortcut is Ctrl and key= (k), ⌘ on a Mac; it opens the first palette of the
  // page, and not from a field, a text area or an editable part, whose own keys stay theirs.
  //
  // padCommandpalette        the button, the link and the dialog around the commands
  // padCommandpaletteRows    the commands of data= as markup, '' without data=
  // padCommandpaletteStyle   the CSS, www/assets/widgets/commandpalette.css, once per page
  // padCommandpaletteScript  the script, www/assets/widgets/commandpalette.js, once per page

  function padCommandpalette ( $label, $items, $key, $placeholder, $empty ) {

    $id    = padWidgetId ( 'commandpalette', [ $label, $items, $key ] );
    $label = padWidgetAttr ( $label );
    $hint  = padWidgetAttr ( $placeholder );
    $none  = padWidgetAttr ( $empty );
    $upper = strtoupper ( $key );

    return padCommandpaletteStyle ()
         . padProtect ( "<div class=\"pad-commandpalette\" id=\"$id\" data-pad-key=\"$key\">"
                      . "<button type=\"button\" class=\"pad-commandpalette-button\" commandfor=\"$id-dialog\" command=\"show-modal\" aria-keyshortcuts=\"Control+$upper Meta+$upper\">"
                      . "<span class=\"pad-commandpalette-glass\" aria-hidden=\"true\"></span>$label<kbd class=\"pad-commandpalette-kbd\" aria-hidden=\"true\">Ctrl $upper</kbd></button>"
                      . "<a class=\"pad-commandpalette-button pad-commandpalette-link\" id=\"$id-open\" href=\"#$id-dialog\">"
                      . "<span class=\"pad-commandpalette-glass\" aria-hidden=\"true\"></span>$label</a>"
                      . "<dialog class=\"pad-commandpalette-dialog\" id=\"$id-dialog\" aria-label=\"$label\" closedby=\"any\">"
                      . '<div class="pad-commandpalette-head">'
                      . "<span class=\"pad-commandpalette-title\" aria-hidden=\"true\">$label</span>"
                      . "<input type=\"text\" class=\"pad-commandpalette-input\" role=\"combobox\" aria-expanded=\"true\" aria-controls=\"$id-list\""
                      . " aria-autocomplete=\"list\" aria-label=\"$label\" placeholder=\"$hint\" autocomplete=\"off\" spellcheck=\"false\" autofocus>"
                      . "<a class=\"pad-commandpalette-close pad-commandpalette-link\" href=\"#$id-open\" aria-label=\"Close\"><span aria-hidden=\"true\">&#215;</span></a>"
                      . "</div><div class=\"pad-commandpalette-list\" id=\"$id-list\">" )
         . $items
         . padProtect ( "</div><p class=\"pad-commandpalette-empty\" hidden>$none</p></dialog></div>" )
         . padCommandpaletteScript ();

  }

  // The rows of data= as the commands they are: a link each, a heading where the group
  // changes, a rule where a row of no group follows a group - the end of that group, for
  // the eye and for the search, which keeps a heading over its own commands alone. A row
  // that is a list without a label, text or name is left out, as one without a link is: its
  // first field, a link or an id, is no label. NULL without data=.

  function padCommandpaletteRows () {

    $rows = padWidgetRows ( 'commandpalette' );

    if ( $rows === NULL )
      return NULL;

    $out   = '';
    $group = '';

    foreach ( (array) $rows as $row ) {

      if ( is_object ( $row ) )
        $row = padToArray ( $row );

      if ( ! is_array ( $row ) )
        $row = [ 'label' => $row ];

      $label = $row ['label'] ?? $row ['text'] ?? $row ['name'] ?? '';
      $link  = $row ['link']  ?? $row ['href'] ?? $row ['url']  ?? '';
      $in    = $row ['group'] ?? '';
      $words = $row ['keywords'] ?? '';

      if ( ! is_scalar ( $label ) or trim ( (string) $label ) === '' or ! is_scalar ( $link ) )
        continue;

      $href = padWidgetLink ( $link );

      if ( $href === '' )
        continue;

      $in = is_scalar ( $in ) ? trim ( (string) $in ) : '';

      if ( $in !== $group ) {
        $out  .= ( $in !== '' ) ? '<p>' . padWidgetAttr ( $in ) . '</p>' : '<hr>';
        $group = $in;
      }

      $more = ( is_scalar ( $words ) and trim ( (string) $words ) !== '' )
            ? ' data-pad-keywords="' . padWidgetAttr ( trim ( (string) $words ) ) . '"' : '';

      $out .= "<a href=\"$href\"$more>" . padWidgetAttr ( trim ( (string) $label ) ) . '</a>';

    }

    return padProtect ( $out );

  }

  function padCommandpaletteStyle () {

    return padWidgetStyle ( 'commandpalette',
      [ 'accent'   => [ 'var(--accent,#2a78d6)', 'var(--accent,#5598e7)' ],
        'text'     => [ 'var(--text,#1f1f1d)', 'var(--text,#ecebe6)' ],
        'muted'    => [ 'var(--dim,#62615c)', 'var(--dim,#a9a8a0)' ],
        'line'     => [ 'var(--line,#e4e3df)', 'var(--line,#3a3a37)' ],
        'surface'  => [ 'var(--panel,#ffffff)', 'var(--panel,#1f1f1d)' ],
        'hover'    => [ 'var(--accent-soft,#eef3fb)', 'var(--accent-soft,#22303f)' ],
        'backdrop' => [ 'rgba(20,20,18,.45)', 'rgba(0,0,0,.65)' ] ],
      padWidgetAsset ( 'commandpalette.css' ) );

  }

  function padCommandpaletteScript () {

    return padWidgetScript ( 'commandpalette', padWidgetAsset ( 'commandpalette.js' ) );

  }

?>
