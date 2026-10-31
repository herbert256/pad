/* signature - the canvas: strokes drawn with the mouse, a pen or a finger (pointer events,
   the pressure of a pen in the line's width), or a typed name drawn in a hand of writing. The
   picture is posted as a PNG data URL in the hidden field after every stroke and every typed
   letter, and the field says it changed; Clear empties both. A canvas is drawn in the pixels
   of the screen, so a signature is as sharp on a phone as on paper, and posted at the size
   width= and height= give. A signature that came back with the post is drawn again. */
PadInputs.register('signature', function (frame, cfg, kit) {
  var pad = frame.querySelector('.pi-signature-pad');
  var canvas = frame.querySelector('.pi-signature-canvas');
  var bar = frame.querySelector('.pi-signature-bar');
  var typed = frame.querySelector('.pi-signature-typed');
  var clear = frame.querySelector('.pi-signature-clear');
  var hidden = frame.querySelector('input[type=hidden]');
  if (!canvas || !hidden || !canvas.getContext) return;

  var width = canvas.width, height = canvas.height;
  var ratio = Math.max(1, Math.min(3, window.devicePixelRatio || 1));
  canvas.width = width * ratio;
  canvas.height = height * ratio;
  canvas.style.width = width + 'px';
  canvas.style.aspectRatio = width + ' / ' + height;
  var ink = canvas.getContext('2d');
  ink.scale(ratio, ratio);
  ink.lineCap = 'round';
  ink.lineJoin = 'round';
  ink.strokeStyle = '#1a1a19';
  ink.fillStyle = '#1a1a19';

  var drawn = false, drawing = false, last = null, wasTyped = false;

  pad.hidden = false;
  if (bar) bar.hidden = false;

  // The picture posted at its own size: a copy of the screen's pixels scaled to width= by
  // height=, so the post is the same on every screen.
  function post() {
    if (!drawn) {
      hidden.value = '';
    } else {
      var out = document.createElement('canvas');
      out.width = width;
      out.height = height;
      out.getContext('2d').drawImage(canvas, 0, 0, width, height);
      hidden.value = out.toDataURL('image/png');
    }
    kit.changed(hidden);
  }

  function wipe() {
    ink.clearRect(0, 0, width, height);
    drawn = false;
  }

  function at(event) {
    var box = canvas.getBoundingClientRect();
    return { x: (event.clientX - box.left) * width / box.width, y: (event.clientY - box.top) * height / box.height };
  }

  canvas.addEventListener('pointerdown', function (event) {
    if (event.button !== undefined && event.button > 0) return;
    if (wasTyped) { wipe(); wasTyped = false; if (typed) typed.value = ''; }
    drawing = true;
    last = at(event);
    canvas.setPointerCapture(event.pointerId);
    ink.beginPath();
    ink.arc(last.x, last.y, 1.1, 0, Math.PI * 2);
    ink.fill();
    drawn = true;
    event.preventDefault();
  });

  canvas.addEventListener('pointermove', function (event) {
    if (!drawing) return;
    var points = event.getCoalescedEvents ? event.getCoalescedEvents() : [event];
    if (!points.length) points = [event];
    points.forEach(function (one) {
      var now = at(one);
      ink.lineWidth = one.pointerType === 'pen' && one.pressure ? 0.8 + one.pressure * 3 : 2.2;
      ink.beginPath();
      ink.moveTo(last.x, last.y);
      ink.lineTo(now.x, now.y);
      ink.stroke();
      last = now;
    });
    event.preventDefault();
  });

  function end() {
    if (!drawing) return;
    drawing = false;
    post();
  }
  canvas.addEventListener('pointerup', end);
  canvas.addEventListener('pointercancel', end);

  if (typed) typed.addEventListener('input', function () {
    wipe();
    var name = typed.value.trim();
    if (name) {
      var size = Math.min(height * 0.42, width * 1.6 / Math.max(4, name.length));
      ink.font = 'italic ' + Math.round(size) + 'px "Snell Roundhand", "Segoe Script", "Brush Script MT", "Apple Chancery", cursive';
      ink.textAlign = 'center';
      ink.textBaseline = 'middle';
      ink.fillText(name, width / 2, height / 2, width - 24);
      drawn = true;
    }
    wasTyped = true;
    post();
  });

  if (clear) clear.addEventListener('click', function () {
    wipe();
    if (typed) typed.value = '';
    wasTyped = false;
    post();
    (typed || canvas).focus();
  });

  // The signature of a post that came back - a PNG of this field, drawn on the canvas again.
  if (/^data:image\/png;base64,/.test(hidden.value)) {
    var picture = new Image();
    picture.onload = function () { ink.drawImage(picture, 0, 0, width, height); drawn = true; };
    picture.src = hidden.value;
  }
});
