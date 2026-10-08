// PAD tags - the little the site does in the browser: a light/dark switch, remembered.

( function () {

  var root = document.documentElement;

  function stored () {
    try { return localStorage.getItem ( 'padTagsTheme' ); } catch ( e ) { return null; }
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
        try { localStorage.setItem ( 'padTagsTheme', next ); } catch ( e ) {}
      } );

  } );

} ) ();
