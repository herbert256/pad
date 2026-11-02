PadToys.register('sorting', function (k, cfg) {
  var NAMES = { bubble: 'Bubble sort', cocktail: 'Cocktail shaker', insertion: 'Insertion sort', selection: 'Selection sort', gnome: 'Gnome sort',
                shell: 'Shellsort', comb: 'Comb sort', quick: 'Quicksort', merge: 'Merge sort', heap: 'Heapsort', radix: 'Radix sort' };
  var algorithm = cfg.algorithm, bars = cfg.bars, order = cfg.order, speed = cfg.speed, race = cfg.race, sound = cfg.sound;
  var panels = [], data = [], finished = 0, places = [];

  function swap(a, i, j) { var t = a[i]; a[i] = a[j]; a[j] = t; }

  // The algorithms, as generators over the bars: each yields what it just did - c compared
  // two bars, s swapped them, w wrote one - so the panel can count it, light it up and wait
  // for the next frame.
  var SORTS = {
    bubble: function* (a) {
      for (var end = a.length - 1; end > 0; end--) {
        var swapped = false;
        for (var i = 0; i < end; i++) { yield ['c', i, i + 1]; if (a[i] > a[i + 1]) { swap(a, i, i + 1); swapped = true; yield ['s', i, i + 1]; } }
        if (!swapped) break;
      }
    },
    cocktail: function* (a) {
      var lo = 0, hi = a.length - 1, i, swapped = true;
      while (swapped && lo < hi) {
        swapped = false;
        for (i = lo; i < hi; i++) { yield ['c', i, i + 1]; if (a[i] > a[i + 1]) { swap(a, i, i + 1); swapped = true; yield ['s', i, i + 1]; } }
        hi--;
        for (i = hi; i > lo; i--) { yield ['c', i - 1, i]; if (a[i - 1] > a[i]) { swap(a, i - 1, i); swapped = true; yield ['s', i - 1, i]; } }
        lo++;
      }
    },
    insertion: function* (a) {
      for (var i = 1; i < a.length; i++)
        for (var j = i; j > 0; j--) { yield ['c', j - 1, j]; if (a[j - 1] > a[j]) { swap(a, j - 1, j); yield ['s', j - 1, j]; } else break; }
    },
    selection: function* (a) {
      for (var i = 0; i < a.length - 1; i++) {
        var min = i;
        for (var j = i + 1; j < a.length; j++) { yield ['c', min, j]; if (a[j] < a[min]) min = j; }
        if (min !== i) { swap(a, i, min); yield ['s', i, min]; }
      }
    },
    gnome: function* (a) {
      var i = 0;
      while (i < a.length) {
        if (i === 0) { i++; continue; }
        yield ['c', i - 1, i];
        if (a[i - 1] <= a[i]) i++;
        else { swap(a, i - 1, i); yield ['s', i - 1, i]; i--; }
      }
    },
    shell: function* (a) {
      var gaps = [701, 301, 132, 57, 23, 10, 4, 1];
      for (var g = 0; g < gaps.length; g++) {
        var gap = gaps[g];
        if (gap >= a.length) continue;
        for (var i = gap; i < a.length; i++)
          for (var j = i; j >= gap; j -= gap) { yield ['c', j - gap, j]; if (a[j - gap] > a[j]) { swap(a, j - gap, j); yield ['s', j - gap, j]; } else break; }
      }
    },
    comb: function* (a) {
      var gap = a.length, sorted = false;
      while (!sorted) {
        gap = Math.floor(gap / 1.3);
        if (gap <= 1) { gap = 1; sorted = true; }
        for (var i = 0; i + gap < a.length; i++) { yield ['c', i, i + gap]; if (a[i] > a[i + gap]) { swap(a, i, i + gap); sorted = false; yield ['s', i, i + gap]; } }
      }
    },
    quick: function* (a) {
      var stack = [[0, a.length - 1]];
      while (stack.length) {
        var r = stack.pop(), lo = r[0], hi = r[1];
        if (lo >= hi) continue;
        var mid = (lo + hi) >> 1;
        if (mid !== hi) { swap(a, mid, hi); yield ['s', mid, hi]; }
        var p = lo;
        for (var i = lo; i < hi; i++) { yield ['c', i, hi]; if (a[i] < a[hi]) { if (i !== p) { swap(a, i, p); yield ['s', i, p]; } p++; } }
        if (p !== hi) { swap(a, p, hi); yield ['s', p, hi]; }
        stack.push([p + 1, hi], [lo, p - 1]);
      }
    },
    merge: function* (a) {
      var aux = a.slice();
      for (var width = 1; width < a.length; width *= 2)
        for (var lo = 0; lo < a.length - width; lo += 2 * width) {
          var mid = lo + width, hi = Math.min(lo + 2 * width, a.length), i = lo, j = mid;
          for (var x = lo; x < hi; x++) aux[x] = a[x];
          for (var n = lo; n < hi; n++) {
            if (i < mid && j < hi) { yield ['c', i, j]; a[n] = aux[i] <= aux[j] ? aux[i++] : aux[j++]; }
            else a[n] = i < mid ? aux[i++] : aux[j++];
            yield ['w', n];
          }
        }
    },
    heap: function* (a) {
      var n = a.length;
      function* sift(start, end) {
        var root = start;
        while (root * 2 + 1 <= end) {
          var child = root * 2 + 1, top = root;
          yield ['c', top, child];
          if (a[top] < a[child]) top = child;
          if (child + 1 <= end) { yield ['c', top, child + 1]; if (a[top] < a[child + 1]) top = child + 1; }
          if (top === root) return;
          swap(a, root, top);
          yield ['s', root, top];
          root = top;
        }
      }
      for (var s = (n >> 1) - 1; s >= 0; s--) yield* sift(s, n - 1);
      for (var end = n - 1; end > 0; end--) { swap(a, 0, end); yield ['s', 0, end]; yield* sift(0, end - 1); }
    },
    radix: function* (a) {
      var max = Math.max.apply(null, a);
      for (var exp = 1; Math.floor(max / exp) > 0; exp *= 10) {
        var buckets = [[], [], [], [], [], [], [], [], [], []];
        for (var i = 0; i < a.length; i++) { buckets[Math.floor(a[i] / exp) % 10].push(a[i]); yield ['r', i]; }
        var n = 0;
        for (var b = 0; b < 10; b++) for (var j = 0; j < buckets[b].length; j++) { a[n] = buckets[b][j]; yield ['w', n]; n++; }
      }
    }
  };

  function makeData() {
    var a = [], i;
    for (i = 1; i <= bars; i++) a.push(order === 'few' ? Math.round(Math.ceil(i / bars * 4) / 4 * bars) : i);
    if (order === 'reversed') a.reverse();
    else if (order === 'nearly') for (i = 0; i < Math.max(1, bars / 12); i++) { var x = k.int(bars - 3); swap(a, x, x + 1 + k.int(3)); }
    else k.shuffle(a);
    return a;
  }

  // A panel is one algorithm on its own copy of the bars, on a canvas of its own.
  function makePanels() {
    k.stage.textContent = '';
    panels = [];
    finished = 0;
    places = [];
    data = makeData();
    var list = race ? cfg.algorithms : [algorithm], w = race ? 208 : 640, h = race ? 116 : 300;
    list.forEach(function (name) {
      var ctx = k.canvas(w, h, NAMES[name], false);
      panels.push({ name: name, ctx: ctx, w: w, h: h, a: data.slice(), gen: null, cmp: 0, swp: 0, wr: 0, hot: [], kind: '', done: false, sweep: -1, acc: 0, place: 0 });
    });
    panels.forEach(function (p) { p.gen = SORTS[p.name](p.a); draw(p); describe(p); });
    k.info(race ? '' : counts(panels[0]));
  }

  function counts(p) { return p.cmp.toLocaleString('en') + ' comparisons · ' + (p.swp ? p.swp.toLocaleString('en') + ' swaps' : p.wr.toLocaleString('en') + ' writes'); }
  function describe(p) {
    p.ctx.canvas.setAttribute('aria-label', NAMES[p.name] + ' on ' + bars + ' bars: ' + (p.done ? 'sorted, ' : '') + counts(p));
  }

  function advance(p) {
    if (p.done) return;
    var r = p.gen.next();
    if (r.done) {
      p.done = true;
      p.hot = [];
      p.sweep = 0;
      p.place = ++finished;
      places.push(p);
      describe(p);
      if (finished === panels.length) over();
      return;
    }
    var op = r.value;
    p.kind = op[0];
    p.hot = op.slice(1);
    if (op[0] === 'c') p.cmp++;
    else if (op[0] === 's') p.swp++;
    else if (op[0] === 'w') p.wr++;
    if (sound && panels.length === 1) tone(p.a[op[1]] / bars);
  }

  function over() {
    k.pause();
    playLabel();
    if (panels.length === 1) k.status(NAMES[panels[0].name] + ' sorted ' + bars + ' bars with ' + counts(panels[0]));
    else k.status('Race over - ' + places.slice(0, 3).map(function (p, i) { return (i + 1) + '. ' + NAMES[p.name]; }).join(', ') + ' ... ' + NAMES[places[places.length - 1].name] + ' last');
  }

  function draw(p) {
    var ctx = p.ctx, n = p.a.length, top = race ? 30 : 6, bw = p.w / n, gap = bw > 4 ? 1 : 0;
    ctx.fillStyle = k.color('canvas');
    ctx.fillRect(0, 0, p.w, p.h);
    var base = k.color('accent'), hot = p.kind === 'c' || p.kind === 'r' ? k.color('mark') : k.color('bad'), good = k.color('good');
    for (var i = 0; i < n; i++) {
      var h = p.a[i] / bars * (p.h - top - 2);
      ctx.fillStyle = (p.done && i <= p.sweep) ? good : (p.hot.indexOf(i) >= 0 ? hot : base);
      ctx.fillRect(i * bw, p.h - h, Math.max(1, bw - gap), h);
    }
    if (race) {
      ctx.fillStyle = k.color('ink');
      ctx.font = '600 11px system-ui, sans-serif';
      ctx.fillText(NAMES[p.name] + (p.place ? '  #' + p.place : ''), 4, 12);
      ctx.fillStyle = k.color('muted');
      ctx.font = '10px system-ui, sans-serif';
      ctx.fillText(p.cmp + ' comparisons · ' + (p.swp || p.wr) + (p.swp ? ' swaps' : ' writes'), 4, 25);
    }
  }

  k.loop(function (dt) {
    panels.forEach(function (p) {
      if (p.done) { if (p.sweep < p.a.length) p.sweep += Math.max(1, p.a.length * dt * 1.5); }
      else {
        p.acc += dt * speed;
        var steps = Math.floor(p.acc);
        p.acc -= steps;
        while (steps-- > 0 && !p.done) advance(p);
      }
      draw(p);
    });
    if (!race) k.info(counts(panels[0]));
  }, function () { panels.forEach(draw); });

  // After the last bar is sorted the sweep of green still runs a moment; the toy is paused,
  // so a frame loop of its own finishes it.
  k.onPause = function () {
    if (finished && finished === panels.length) {
      var t0 = performance.now();
      (function finish(now) {
        var dt = Math.min(0.05, (now - t0) / 1000); t0 = now;
        var more = false;
        panels.forEach(function (p) { if (p.sweep < p.a.length) { p.sweep += Math.max(1, p.a.length * dt * 1.5); more = true; } draw(p); });
        if (more && !k.playing) requestAnimationFrame(finish);
      })(t0);
    } else k.status('Paused - ' + (race ? 'the race' : NAMES[algorithm]) + ' waits');
  };
  k.onPlay = function () {
    if (finished && finished === panels.length) makePanels();
    k.status(race ? 'Racing ' + panels.length + ' algorithms on the same ' + bars + ' bars' : NAMES[algorithm] + ' on ' + bars + ' bars');
  };
  function playLabel() { if (k.playBtn) k.playBtn.textContent = finished === panels.length ? 'Again' : (k.playing ? 'Pause' : 'Play'); }

  // Sound: a short note for each bar touched, higher for a taller one, at most one every
  // thirty milliseconds - made in the page, nothing fetched.
  var audio = null, lastTone = 0;
  function tone(f) {
    if (!audio) return;
    var now = audio.currentTime;
    if (now - lastTone < 0.03) return;
    lastTone = now;
    var o = audio.createOscillator(), g = audio.createGain();
    o.type = 'triangle';
    o.frequency.value = 180 + f * 900;
    g.gain.setValueAtTime(0.06, now);
    g.gain.exponentialRampToValueAtTime(0.0001, now + 0.08);
    o.connect(g); g.connect(audio.destination);
    o.start(now); o.stop(now + 0.09);
  }
  function soundOn(v) {
    sound = v;
    if (v && !audio && (window.AudioContext || window.webkitAudioContext)) audio = new (window.AudioContext || window.webkitAudioContext)();
    if (audio && audio.state === 'suspended') audio.resume();
  }

  function restart(say) { k.pause(); makePanels(); playLabel(); if (say) k.status(say); }
  function stepOnce() {
    k.pause();
    if (finished === panels.length) makePanels();
    panels.forEach(function (p) { advance(p); draw(p); });
    if (!race) k.info(counts(panels[0]));
    playLabel();
  }

  k.keys({ s: stepOnce, n: stepOnce, r: function () { restart('New bars'); } });
  k.row();
  k.playButton();
  k.button('Step', stepOnce, 'One comparison or swap (S)');
  k.button('Shuffle', function () { restart('New bars - press Play'); }, 'New bars (R)');
  var algSelect = k.select('Algorithm', cfg.algorithms.map(function (n) { return [n, NAMES[n]]; }), algorithm, function (v) { algorithm = v; restart(NAMES[v] + ' - press Play'); });
  k.check('Race them all', race, function (v) { race = v; algSelect.querySelector('select').disabled = v; restart(v ? 'All ' + cfg.algorithms.length + ' algorithms on the same bars - press Play' : NAMES[algorithm] + ' - press Play'); });
  algSelect.querySelector('select').disabled = race;
  k.row();
  k.select('Start', [['random', 'Random'], ['reversed', 'Reversed'], ['nearly', 'Nearly sorted'], ['few', 'Few heights']], order, function (v) { order = v; restart('New bars, ' + v + ' - press Play'); });
  k.slider('Bars', 8, 300, 1, bars, function (v) { bars = v; restart(); });
  var levels = [1, 5, 15, 40, 120, 300, 1000, 5000], at = 0;
  levels.forEach(function (v, i) { if (Math.abs(Math.log(v) - Math.log(speed)) < Math.abs(Math.log(levels[at]) - Math.log(speed))) at = i; });
  k.slider('Speed', 0, levels.length - 1, 1, at, function (v) { speed = levels[v]; }, function (v) { return levels[v] + '/s'; });
  k.check('Sound', sound, soundOn);
  k.theme(function () { panels.forEach(draw); });

  // A browser lets a page make sound only after the visitor did something on it: sound=
  // asked for it, so the first press or key in the toy switches it on.
  if (sound) {
    var wake = function () { soundOn(true); k.root.removeEventListener('pointerdown', wake); k.root.removeEventListener('keydown', wake); };
    k.root.addEventListener('pointerdown', wake);
    k.root.addEventListener('keydown', wake);
  }

  makePanels();
  k.begin();
});
