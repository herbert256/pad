"""Optional independent decoder checks: numpy, pillow, zxing-cpp and ImageMagick.
Run: python tests/subsystems/code-specialized-oracle.py
Golden Zint revision and supported encoding contracts: docs/subsystems/codes/specialized.md.
The MaxiCode checks rasterize the actual SVG, including hexagons and finder rings.
"""
from pathlib import Path
import base64
import json
import random
import subprocess
import tempfile
import numpy as np
from PIL import Image
import zxingcpp

root = Path(__file__).resolve().parents[2]
fixtures = json.loads((Path(__file__).parent / 'fixtures/specialized-codes-zint.json').read_text())
cases = [{k: f[k] for k in ('kind', 'text', 'options')} for f in fixtures]
rng = random.Random(20261102)
for i in range(100):
    text = ''.join(rng.choice('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789 éä£') for _ in range(rng.randint(1, 35)))
    cases.append({'kind': 'maxicode', 'text': text, 'options': {'mode': rng.choice([4, 5])}})
    cases.append({'kind': 'rmqr', 'text': text, 'options': {'version': 'R17x139', 'level': rng.choice(['M', 'H'])}})
for text in ['漢字 Καλημέρα', '🙂 café', 'line one\nline two', 'مرحبا']:
    for kind in ['rmqr', 'maxicode']:
        options = {'encoding': 'UTF-8'}
        if kind == 'rmqr': options.update(version='R17x139', level='M')
        cases.append({'kind': kind, 'text': text, 'options': options})
# Every raw byte, including controls, must survive the single-shift encoder.
for offset in range(0, 256, 16):
    cases.append({'kind': 'maxicode', 'base64': base64.b64encode(bytes(range(offset, offset + 16))).decode(), 'options': {'encoding': 'binary'}})
php = """define('PAD',$argv[1].'/pad/');function padUnprotectQuotes($v){return $v;}
require PAD.'subs/codes/_lib/rmqr.php';require PAD.'subs/codes/_lib/maxicode.php';$out=[];
foreach(json_decode(stream_get_contents(STDIN),true)as$c){$text=isset($c['base64'])?base64_decode($c['base64']):$c['text'];
$code=$c['kind']==='rmqr'?padRmqrEncode($text,$c['options']):padMaxicodeEncode($text,$c['options']);
if(is_array($code)&&$c['kind']==='maxicode')$code['svg']=padMaxicodeSvg($code,640,'Decoder fixture');$out[]=$code;}
echo json_encode($out,JSON_THROW_ON_ERROR);"""
result = subprocess.run(['php', '-r', php, str(root)], input=json.dumps(cases), text=True, capture_output=True, check=True)
assert not result.stderr, result.stderr
codes = json.loads(result.stdout)
checks = 0
with tempfile.TemporaryDirectory(prefix='pad-specialized-codes-') as temporary:
    directory = Path(temporary)
    for i, (case, code) in enumerate(zip(cases, codes)):
        assert isinstance(code, dict), (i, code)
        if i < len(fixtures): assert [''.join(map(str, row)) for row in code['matrix']] == fixtures[i]['matrix'], i
        options = case['options']
        expected = base64.b64decode(case['base64']) if 'base64' in case else case['text'].encode('utf8' if options.get('encoding') == 'UTF-8' else 'latin1')
        if case['kind'] == 'rmqr':
            matrix = np.pad(np.asarray(code['matrix'], dtype=np.uint8), 2)
            for scale in (2, 4, 8):
                pixels = np.repeat(np.repeat((1 - matrix) * 255, scale, axis=0), scale, axis=1)
                for rotation in range(4):
                    found = zxingcpp.read_barcode(np.rot90(pixels, rotation).copy(), formats=zxingcpp.BarcodeFormat.RMQRCode)
                    assert found is not None and found.bytes == expected, (i, scale, rotation, case, found)
                    checks += 1
        else:
            if options.get('mode', 4) in (2, 3):
                postcode = options['postcode']
                if options['mode'] == 2 and options['country'] == 840 and len(postcode) == 5: postcode += '0000'
                if options['mode'] == 3: postcode = postcode.ljust(6)
                expected = f"{postcode}\x1d{options['country']:03d}\x1d{options['service']:03d}\x1d".encode() + expected
            svg = directory / 'symbol.svg'
            png = directory / 'symbol.png'
            svg.write_text(code['svg'])
            # MSVG selects ImageMagick's native SVG renderer; no Inkscape delegate.
            subprocess.run(['magick', 'MSVG:' + str(svg), str(png)], check=True, capture_output=True)
            image = Image.open(png).convert('RGB')
            for size in (160, 320, 640):
                scaled = image.resize((size, round(image.height * size / image.width)), Image.Resampling.LANCZOS)
                for rotation in (0, 90, 180, 270):
                    rotated = scaled.rotate(rotation, expand=True)
                    # ZXing's MaxiCode detector expects an upright symbol (confirmed
                    # on Zint-generated PNGs too), so use an explicit four-orientation
                    # scanner adapter; unlike rMQR this is not native auto-rotation.
                    found = None
                    for attempt in (0, 90, 180, 270):
                        candidate = zxingcpp.read_barcode(rotated.rotate(attempt, expand=True), formats=zxingcpp.BarcodeFormat.MaxiCode)
                        if candidate is not None:
                            found = candidate
                            break
                    assert found is not None and found.bytes == expected, (i, size, rotation, case, None if found is None else found.bytes)
                    checks += 1
print(f'Specialized codes: {len(cases)} payloads; {checks} ZXing decodes passed (rMQR native rotation; MaxiCode explicit orientation adapter)')
