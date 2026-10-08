// PAD admin - the little the console does in the browser: a filter box over a table, and a
// question before a button that changes or removes something. Everything works without it.

document.addEventListener ( 'DOMContentLoaded', function () {

  // <input data-filter="#table-id">: rows that do not hold the typed text are hidden.

  document.querySelectorAll ( 'input[data-filter]' ).forEach ( function ( box ) {

    var table = document.querySelector ( box.getAttribute ( 'data-filter' ) );

    if ( ! table )
      return;

    box.addEventListener ( 'input', function () {

      var want = box.value.toLowerCase ().trim ();

      table.querySelectorAll ( 'tbody tr' ).forEach ( function ( row ) {
        row.hidden = want !== '' && row.textContent.toLowerCase ().indexOf ( want ) < 0;
      } );

    } );

  } );

  // <button data-confirm="Remove it?">: asked before the form goes.

  document.querySelectorAll ( '[data-confirm]' ).forEach ( function ( button ) {

    button.addEventListener ( 'click', function ( event ) {
      if ( ! window.confirm ( button.getAttribute ( 'data-confirm' ) ) )
        event.preventDefault ();
    } );

  } );

} );
