// PAD - the pull-down of the brand every PAD application shows top left
// (apps/_common/_tags/padBrand.pad): the list of the applications opens in front of everything
// the page has, the page dimmed behind it, and it closes on a click elsewhere - the dimmed
// page too - and on Escape.
//
// The list is a <details>, and without this script it works as one - open until its name is
// clicked again - but it stands where the brand stands: in the frame's side, whose sticky
// position makes a stacking context of its own, so the bar and every positioned part of the
// page painted over it. Here it becomes a popover when it opens: in the top layer, before
// whatever the page stacks, placed under the name. The page behind it is dimmed by a popover
// of its own shown just before it, over the whole window: a popover's ::backdrop lets a click
// through to the page - a link under it was followed - where this layer takes the click and
// shuts the list.

( function () {

  var popover = typeof HTMLElement !== 'undefined' && 'showPopover' in HTMLElement.prototype;

  function menus () {
    return document.querySelectorAll ( '.pad-brand-menu[open]' );
  }

  function list ( menu ) {
    return menu.querySelector ( '.pad-brand-apps' );
  }

  function shown ( apps ) {
    try { return apps.matches ( ':popover-open' ); } catch ( e ) { return false; }
  }

  // The layer that dims the page, made the first time a list opens.

  var dim = null;

  function dimmer () {
    if ( ! dim ) {
      dim = document.createElement ( 'div' );
      dim.className = 'pad-brand-dim';
      dim.setAttribute ( 'popover', 'manual' );
      dim.setAttribute ( 'aria-hidden', 'true' );
      document.body.appendChild ( dim );
    }
    return dim;
  }

  // Under the name, its left edge with the brand's - kept inside the window - and no taller
  // than the room below it.

  function place ( menu ) {

    var apps = list ( menu );

    if ( ! popover || ! apps )
      return;

    if ( ! apps.hasAttribute ( 'popover' ) )
      apps.setAttribute ( 'popover', 'manual' );

    if ( ! shown ( apps ) ) {
      if ( ! shown ( dimmer () ) )
        dim.showPopover ();
      apps.showPopover ();
    }

    var name  = menu.querySelector ( 'summary' ).getBoundingClientRect ();
    var brand = ( menu.closest ( '.pad-brand' ) || menu ).getBoundingClientRect ();
    var top   = Math.round ( name.bottom + 8 );
    var left  = Math.max ( 12, Math.min ( brand.left, window.innerWidth - apps.offsetWidth - 12 ) );

    apps.style.top       = top + 'px';
    apps.style.left      = Math.round ( left ) + 'px';
    apps.style.maxHeight = Math.max ( 160, window.innerHeight - top - 12 ) + 'px';

  }

  function shut ( menu ) {
    var apps = list ( menu );
    if ( popover && apps && shown ( apps ) )
      apps.hidePopover ();
    if ( dim && shown ( dim ) && ! menus ().length )
      dim.hidePopover ();
  }

  // The <details> says when it opens and shuts - its toggle does not bubble, so it is caught
  // on its way down. A popover's own toggle is no <details> and passes.

  document.addEventListener ( 'toggle', function ( event ) {
    var menu = event.target;
    if ( ! menu.matches || ! menu.matches ( '.pad-brand-menu' ) )
      return;
    if ( menu.open ) place ( menu ); else shut ( menu );
  }, true );

  // A click outside the list - on the dimmed page, which is the dimming layer - shuts it.

  document.addEventListener ( 'click', function ( event ) {
    menus ().forEach ( function ( menu ) {
      if ( ! menu.contains ( event.target ) )
        menu.open = false;
    } );
  } );

  document.addEventListener ( 'keydown', function ( event ) {
    if ( event.key !== 'Escape' )
      return;
    menus ().forEach ( function ( menu ) {
      menu.open = false;
      menu.querySelector ( 'summary' ).focus ();
    } );
  } );

  window.addEventListener ( 'resize', function () {
    menus ().forEach ( place );
  } );

} ) ();
