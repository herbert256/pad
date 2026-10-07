// PAD Charts - the little the showcase does in the browser: a light/dark switch that the
// charts follow (their colours use light-dark()), copy buttons on the source cells, and
// pulldowns that open on a tap as well as on hover.

( function () {

  var root = document.documentElement;

  function stored () {
    try { return localStorage.getItem ( 'padChartsTheme' ); } catch ( e ) { return null; }
  }

  function apply ( theme ) {
    if ( theme ) root.setAttribute ( 'data-theme', theme );
    else root.removeAttribute ( 'data-theme' );
  }

  apply ( stored () );

  document.addEventListener ( 'DOMContentLoaded', function () {

    var toggle = document.getElementById ( 'theme' );

    if ( toggle )
      toggle.addEventListener ( 'click', function () {
        var dark = root.getAttribute ( 'data-theme' )
          ? root.getAttribute ( 'data-theme' ) === 'dark'
          : matchMedia ( '(prefers-color-scheme: dark)' ).matches;
        var next = dark ? 'light' : 'dark';
        apply ( next );
        try { localStorage.setItem ( 'padChartsTheme', next ); } catch ( e ) {}
      } );

    document.querySelectorAll ( '.copy' ).forEach ( function ( button ) {
      button.addEventListener ( 'click', function () {
        var source = document.getElementById ( button.getAttribute ( 'data-copy' ) );
        if ( ! source || ! navigator.clipboard ) return;
        navigator.clipboard.writeText ( source.textContent ).then ( function () {
          button.textContent = 'Copied';
          button.classList.add ( 'is-done' );
          setTimeout ( function () { button.textContent = 'Copy'; button.classList.remove ( 'is-done' ); }, 1400 );
        } );
      } );
    } );

    var items = document.querySelectorAll ( '.menu-item' );

    items.forEach ( function ( item ) {
      var button = item.querySelector ( '.menu-button' );
      button.addEventListener ( 'click', function ( event ) {
        var open = ! item.classList.contains ( 'is-open' );
        items.forEach ( function ( other ) {
          other.classList.remove ( 'is-open' );
          other.querySelector ( '.menu-button' ).setAttribute ( 'aria-expanded', 'false' );
        } );
        if ( open ) {
          item.classList.add ( 'is-open' );
          button.setAttribute ( 'aria-expanded', 'true' );
        }
        event.stopPropagation ();
      } );
    } );

    document.addEventListener ( 'click', function () {
      items.forEach ( function ( item ) { item.classList.remove ( 'is-open' ); } );
    } );

    document.addEventListener ( 'keydown', function ( event ) {
      if ( event.key === 'Escape' )
        items.forEach ( function ( item ) { item.classList.remove ( 'is-open' ); } );
    } );

  } );

} ) ();
