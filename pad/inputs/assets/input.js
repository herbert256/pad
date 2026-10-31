/* The runtime of the PAD inputs (pad/inputs/_lib/input.php writes it once per page). A kind's
   script registers itself - PadInputs.register('slider', function (frame, cfg, kit) { ... })
   - and is started on every field of its kind: the ones on the page, and the ones a fragment,
   a live region or a script brings later. frame is the field's .pad-input element, cfg its
   data-config, kit the helpers below. The server wrote a native control that a form posts
   as it is; a kind's script makes more of it and keeps the posted control's value true -
   and says so with the input and change events a typed value sends, which the browser's
   checker of the rules (pad/rules/assets/validate.js) listens to. */

(function () {
  'use strict';

  if (window.PadInputs) return;

  var kinds = {};
  var made = 0;

  function el(tag, cls, text) {
    var e = document.createElement(tag);
    if (cls) e.className = cls;
    if (text !== undefined && text !== null) e.textContent = text;
    return e;
  }

  var kit = {
    el: el,

    // A new id of the page, for an element a script adds: a listbox, an option, a box.
    id: function (base) {
      made++;
      return (base || 'pad-input') + '-js' + made;
    },

    // The value changed as if typed: the form, the rules' checker and any listener hear it.
    changed: function (control) {
      control.dispatchEvent(new Event('input', { bubbles: true }));
      control.dispatchEvent(new Event('change', { bubbles: true }));
    },

    // What the server wrote hidden for a script to show - a button, a popup's opener.
    show: function (root, selector) {
      var list = root.querySelectorAll(selector);
      for (var i = 0; i < list.length; i++) list[i].hidden = false;
    },

    // A click outside the element - or the focus leaving it - closes what it opened.
    outside: function (root, close) {
      document.addEventListener('pointerdown', function (event) {
        if (!root.contains(event.target)) close();
      });
      root.addEventListener('focusout', function (event) {
        if (event.relatedTarget && !root.contains(event.relatedTarget)) close();
      });
    },

    // The text of a field's label, for a name a script gives a control it adds.
    label: function (frame) {
      return frame.getAttribute('data-pad-label') || '';
    }
  };

  function start(frame) {
    if (frame.padInputReady) return;
    var init = kinds[frame.getAttribute('data-pad-input')];
    if (!init) return;
    var cfg = {};
    try { cfg = JSON.parse(frame.getAttribute('data-config') || '{}') || {}; } catch (e) { cfg = {}; }
    frame.padInputReady = true;
    frame.classList.add('is-ready');
    init(frame, cfg, kit);
  }

  function scan(where) {
    var list = (where || document).querySelectorAll('.pad-input[data-pad-input]');
    for (var i = 0; i < list.length; i++) start(list[i]);
  }

  window.PadInputs = {
    register: function (name, init) { kinds[name] = init; scan(); },
    scan: scan,
    kit: kit
  };

  document.addEventListener('DOMContentLoaded', function () { scan(); });

  if (window.MutationObserver)
    new MutationObserver(function (records) {
      for (var i = 0; i < records.length; i++)
        for (var j = 0; j < records[i].addedNodes.length; j++) {
          var n = records[i].addedNodes[j];
          if (n.nodeType === 1 && (n.matches('.pad-input') || n.querySelector('.pad-input'))) { scan(); return; }
        }
    }).observe(document.documentElement, { childList: true, subtree: true });
})();
