<?php
// Generated from polish.sbl by Snowball 3.2.0 - https://snowballstem.org/

class PadSearchPolishStemmer extends PadSearchGeneratedStemmer
{
    private const A_0 = [
        ["by\u{15B}cie", -1, 1],
        ["bym", -1, 1],
        ["by", -1, 1],
        ["by\u{15B}my", -1, 1],
        ["by\u{15B}", -1, 1]
    ];

    private const A_1 = [
        ["\u{105}c", -1, 1],
        ["aj\u{105}c", 0, 1],
        ["sz\u{105}c", 0, 2],
        ["sz", -1, 1],
        ["iejsz", 3, 1]
    ];

    private const AS_1 = ["", "s"];

    private const A_2 = [
        ["a", -1, 1, 'r_R1'],
        ["\u{105}ca", 0, 1],
        ["aj\u{105}ca", 1, 1],
        ["sz\u{105}ca", 1, 2],
        ["ia", 0, 1, 'r_R1'],
        ["sza", 0, 1],
        ["iejsza", 5, 1],
        ["a\u{142}a", 0, 1],
        ["ia\u{142}a", 7, 1],
        ["i\u{142}a", 0, 1],
        ["\u{105}c", -1, 1],
        ["aj\u{105}c", 10, 1],
        ["e", -1, 1, 'r_R1'],
        ["\u{105}ce", 12, 1],
        ["aj\u{105}ce", 13, 1],
        ["sz\u{105}ce", 13, 2],
        ["ie", 12, 1, 'r_R1'],
        ["cie", 16, 1],
        ["acie", 17, 1],
        ["ecie", 17, 1],
        ["icie", 17, 1],
        ["ajcie", 17, 1],
        ["li\u{15B}cie", 17, 4],
        ["ali\u{15B}cie", 22, 1],
        ["ieli\u{15B}cie", 22, 1],
        ["ili\u{15B}cie", 22, 1],
        ["\u{142}y\u{15B}cie", 17, 4],
        ["a\u{142}y\u{15B}cie", 26, 1],
        ["ia\u{142}y\u{15B}cie", 27, 1],
        ["i\u{142}y\u{15B}cie", 26, 1],
        ["sze", 12, 1],
        ["iejsze", 30, 1],
        ["ach", -1, 1, 'r_R1'],
        ["iach", 32, 1, 'r_R1'],
        ["ich", -1, 5],
        ["ych", -1, 5],
        ["i", -1, 1, 'r_R1'],
        ["ali", 36, 1],
        ["ieli", 36, 1],
        ["ili", 36, 1],
        ["ami", 36, 1, 'r_R1'],
        ["iami", 40, 1, 'r_R1'],
        ["imi", 36, 5],
        ["ymi", 36, 5],
        ["owi", 36, 1, 'r_R1'],
        ["iowi", 44, 1, 'r_R1'],
        ["aj", -1, 1],
        ["ej", -1, 5],
        ["iej", 47, 5],
        ["am", -1, 1],
        ["a\u{142}am", 49, 1],
        ["ia\u{142}am", 50, 1],
        ["i\u{142}am", 49, 1],
        ["em", -1, 1, 'r_R1'],
        ["iem", 53, 1, 'r_R1'],
        ["a\u{142}em", 53, 1],
        ["ia\u{142}em", 55, 1],
        ["i\u{142}em", 53, 1],
        ["im", -1, 5],
        ["om", -1, 1, 'r_R1'],
        ["iom", 59, 1, 'r_R1'],
        ["ym", -1, 5],
        ["o", -1, 1, 'r_R1'],
        ["ego", 62, 5],
        ["iego", 63, 5],
        ["a\u{142}o", 62, 1],
        ["ia\u{142}o", 65, 1],
        ["i\u{142}o", 62, 1],
        ["u", -1, 1, 'r_R1'],
        ["iu", 68, 1, 'r_R1'],
        ["emu", 68, 5],
        ["iemu", 70, 5],
        ["\u{F3}w", -1, 1, 'r_R1'],
        ["y", -1, 5],
        ["amy", 73, 1],
        ["emy", 73, 1],
        ["imy", 73, 1],
        ["li\u{15B}my", 73, 4],
        ["ali\u{15B}my", 77, 1],
        ["ieli\u{15B}my", 77, 1],
        ["ili\u{15B}my", 77, 1],
        ["\u{142}y\u{15B}my", 73, 4],
        ["a\u{142}y\u{15B}my", 81, 1],
        ["ia\u{142}y\u{15B}my", 82, 1],
        ["i\u{142}y\u{15B}my", 81, 1],
        ["a\u{142}y", 73, 1],
        ["ia\u{142}y", 85, 1],
        ["i\u{142}y", 73, 1],
        ["asz", -1, 1],
        ["esz", -1, 1],
        ["isz", -1, 1],
        ["a\u{142}", -1, 1],
        ["ia\u{142}", 91, 1],
        ["i\u{142}", -1, 1],
        ["\u{105}", -1, 1, 'r_R1'],
        ["\u{105}c\u{105}", 94, 1],
        ["aj\u{105}c\u{105}", 95, 1],
        ["sz\u{105}c\u{105}", 95, 2],
        ["i\u{105}", 94, 1, 'r_R1'],
        ["aj\u{105}", 94, 1],
        ["sz\u{105}", 94, 3],
        ["iejsz\u{105}", 100, 1],
        ["a\u{107}", -1, 1],
        ["ie\u{107}", -1, 1],
        ["i\u{107}", -1, 1],
        ["\u{105}\u{107}", -1, 1],
        ["a\u{15B}\u{107}", -1, 1],
        ["e\u{15B}\u{107}", -1, 1],
        ["\u{119}", -1, 1],
        ["sz\u{119}", 108, 2],
        ["\u{142}a\u{15B}", -1, 4],
        ["a\u{142}a\u{15B}", 110, 1],
        ["ia\u{142}a\u{15B}", 111, 1],
        ["i\u{142}a\u{15B}", 110, 1],
        ["\u{142}e\u{15B}", -1, 4],
        ["a\u{142}e\u{15B}", 114, 1],
        ["ia\u{142}e\u{15B}", 115, 1],
        ["i\u{142}e\u{15B}", 114, 1]
    ];

    private const A_3 = [
        ["\u{144}", -1, 2],
        ["\u{107}", -1, 1],
        ["\u{15B}", -1, 3],
        ["\u{17A}", -1, 4]
    ];

    private const AS_3 = ["c", "n", "s", "z"];

    private const G_v = ["a"=>true, "e"=>true, "i"=>true, "o"=>true, "u"=>true, "y"=>true, "\u{F3}"=>true, "\u{105}"=>true, "\u{119}"=>true];

    private int $I_p1 = 0;



    protected function r_R1(): bool
    {
        return $this->I_p1 <= $this->cursor;
    }


    public function stem(): bool
    {
        $v_1 = $this->cursor;
        $this->I_p1 = $this->limit;
        if (!$this->go_out_grouping(self::G_v)) {
            goto lab0;
        }
        $this->inc_cursor();
        if (!$this->go_in_grouping(self::G_v)) {
            goto lab0;
        }
        $this->inc_cursor();
        $this->I_p1 = $this->cursor;
    lab0:
        $this->cursor = $v_1;
        $v_2 = $this->cursor;
        if (!$this->hop(2)) {
            goto lab1;
        }
        $this->limit_backward = $this->cursor;
        $this->cursor = $this->limit;
        $v_3 = $this->limit - $this->cursor;
        if ($this->cursor < $this->I_p1) {
            goto lab2;
        }
        $v_4 = $this->limit_backward;
        $this->limit_backward = $this->I_p1;
        $this->ket = $this->cursor;
        if ($this->find_among_b(self::A_0) === 0) {
            $this->limit_backward = $v_4;
            goto lab2;
        }
        $this->bra = $this->cursor;
        $this->limit_backward = $v_4;
        $this->slice_del();
    lab2:
        $this->cursor = $this->limit - $v_3;
        $this->ket = $this->cursor;
        $among_var = $this->find_among_b(self::A_2);
        if (0 === $among_var) {
            goto lab1;
        }
        $this->bra = $this->cursor;
        switch ($among_var) {
            case 1:
                $this->slice_del();
                break;
            case 2:
                $this->slice_from("s");
                break;
            case 3:
                $v_5 = $this->limit - $this->cursor;
                if ($this->I_p1 > $this->cursor) {
                    goto lab3;
                }
                $this->slice_del();
                goto lab4;
            lab3:
                $this->cursor = $this->limit - $v_5;
                $this->slice_from("s");
            lab4:
                break;
            case 4:
                $this->slice_from("\u{142}");
                break;
            case 5:
                $this->slice_del();
                $v_6 = $this->limit - $this->cursor;
                $this->ket = $this->cursor;
                $among_var = $this->find_among_b(self::A_1);
                if (0 === $among_var) {
                    $this->cursor = $this->limit - $v_6;
                    goto lab5;
                }
                $this->bra = $this->cursor;
                $this->slice_from(self::AS_1[$among_var - 1]);
            lab5:
                break;
        }
        $v_7 = $this->limit - $this->cursor;
        $this->ket = $this->cursor;
        if (!($this->eq_s_b("'"))) {
            $this->cursor = $this->limit - $v_7;
            goto lab6;
        }
        $this->bra = $this->cursor;
        $this->slice_del();
    lab6:
        $this->cursor = $this->limit_backward;
        goto lab7;
    lab1:
        $this->cursor = $v_2;
        $this->limit_backward = $this->cursor;
        $this->cursor = $this->limit;
        $this->ket = $this->cursor;
        $among_var = $this->find_among_b(self::A_3);
        if (0 === $among_var) {
            return false;
        }
        $this->bra = $this->cursor;
        if ($this->cursor <= $this->limit_backward) {
            return false;
        }
        $this->slice_from(self::AS_3[$among_var - 1]);
        $this->cursor = $this->limit_backward;
    lab7:
        return true;
    }
}
