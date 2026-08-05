;;; pad-mode.el --- Major mode for PAD templates -*- lexical-binding: t; -*-

;; Derived from mhtml-mode; adds font-lock for PAD constructs.
;; Install: (add-to-list 'load-path "~/pad/editors/emacs")
;;          (require 'pad-mode)

(require 'mhtml-mode)

(defconst pad-font-lock-keywords
  `(;; {-- comment --} (single-line; multiline handled acceptably by refontify)
    ("{--\\(?:.\\|\n\\)*?--}" . font-lock-comment-face)
    ;; @page@ etc.
    ("@\\(?:page\\|content\\|start\\|end\\|else\\|tidy\\)@" . font-lock-preprocessor-face)
    ;; braces
    ("\\({\\)[/A-Za-z_$%!@]" 1 font-lock-function-name-face)
    ("\\(}\\)" 1 font-lock-function-name-face)
    ;; tag name (optional / and prefix:)
    ("{\\(/?\\)\\(?:\\([A-Za-z_][A-Za-z0-9_]*\\):\\)?\\([A-Za-z_][A-Za-z0-9_]*\\)"
     (2 font-lock-type-face nil t) (3 font-lock-keyword-face))
    ;; variables: sigil + name
    ("\\([$%]\\)\\([A-Za-z_][A-Za-z0-9_.]*\\)"
     (1 font-lock-warning-face) (2 font-lock-variable-name-face))
    ;; properties first@items
    ("\\<\\([A-Za-z_][A-Za-z0-9_]*@[A-Za-z_][A-Za-z0-9_]*\\)\\>" . font-lock-builtin-face)
    ;; word operators
    ("\\<\\(?:eq\\|ne\\|gt\\|lt\\|ge\\|le\\|and\\|or\\|xor\\|not\\|range\\)\\>"
     . font-lock-keyword-face))
  "Extra font-lock rules for PAD constructs.")

;;;###autoload
(define-derived-mode pad-mode mhtml-mode "PAD"
  "Major mode for PAD template files (HTML plus PAD tags)."
  (font-lock-add-keywords nil pad-font-lock-keywords)
  (setq-local comment-start "{-- ")
  (setq-local comment-end " --}"))

;;;###autoload
(add-to-list 'auto-mode-alist '("\\.pad\\'" . pad-mode))

(provide 'pad-mode)
;;; pad-mode.el ends here
