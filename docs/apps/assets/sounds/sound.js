/* The sounds of PAD (pad/subs/sounds/): every recipe the page may play, rendered to samples
   by the same arithmetic as padSoundRender in pad/subs/sounds/_lib/sound.php - change one,
   change both - and played through WebAudio. PadSound.add hands over the settled recipes and
   the events; an element with data-pad-sound="<kind>" plays on its event (data-pad-sound-on,
   click without it), a binding of {sound target=...} plays for the elements its selector
   finds, an element with data-pad-sound-play once as it appears - a toast. Browsers let a
   page play audio after a gesture on it: the AudioContext is made or resumed on the first
   pointerdown or key. PadSound.play ( kind, volume ) for a page's own script, PadSound.render
   ( kind ) the samples, as Int16Array. */
(function () {
  if (window.PadSound) return;
  var RATE = 22050, recipes = {}, buffers = {}, events = {}, listening = {}, context = null;

  function render(recipe) {
    var count = Math.floor(recipe.length * RATE + 0.5), mix = new Float64Array(Math.max(1, count)), i;
    recipe.voices.forEach(function (voice) { renderVoice(voice, mix); });
    var peak = 0;
    for (i = 0; i < mix.length; i++) if (Math.abs(mix[i]) > peak) peak = Math.abs(mix[i]);
    var scale = peak > 0 ? recipe.level / peak : 0, out = new Int16Array(mix.length);
    for (i = 0; i < mix.length; i++) {
      var q = Math.floor(mix[i] * scale * 32767 + 0.5);
      out[i] = q > 32767 ? 32767 : (q < -32767 ? -32767 : q);
    }
    return out;
  }

  function renderVoice(voice, mix) {
    var length = voice.length, notes = voice.notes, span = notes[notes.length - 1][0] + length,
        size = Math.floor(span * RATE + 0.5), buffer = new Float64Array(Math.max(1, size)),
        state = voice.seed, wave = voice.wave, glide = voice.glide, attack = voice.attack,
        decay = voice.decay, sustain = voice.sustain, release = Math.min(voice.release, length),
        gain = voice.gain, duty = voice.duty, vibRate = voice.vibrato[0], vibDeep = voice.vibrato[1],
        twoPi = 2 * Math.PI, n, i;
    for (n = 0; n < notes.length; n++) {
      var at = notes[n][0], from = notes[n][1], to = notes[n][2],
          first = Math.floor(at * RATE + 0.5), count = Math.floor(length * RATE + 0.5),
          phase = 0, hold = 0, held = 1;
      for (i = 0; i < count && first + i < size; i++) {
        var t = i / RATE, f = from, x, e, d;
        if (to != from && from > 0) f = from * Math.pow(to / from, Math.min(t / glide, 1));
        if (vibDeep != 0) f = f * Math.pow(2, vibDeep / 12 * Math.sin(twoPi * vibRate * t));
        if (wave === 'sine') x = Math.sin(twoPi * phase);
        else if (wave === 'square') x = (phase < duty ? 1 : -1) - (2 * duty - 1);
        else if (wave === 'saw') x = 2 * phase - 1;
        else if (wave === 'triangle') x = phase < 0.5 ? 4 * phase - 1 : 3 - 4 * phase;
        else if (from <= 0) {
          state ^= state << 13; state ^= state >>> 17; state ^= state << 5; state >>>= 0;
          x = state / 2147483648 - 1;
        } else {
          if (held >= 1) {
            state ^= state << 13; state ^= state >>> 17; state ^= state << 5; state >>>= 0;
            hold = state / 2147483648 - 1;
            held = held - Math.floor(held);
          }
          x = hold;
          held = held + f / RATE;
        }
        phase = phase + f / RATE;
        phase = phase - Math.floor(phase);
        if (t < attack) e = t / attack;
        else if (decay > 0 && t < attack + decay) {
          d = 1 - (t - attack) / decay;
          e = sustain + (1 - sustain) * d * d;
        } else e = sustain;
        if (release > 0 && t > length - release) e = e * Math.max(0, (length - t) / release);
        buffer[first + i] += x * e * gain;
      }
    }
    if (voice.filter) filter(buffer, voice.filter);
    var offset = Math.floor(voice.start * RATE + 0.5);
    for (i = 0; i < buffer.length; i++) if (offset + i < mix.length) mix[offset + i] += buffer[i];
  }

  function filter(buffer, spec) {
    var type = spec[0], from = spec[1], to = spec[2], q = spec[3], size = buffer.length,
        x1 = 0, x2 = 0, y1 = 0, y2 = 0, b0 = 0, b1 = 0, b2 = 0, a1 = 0, a2 = 0, i;
    for (i = 0; i < size; i++) {
      if (i == 0 || to != from) {
        var fc = to != from ? from * Math.pow(to / from, i / size) : from;
        fc = Math.min(Math.max(fc, 20), RATE * 0.45);
        var w = 2 * Math.PI * fc / RATE, cs = Math.cos(w), alpha = Math.sin(w) / (2 * q), a0 = 1 + alpha;
        if (type === 'lowpass') { b0 = (1 - cs) / 2; b1 = 1 - cs; b2 = b0; }
        else if (type === 'highpass') { b0 = (1 + cs) / 2; b1 = -(1 + cs); b2 = b0; }
        else { b0 = alpha; b1 = 0; b2 = -alpha; }
        b0 = b0 / a0; b1 = b1 / a0; b2 = b2 / a0;
        a1 = -2 * cs / a0; a2 = (1 - alpha) / a0;
      }
      var x = buffer[i], y = b0 * x + b1 * x1 + b2 * x2 - a1 * y1 - a2 * y2;
      x2 = x1; x1 = x; y2 = y1; y1 = y;
      buffer[i] = y;
    }
  }

  function audio() {
    if (!context) {
      var Context = window.AudioContext || window.webkitAudioContext;
      if (!Context) return null;
      context = new Context();
    }
    return context;
  }

  function samples(kind) {
    if (!buffers[kind]) {
      var made = render(recipes[kind]), ctx = audio(), buffer = ctx.createBuffer(1, made.length, RATE),
          data = buffer.getChannelData(0);
      for (var i = 0; i < made.length; i++) data[i] = made[i] / 32767;
      buffers[kind] = buffer;
    }
    return buffers[kind];
  }

  function start(kind, volume) {
    var ctx = audio(), source = ctx.createBufferSource(), gain = ctx.createGain();
    source.buffer = samples(kind);
    gain.gain.value = volume;
    source.connect(gain);
    gain.connect(ctx.destination);
    source.start();
  }

  // Played now, or - an AudioContext still waiting for the visitor's first gesture - when it
  // is let go within a moment; a sound asked for long before that would come too late.

  function play(kind, volume) {
    var ctx = audio(), asked = Date.now();
    if (!ctx || !recipes[kind]) return;
    volume = (volume == null || isNaN(volume)) ? 1 : Math.max(0, Math.min(1, volume));
    if (ctx.state === 'running') return start(kind, volume);
    ctx.resume().then(function () { if (Date.now() - asked < 300) start(kind, volume); }, function () {});
  }

  function volumeOf(element) {
    var value = element.getAttribute('data-pad-sound-volume');
    return value === null ? 1 : parseFloat(value);
  }

  function unlock() {
    var ctx = audio();
    if (ctx && ctx.state === 'suspended') ctx.resume().catch(function () {});
  }

  function handler(name) {
    return function (e) {
      var target = e.target;
      if (!target || !target.closest) return;
      var own = target.closest('[data-pad-sound]');
      if (own && (own.getAttribute('data-pad-sound-on') || 'click') === name
          && !(name === 'hover' && own.contains(e.relatedTarget)))
        play(own.getAttribute('data-pad-sound'), volumeOf(own));
      document.querySelectorAll('[data-pad-sound-bind]').forEach(function (bind) {
        if (bind.getAttribute('data-pad-sound-on') !== name) return;
        var hit = null;
        try { hit = target.closest(bind.getAttribute('data-pad-sound-target')); } catch (x) { return; }
        if (hit && !(name === 'hover' && hit.contains(e.relatedTarget)))
          play(bind.getAttribute('data-pad-sound-bind'), volumeOf(bind));
      });
    };
  }

  function scan() {
    document.querySelectorAll('[data-pad-sound-play]').forEach(function (element) {
      if (element.padSoundPlayed) return;
      element.padSoundPlayed = true;
      play(element.getAttribute('data-pad-sound-play'), volumeOf(element));
    });
  }

  function add(more, names) {
    for (var kind in more) recipes[kind] = more[kind];
    for (var name in names) {
      events[name] = names[name];
      if (listening[name]) continue;
      listening[name] = true;
      document.addEventListener(names[name], handler(name), true);
    }
  }

  document.addEventListener('pointerdown', unlock, true);
  document.addEventListener('keydown', unlock, true);
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', scan);
  else setTimeout(scan, 0);
  new MutationObserver(scan).observe(document.documentElement, { childList: true, subtree: true });

  window.PadSound = {
    add: add,
    play: play,
    recipes: recipes,
    render: function (kind) { return recipes[kind] ? render(recipes[kind]) : null; }
  };
})();
