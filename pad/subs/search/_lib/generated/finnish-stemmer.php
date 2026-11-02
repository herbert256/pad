<?php
// Generated from finnish.sbl by Snowball 3.2.0 - https://snowballstem.org/

class PadSearchFinnishStemmer extends PadSearchGeneratedStemmer
{
    private const A_0 = [
        ["pa", -1, 1],
        ["sti", -1, 2],
        ["kaan", -1, 1],
        ["han", -1, 1],
        ["kin", -1, 1],
        ["h\u{E4}n", -1, 1],
        ["k\u{E4}\u{E4}n", -1, 1],
        ["ko", -1, 1],
        ["p\u{E4}", -1, 1],
        ["k\u{F6}", -1, 1]
    ];

    private const A_1 = [
        ["lla", -1, -1],
        ["na", -1, -1],
        ["ssa", -1, -1],
        ["ta", -1, -1],
        ["lta", 3, -1],
        ["sta", 3, -1]
    ];

    private const A_2 = [
        ["ll\u{E4}", -1, -1],
        ["n\u{E4}", -1, -1],
        ["ss\u{E4}", -1, -1],
        ["t\u{E4}", -1, -1],
        ["lt\u{E4}", 3, -1],
        ["st\u{E4}", 3, -1]
    ];

    private const A_3 = [
        ["lle", -1, -1],
        ["ine", -1, -1]
    ];

    private const A_4 = [
        ["nsa", -1, 3],
        ["mme", -1, 3],
        ["nne", -1, 3],
        ["ni", -1, 2],
        ["si", -1, 1],
        ["an", -1, 4],
        ["en", -1, 6],
        ["\u{E4}n", -1, 5],
        ["ns\u{E4}", -1, 3]
    ];

    private const A_5 = [
        ["aa", -1, -1],
        ["ee", -1, -1],
        ["ii", -1, -1],
        ["oo", -1, -1],
        ["uu", -1, -1],
        ["\u{E4}\u{E4}", -1, -1],
        ["\u{F6}\u{F6}", -1, -1]
    ];

    private const A_6 = [
        ["'", -1, -1],
        ["ai", -1, -1],
        ["ei", -1, -1],
        ["ii", -1, -1],
        ["oi", -1, -1],
        ["ui", -1, -1],
        ["\u{E4}i", -1, -1],
        ["\u{F6}i", -1, -1]
    ];

    private const A_7 = [
        ["a", -1, 2],
        ["lla", 0, -1],
        ["na", 0, -1],
        ["ssa", 0, -1],
        ["ta", 0, -1],
        ["lta", 4, -1],
        ["sta", 4, -1],
        ["tta", 4, 3],
        ["lle", -1, -1],
        ["ine", -1, -1],
        ["ksi", -1, -1],
        ["n", -1, 1],
        ["han", 11, -1, 'r_A'],
        ["den", 11, -1, 'r_VI'],
        ["seen", 11, -1, 'r_LV'],
        ["hen", 11, -1, 'r_E'],
        ["tten", 11, -1, 'r_VI'],
        ["hin", 11, -1, 'r_I'],
        ["siin", 11, -1, 'r_VI'],
        ["hon", 11, -1, 'r_O'],
        ["hun", 11, -1, 'r_U'],
        ["h\u{E4}n", 11, -1, 'r_A_'],
        ["h\u{F6}n", 11, -1, 'r_O_'],
        ["\u{E4}", -1, 2],
        ["ll\u{E4}", 23, -1],
        ["n\u{E4}", 23, -1],
        ["ss\u{E4}", 23, -1],
        ["t\u{E4}", 23, -1],
        ["lt\u{E4}", 27, -1],
        ["st\u{E4}", 27, -1],
        ["tt\u{E4}", 27, 3]
    ];

    private const A_8 = [
        ["eja", -1, -1],
        ["mma", -1, 1],
        ["imma", 1, -1],
        ["mpa", -1, 1],
        ["impa", 3, -1],
        ["mmi", -1, 1],
        ["immi", 5, -1],
        ["mpi", -1, 1],
        ["impi", 7, -1],
        ["ej\u{E4}", -1, -1],
        ["mm\u{E4}", -1, 1],
        ["imm\u{E4}", 10, -1],
        ["mp\u{E4}", -1, 1],
        ["imp\u{E4}", 12, -1]
    ];

    private const A_9 = [
        ["i", -1, -1],
        ["j", -1, -1]
    ];

    private const A_10 = [
        ["mma", -1, 1],
        ["imma", 0, -1]
    ];

    private const G_AEI = ["a"=>true, "e"=>true, "i"=>true, "\u{E4}"=>true];

    private const G_C = ["b"=>true, "c"=>true, "d"=>true, "f"=>true, "g"=>true, "h"=>true, "j"=>true, "k"=>true, "l"=>true, "m"=>true, "n"=>true, "p"=>true, "q"=>true, "r"=>true, "s"=>true, "t"=>true, "v"=>true, "w"=>true, "x"=>true, "z"=>true];

    private const G_v = ["a"=>true, "e"=>true, "i"=>true, "o"=>true, "u"=>true, "y"=>true, "\u{E4}"=>true, "\u{F6}"=>true];

    private const G_particle_end = ["a"=>true, "e"=>true, "i"=>true, "n"=>true, "o"=>true, "t"=>true, "u"=>true, "y"=>true, "\u{E4}"=>true, "\u{F6}"=>true];



    protected function r_LV(): bool
    {
        return $this->find_among_b(self::A_5) !== 0;
    }


    protected function r_VI(): bool
    {
        return $this->find_among_b(self::A_6) !== 0;
    }


    protected function r_A(): bool
    {
        if (!($this->eq_s_b("a"))) {
            goto lab0;
        }
        goto lab1;
    lab0:
        if (!($this->eq_s_b("'"))) {
            return false;
        }
    lab1:
        return true;
    }


    protected function r_E(): bool
    {
        if (!($this->eq_s_b("e"))) {
            goto lab0;
        }
        goto lab1;
    lab0:
        if (!($this->eq_s_b("'"))) {
            return false;
        }
    lab1:
        return true;
    }


    protected function r_I(): bool
    {
        if (!($this->eq_s_b("i"))) {
            goto lab0;
        }
        goto lab1;
    lab0:
        if (!($this->eq_s_b("'"))) {
            return false;
        }
    lab1:
        return true;
    }


    protected function r_O(): bool
    {
        if (!($this->eq_s_b("o"))) {
            goto lab0;
        }
        goto lab1;
    lab0:
        if (!($this->eq_s_b("'"))) {
            return false;
        }
    lab1:
        return true;
    }


    protected function r_U(): bool
    {
        if (!($this->eq_s_b("u"))) {
            goto lab0;
        }
        goto lab1;
    lab0:
        if (!($this->eq_s_b("'"))) {
            return false;
        }
    lab1:
        return true;
    }


    protected function r_A_(): bool
    {
        if (!($this->eq_s_b("\u{E4}"))) {
            goto lab0;
        }
        goto lab1;
    lab0:
        if (!($this->eq_s_b("'"))) {
            return false;
        }
    lab1:
        return true;
    }


    protected function r_O_(): bool
    {
        if (!($this->eq_s_b("\u{F6}"))) {
            goto lab0;
        }
        goto lab1;
    lab0:
        if (!($this->eq_s_b("\u{F8}"))) {
            goto lab2;
        }
        goto lab1;
    lab2:
        if (!($this->eq_s_b("'"))) {
            return false;
        }
    lab1:
        return true;
    }


    public function stem(): bool
    {
        $v_1 = $this->cursor;
        $I_p1 = $this->limit;
        $I_p2 = $this->limit;
        if (!$this->go_out_grouping(self::G_v)) {
            goto lab0;
        }
        $this->inc_cursor();
        if (!$this->go_in_grouping(self::G_v)) {
            goto lab0;
        }
        $this->inc_cursor();
        $I_p1 = $this->cursor;
        if (!$this->go_out_grouping(self::G_v)) {
            goto lab0;
        }
        $this->inc_cursor();
        if (!$this->go_in_grouping(self::G_v)) {
            goto lab0;
        }
        $this->inc_cursor();
        $I_p2 = $this->cursor;
    lab0:
        $this->cursor = $v_1;
        $B_ending_removed = false;
        $this->limit_backward = $this->cursor;
        $this->cursor = $this->limit;
        $v_2 = $this->limit - $this->cursor;
        if ($this->cursor < $I_p1) {
            goto lab1;
        }
        $v_3 = $this->limit_backward;
        $this->limit_backward = $I_p1;
        $this->ket = $this->cursor;
        $among_var = $this->find_among_b(self::A_0);
        if (0 === $among_var) {
            $this->limit_backward = $v_3;
            goto lab1;
        }
        $this->bra = $this->cursor;
        $this->limit_backward = $v_3;
        switch ($among_var) {
            case 1:
                if (!($this->in_grouping_b(self::G_particle_end))) {
                    goto lab1;
                }
                break;
            case 2:
                if ($I_p2 > $this->cursor) {
                    goto lab1;
                }
                break;
        }
        $this->slice_del();
    lab1:
        $this->cursor = $this->limit - $v_2;
        $v_4 = $this->limit - $this->cursor;
        if ($this->cursor < $I_p1) {
            goto lab2;
        }
        $v_5 = $this->limit_backward;
        $this->limit_backward = $I_p1;
        $this->ket = $this->cursor;
        $among_var = $this->find_among_b(self::A_4);
        if (0 === $among_var) {
            $this->limit_backward = $v_5;
            goto lab2;
        }
        $this->bra = $this->cursor;
        $this->limit_backward = $v_5;
        switch ($among_var) {
            case 1:
                if (!($this->eq_s_b("k"))) {
                    goto lab3;
                }
                goto lab2;
            lab3:
                $this->slice_del();
                break;
            case 2:
                $this->slice_del();
                $this->ket = $this->cursor;
                if (!($this->eq_s_b("kse"))) {
                    goto lab2;
                }
                $this->bra = $this->cursor;
                $this->slice_from("ksi");
                break;
            case 3:
                $this->slice_del();
                break;
            case 4:
                if ($this->find_among_b(self::A_1) === 0) {
                    goto lab2;
                }
                $this->slice_del();
                break;
            case 5:
                if ($this->find_among_b(self::A_2) === 0) {
                    goto lab2;
                }
                $this->slice_del();
                break;
            case 6:
                if ($this->find_among_b(self::A_3) === 0) {
                    goto lab2;
                }
                $this->slice_del();
                break;
        }
    lab2:
        $this->cursor = $this->limit - $v_4;
        $v_6 = $this->limit - $this->cursor;
        if ($this->cursor < $I_p1) {
            goto lab4;
        }
        $v_7 = $this->limit_backward;
        $this->limit_backward = $I_p1;
        $this->ket = $this->cursor;
        $among_var = $this->find_among_b(self::A_7);
        if (0 === $among_var) {
            $this->limit_backward = $v_7;
            goto lab4;
        }
        $this->bra = $this->cursor;
        $this->limit_backward = $v_7;
        switch ($among_var) {
            case 1:
                $v_8 = $this->limit - $this->cursor;
                $v_9 = $this->limit - $this->cursor;
                $v_10 = $this->limit - $this->cursor;
                if (!$this->r_LV()) {
                    goto lab6;
                }
                goto lab7;
            lab6:
                $this->cursor = $this->limit - $v_10;
                if (!($this->eq_s_b("ie"))) {
                    $this->cursor = $this->limit - $v_8;
                    goto lab5;
                }
            lab7:
                $this->cursor = $this->limit - $v_9;
                if ($this->cursor <= $this->limit_backward) {
                    $this->cursor = $this->limit - $v_8;
                    goto lab5;
                }
                $this->dec_cursor();
                $this->bra = $this->cursor;
            lab5:
                break;
            case 2:
                if (!($this->in_grouping_b(self::G_v))) {
                    goto lab4;
                }
                if (!($this->in_grouping_b(self::G_C))) {
                    goto lab4;
                }
                break;
            case 3:
                if (!($this->eq_s_b("e"))) {
                    goto lab4;
                }
                break;
        }
        $this->slice_del();
        $B_ending_removed = true;
    lab4:
        $this->cursor = $this->limit - $v_6;
        $v_11 = $this->limit - $this->cursor;
        if ($this->cursor < $I_p2) {
            goto lab8;
        }
        $v_12 = $this->limit_backward;
        $this->limit_backward = $I_p2;
        $this->ket = $this->cursor;
        $among_var = $this->find_among_b(self::A_8);
        if (0 === $among_var) {
            $this->limit_backward = $v_12;
            goto lab8;
        }
        $this->bra = $this->cursor;
        $this->limit_backward = $v_12;
        switch ($among_var) {
            case 1:
                if (!($this->eq_s_b("po"))) {
                    goto lab9;
                }
                goto lab8;
            lab9:
                break;
        }
        $this->slice_del();
    lab8:
        $this->cursor = $this->limit - $v_11;
        if (!$B_ending_removed) {
            goto lab10;
        }
        $v_13 = $this->limit - $this->cursor;
        if ($this->cursor < $I_p1) {
            goto lab11;
        }
        $v_14 = $this->limit_backward;
        $this->limit_backward = $I_p1;
        $this->ket = $this->cursor;
        if ($this->find_among_b(self::A_9) === 0) {
            $this->limit_backward = $v_14;
            goto lab11;
        }
        $this->bra = $this->cursor;
        $this->limit_backward = $v_14;
        $this->slice_del();
    lab11:
        $this->cursor = $this->limit - $v_13;
        goto lab12;
    lab10:
        $v_15 = $this->limit - $this->cursor;
        if ($this->cursor < $I_p1) {
            goto lab13;
        }
        $v_16 = $this->limit_backward;
        $this->limit_backward = $I_p1;
        $this->ket = $this->cursor;
        if (!($this->eq_s_b("t"))) {
            $this->limit_backward = $v_16;
            goto lab13;
        }
        $this->bra = $this->cursor;
        $v_17 = $this->limit - $this->cursor;
        if (!($this->in_grouping_b(self::G_v))) {
            $this->limit_backward = $v_16;
            goto lab13;
        }
        $this->cursor = $this->limit - $v_17;
        $this->slice_del();
        $this->limit_backward = $v_16;
        if ($this->cursor < $I_p2) {
            goto lab13;
        }
        $v_18 = $this->limit_backward;
        $this->limit_backward = $I_p2;
        $this->ket = $this->cursor;
        $among_var = $this->find_among_b(self::A_10);
        if (0 === $among_var) {
            $this->limit_backward = $v_18;
            goto lab13;
        }
        $this->bra = $this->cursor;
        $this->limit_backward = $v_18;
        switch ($among_var) {
            case 1:
                if (!($this->eq_s_b("po"))) {
                    goto lab14;
                }
                goto lab13;
            lab14:
                break;
        }
        $this->slice_del();
    lab13:
        $this->cursor = $this->limit - $v_15;
    lab12:
        $v_19 = $this->limit - $this->cursor;
        if ($this->cursor < $I_p1) {
            goto lab15;
        }
        $v_20 = $this->limit_backward;
        $this->limit_backward = $I_p1;
        $v_21 = $this->limit - $this->cursor;
        $v_22 = $this->limit - $this->cursor;
        if (!$this->r_LV()) {
            goto lab16;
        }
        $this->cursor = $this->limit - $v_22;
        $this->ket = $this->cursor;
        if ($this->cursor <= $this->limit_backward) {
            goto lab16;
        }
        $this->dec_cursor();
        $this->bra = $this->cursor;
        $this->slice_del();
    lab16:
        $this->cursor = $this->limit - $v_21;
        $v_23 = $this->limit - $this->cursor;
        $this->ket = $this->cursor;
        if (!($this->in_grouping_b(self::G_AEI))) {
            goto lab17;
        }
        $this->bra = $this->cursor;
        if (!($this->in_grouping_b(self::G_C))) {
            goto lab17;
        }
        $this->slice_del();
    lab17:
        $this->cursor = $this->limit - $v_23;
        $v_24 = $this->limit - $this->cursor;
        $this->ket = $this->cursor;
        if (!($this->eq_s_b("j"))) {
            goto lab18;
        }
        $this->bra = $this->cursor;
        if (!($this->eq_s_b("o"))) {
            goto lab19;
        }
        goto lab20;
    lab19:
        if (!($this->eq_s_b("u"))) {
            goto lab18;
        }
    lab20:
        $this->slice_del();
    lab18:
        $this->cursor = $this->limit - $v_24;
        $v_25 = $this->limit - $this->cursor;
        $this->ket = $this->cursor;
        if (!($this->eq_s_b("o"))) {
            goto lab21;
        }
        $this->bra = $this->cursor;
        if (!($this->eq_s_b("j"))) {
            goto lab21;
        }
        $this->slice_del();
    lab21:
        $this->cursor = $this->limit - $v_25;
        $this->limit_backward = $v_20;
        $v_26 = $this->limit - $this->cursor;
        if (!$this->go_in_grouping_b(self::G_v)) {
            goto lab22;
        }
        $this->ket = $this->cursor;
        if (!($this->in_grouping_b(self::G_C))) {
            goto lab22;
        }
        $this->bra = $this->cursor;
        $S_x = $this->slice_to();
        if (!($this->eq_s_b($S_x))) {
            goto lab22;
        }
        $this->slice_del();
    lab22:
        $this->cursor = $this->limit - $v_26;
        $this->ket = $this->cursor;
        if (!($this->eq_s_b("'"))) {
            goto lab15;
        }
        $this->bra = $this->cursor;
        $this->slice_del();
    lab15:
        $this->cursor = $this->limit - $v_19;
        $this->cursor = $this->limit_backward;
        return true;
    }
}
