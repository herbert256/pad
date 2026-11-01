// PAD Charts - the little the showcase does in the browser: copy buttons on the source cells.
// Light, dark and the themes are the frame's (www/_common/pad.css, pad/subs/themes/); the
// charts follow them by themselves, their colours use light-dark().

document.addEventListener ( 'DOMContentLoaded', function () {

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

} );
