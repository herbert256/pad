; The text around the tags, and the content of {ignore}, is HTML - one document, so a
; <script> or <style> split by tags still reads as one.

(([
  (text)
  (raw_text)
] @injection.content)
  (#set! injection.language "html")
  (#set! injection.combined))
