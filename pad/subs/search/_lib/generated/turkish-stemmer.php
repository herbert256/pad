<?php
// Generated from turkish.sbl by Snowball 3.2.0 - https://snowballstem.org/

class PadSearchTurkishStemmer extends PadSearchGeneratedStemmer
{
    private const A_0 = [
        ["m", -1, -1],
        ["n", -1, -1],
        ["miz", -1, -1],
        ["niz", -1, -1],
        ["muz", -1, -1],
        ["nuz", -1, -1],
        ["m\u{131}z", -1, -1],
        ["n\u{131}z", -1, -1],
        ["m\u{FC}z", -1, -1],
        ["n\u{FC}z", -1, -1]
    ];

    private const A_1 = [
        ["leri", -1, -1],
        ["lar\u{131}", -1, -1]
    ];

    private const A_2 = [
        ["ni", -1, -1],
        ["nu", -1, -1],
        ["n\u{131}", -1, -1],
        ["n\u{FC}", -1, -1]
    ];

    private const A_3 = [
        ["in", -1, -1],
        ["un", -1, -1],
        ["\u{131}n", -1, -1],
        ["\u{FC}n", -1, -1]
    ];

    private const A_4 = [
        ["a", -1, -1],
        ["e", -1, -1]
    ];

    private const A_5 = [
        ["na", -1, -1],
        ["ne", -1, -1]
    ];

    private const A_6 = [
        ["da", -1, -1],
        ["ta", -1, -1],
        ["de", -1, -1],
        ["te", -1, -1]
    ];

    private const A_7 = [
        ["nda", -1, -1],
        ["nde", -1, -1]
    ];

    private const A_8 = [
        ["dan", -1, -1],
        ["tan", -1, -1],
        ["den", -1, -1],
        ["ten", -1, -1]
    ];

    private const A_9 = [
        ["ndan", -1, -1],
        ["nden", -1, -1]
    ];

    private const A_10 = [
        ["la", -1, -1],
        ["le", -1, -1]
    ];

    private const A_11 = [
        ["ca", -1, -1],
        ["ce", -1, -1]
    ];

    private const A_12 = [
        ["im", -1, -1],
        ["um", -1, -1],
        ["\u{131}m", -1, -1],
        ["\u{FC}m", -1, -1]
    ];

    private const A_13 = [
        ["sin", -1, -1],
        ["sun", -1, -1],
        ["s\u{131}n", -1, -1],
        ["s\u{FC}n", -1, -1]
    ];

    private const A_14 = [
        ["iz", -1, -1],
        ["uz", -1, -1],
        ["\u{131}z", -1, -1],
        ["\u{FC}z", -1, -1]
    ];

    private const A_15 = [
        ["siniz", -1, -1],
        ["sunuz", -1, -1],
        ["s\u{131}n\u{131}z", -1, -1],
        ["s\u{FC}n\u{FC}z", -1, -1]
    ];

    private const A_16 = [
        ["lar", -1, -1],
        ["ler", -1, -1]
    ];

    private const A_17 = [
        ["niz", -1, -1],
        ["nuz", -1, -1],
        ["n\u{131}z", -1, -1],
        ["n\u{FC}z", -1, -1]
    ];

    private const A_18 = [
        ["dir", -1, -1],
        ["tir", -1, -1],
        ["dur", -1, -1],
        ["tur", -1, -1],
        ["d\u{131}r", -1, -1],
        ["t\u{131}r", -1, -1],
        ["d\u{FC}r", -1, -1],
        ["t\u{FC}r", -1, -1]
    ];

    private const A_19 = [
        ["cas\u{131}na", -1, -1],
        ["cesine", -1, -1]
    ];

    private const A_20 = [
        ["di", -1, -1],
        ["ti", -1, -1],
        ["dik", -1, -1],
        ["tik", -1, -1],
        ["duk", -1, -1],
        ["tuk", -1, -1],
        ["d\u{131}k", -1, -1],
        ["t\u{131}k", -1, -1],
        ["d\u{FC}k", -1, -1],
        ["t\u{FC}k", -1, -1],
        ["dim", -1, -1],
        ["tim", -1, -1],
        ["dum", -1, -1],
        ["tum", -1, -1],
        ["d\u{131}m", -1, -1],
        ["t\u{131}m", -1, -1],
        ["d\u{FC}m", -1, -1],
        ["t\u{FC}m", -1, -1],
        ["din", -1, -1],
        ["tin", -1, -1],
        ["dun", -1, -1],
        ["tun", -1, -1],
        ["d\u{131}n", -1, -1],
        ["t\u{131}n", -1, -1],
        ["d\u{FC}n", -1, -1],
        ["t\u{FC}n", -1, -1],
        ["du", -1, -1],
        ["tu", -1, -1],
        ["d\u{131}", -1, -1],
        ["t\u{131}", -1, -1],
        ["d\u{FC}", -1, -1],
        ["t\u{FC}", -1, -1]
    ];

    private const A_21 = [
        ["sa", -1, -1],
        ["se", -1, -1],
        ["sak", -1, -1],
        ["sek", -1, -1],
        ["sam", -1, -1],
        ["sem", -1, -1],
        ["san", -1, -1],
        ["sen", -1, -1]
    ];

    private const A_22 = [
        ["mi\u{15F}", -1, -1],
        ["mu\u{15F}", -1, -1],
        ["m\u{131}\u{15F}", -1, -1],
        ["m\u{FC}\u{15F}", -1, -1]
    ];

    private const A_23 = [
        ["b", -1, 1],
        ["c", -1, 2],
        ["d", -1, 3],
        ["\u{11F}", -1, 4]
    ];

    private const AS_23 = ["p", "\u{E7}", "t", "k"];

    private const G_vowel = ["a"=>true, "e"=>true, "i"=>true, "o"=>true, "u"=>true, "\u{F6}"=>true, "\u{FC}"=>true, "\u{131}"=>true];

    private const G_U = ["i"=>true, "u"=>true, "\u{FC}"=>true, "\u{131}"=>true];

    private const G_vowel1 = ["a"=>true, "o"=>true, "u"=>true, "\u{131}"=>true];

    private const G_vowel2 = ["e"=>true, "i"=>true, "\u{F6}"=>true, "\u{FC}"=>true];

    private const G_vowel3 = ["a"=>true, "\u{131}"=>true];

    private const G_vowel4 = ["e"=>true, "i"=>true];

    private const G_vowel5 = ["o"=>true, "u"=>true];

    private const G_vowel6 = ["\u{F6}"=>true, "\u{FC}"=>true];



    protected function r_check_vowel_harmony(): bool
    {
        $v_1 = $this->limit - $this->cursor;
        if (!$this->go_out_grouping_b(self::G_vowel)) {
            return false;
        }
        $v_2 = $this->limit - $this->cursor;
        if (!($this->eq_s_b("a"))) {
            goto lab0;
        }
        if (!$this->go_out_grouping_b(self::G_vowel1)) {
            goto lab0;
        }
        goto lab1;
    lab0:
        $this->cursor = $this->limit - $v_2;
        if (!($this->eq_s_b("e"))) {
            goto lab2;
        }
        if (!$this->go_out_grouping_b(self::G_vowel2)) {
            goto lab2;
        }
        goto lab1;
    lab2:
        $this->cursor = $this->limit - $v_2;
        if (!($this->eq_s_b("\u{131}"))) {
            goto lab3;
        }
        if (!$this->go_out_grouping_b(self::G_vowel3)) {
            goto lab3;
        }
        goto lab1;
    lab3:
        $this->cursor = $this->limit - $v_2;
        if (!($this->eq_s_b("i"))) {
            goto lab4;
        }
        if (!$this->go_out_grouping_b(self::G_vowel4)) {
            goto lab4;
        }
        goto lab1;
    lab4:
        $this->cursor = $this->limit - $v_2;
        if (!($this->eq_s_b("o"))) {
            goto lab5;
        }
        if (!$this->go_out_grouping_b(self::G_vowel5)) {
            goto lab5;
        }
        goto lab1;
    lab5:
        $this->cursor = $this->limit - $v_2;
        if (!($this->eq_s_b("\u{F6}"))) {
            goto lab6;
        }
        if (!$this->go_out_grouping_b(self::G_vowel6)) {
            goto lab6;
        }
        goto lab1;
    lab6:
        $this->cursor = $this->limit - $v_2;
        if (!($this->eq_s_b("u"))) {
            goto lab7;
        }
        if (!$this->go_out_grouping_b(self::G_vowel5)) {
            goto lab7;
        }
        goto lab1;
    lab7:
        $this->cursor = $this->limit - $v_2;
        if (!($this->eq_s_b("\u{FC}"))) {
            return false;
        }
        if (!$this->go_out_grouping_b(self::G_vowel6)) {
            return false;
        }
    lab1:
        $this->cursor = $this->limit - $v_1;
        return true;
    }


    protected function r_mark_suffix_with_optional_n_consonant(): bool
    {
        $v_1 = $this->limit - $this->cursor;
        if (!($this->eq_s_b("n"))) {
            goto lab0;
        }
        $v_2 = $this->limit - $this->cursor;
        if (!($this->in_grouping_b(self::G_vowel))) {
            goto lab0;
        }
        $this->cursor = $this->limit - $v_2;
        goto lab1;
    lab0:
        $this->cursor = $this->limit - $v_1;
        if (!($this->eq_s_b("n"))) {
            goto lab2;
        }
        return false;
    lab2:
        $v_3 = $this->limit - $this->cursor;
        if ($this->cursor <= $this->limit_backward) {
            return false;
        }
        $this->dec_cursor();
        if (!($this->in_grouping_b(self::G_vowel))) {
            return false;
        }
        $this->cursor = $this->limit - $v_3;
    lab1:
        return true;
    }


    protected function r_mark_suffix_with_optional_y_consonant(): bool
    {
        $v_1 = $this->limit - $this->cursor;
        if (!($this->eq_s_b("y"))) {
            goto lab0;
        }
        $v_2 = $this->limit - $this->cursor;
        if (!($this->in_grouping_b(self::G_vowel))) {
            goto lab0;
        }
        $this->cursor = $this->limit - $v_2;
        goto lab1;
    lab0:
        $this->cursor = $this->limit - $v_1;
        if (!($this->eq_s_b("y"))) {
            goto lab2;
        }
        return false;
    lab2:
        $v_3 = $this->limit - $this->cursor;
        if ($this->cursor <= $this->limit_backward) {
            return false;
        }
        $this->dec_cursor();
        if (!($this->in_grouping_b(self::G_vowel))) {
            return false;
        }
        $this->cursor = $this->limit - $v_3;
    lab1:
        return true;
    }


    protected function r_mark_possessives(): bool
    {
        if ($this->find_among_b(self::A_0) === 0) {
            return false;
        }
        $v_1 = $this->limit - $this->cursor;
        if (!($this->in_grouping_b(self::G_U))) {
            goto lab0;
        }
        $v_2 = $this->limit - $this->cursor;
        if (!($this->out_grouping_b(self::G_vowel))) {
            goto lab0;
        }
        $this->cursor = $this->limit - $v_2;
        goto lab1;
    lab0:
        $this->cursor = $this->limit - $v_1;
        if (!($this->in_grouping_b(self::G_U))) {
            goto lab2;
        }
        return false;
    lab2:
        $v_3 = $this->limit - $this->cursor;
        if ($this->cursor <= $this->limit_backward) {
            return false;
        }
        $this->dec_cursor();
        if (!($this->out_grouping_b(self::G_vowel))) {
            return false;
        }
        $this->cursor = $this->limit - $v_3;
    lab1:
        return true;
    }


    protected function r_mark_sU(): bool
    {
        if (!$this->r_check_vowel_harmony()) {
            return false;
        }
        if (!($this->in_grouping_b(self::G_U))) {
            return false;
        }
        $v_1 = $this->limit - $this->cursor;
        if (!($this->eq_s_b("s"))) {
            goto lab0;
        }
        $v_2 = $this->limit - $this->cursor;
        if (!($this->in_grouping_b(self::G_vowel))) {
            goto lab0;
        }
        $this->cursor = $this->limit - $v_2;
        goto lab1;
    lab0:
        $this->cursor = $this->limit - $v_1;
        if (!($this->eq_s_b("s"))) {
            goto lab2;
        }
        return false;
    lab2:
        $v_3 = $this->limit - $this->cursor;
        if ($this->cursor <= $this->limit_backward) {
            return false;
        }
        $this->dec_cursor();
        if (!($this->in_grouping_b(self::G_vowel))) {
            return false;
        }
        $this->cursor = $this->limit - $v_3;
    lab1:
        return true;
    }


    protected function r_mark_lArI(): bool
    {
        return $this->find_among_b(self::A_1) !== 0;
    }


    protected function r_mark_nUn(): bool
    {
        if (!$this->r_check_vowel_harmony()) {
            return false;
        }
        if ($this->find_among_b(self::A_3) === 0) {
            return false;
        }
        return $this->r_mark_suffix_with_optional_n_consonant();
    }


    protected function r_mark_DA(): bool
    {
        if (!$this->r_check_vowel_harmony()) {
            return false;
        }
        return $this->find_among_b(self::A_6) !== 0;
    }


    protected function r_mark_ndA(): bool
    {
        if (!$this->r_check_vowel_harmony()) {
            return false;
        }
        return $this->find_among_b(self::A_7) !== 0;
    }


    protected function r_mark_yUm(): bool
    {
        if (!$this->r_check_vowel_harmony()) {
            return false;
        }
        if ($this->find_among_b(self::A_12) === 0) {
            return false;
        }
        return $this->r_mark_suffix_with_optional_y_consonant();
    }


    protected function r_mark_sUn(): bool
    {
        if (!$this->r_check_vowel_harmony()) {
            return false;
        }
        return $this->find_among_b(self::A_13) !== 0;
    }


    protected function r_mark_yUz(): bool
    {
        if (!$this->r_check_vowel_harmony()) {
            return false;
        }
        if ($this->find_among_b(self::A_14) === 0) {
            return false;
        }
        return $this->r_mark_suffix_with_optional_y_consonant();
    }


    protected function r_mark_sUnUz(): bool
    {
        return $this->find_among_b(self::A_15) !== 0;
    }


    protected function r_mark_lAr(): bool
    {
        if (!$this->r_check_vowel_harmony()) {
            return false;
        }
        return $this->find_among_b(self::A_16) !== 0;
    }


    protected function r_mark_DUr(): bool
    {
        if (!$this->r_check_vowel_harmony()) {
            return false;
        }
        return $this->find_among_b(self::A_18) !== 0;
    }


    protected function r_mark_yDU(): bool
    {
        if (!$this->r_check_vowel_harmony()) {
            return false;
        }
        if ($this->find_among_b(self::A_20) === 0) {
            return false;
        }
        return $this->r_mark_suffix_with_optional_y_consonant();
    }


    protected function r_mark_ysA(): bool
    {
        if ($this->find_among_b(self::A_21) === 0) {
            return false;
        }
        return $this->r_mark_suffix_with_optional_y_consonant();
    }


    protected function r_mark_ymUs_(): bool
    {
        if (!$this->r_check_vowel_harmony()) {
            return false;
        }
        if ($this->find_among_b(self::A_22) === 0) {
            return false;
        }
        return $this->r_mark_suffix_with_optional_y_consonant();
    }


    protected function r_stem_suffix_chain_before_ki(): bool
    {
        $this->ket = $this->cursor;
        if (!($this->eq_s_b("ki"))) {
            return false;
        }
        $v_1 = $this->limit - $this->cursor;
        if (!$this->r_mark_DA()) {
            goto lab0;
        }
        $this->bra = $this->cursor;
        $this->slice_del();
        $v_2 = $this->limit - $this->cursor;
        $this->ket = $this->cursor;
        $v_3 = $this->limit - $this->cursor;
        if (!$this->r_mark_lAr()) {
            goto lab2;
        }
        $this->bra = $this->cursor;
        $this->slice_del();
        $v_4 = $this->limit - $this->cursor;
        if (!$this->r_stem_suffix_chain_before_ki()) {
            $this->cursor = $this->limit - $v_4;
            goto lab3;
        }
    lab3:
        goto lab4;
    lab2:
        $this->cursor = $this->limit - $v_3;
        if (!$this->r_mark_possessives()) {
            $this->cursor = $this->limit - $v_2;
            goto lab1;
        }
        $this->bra = $this->cursor;
        $this->slice_del();
        $v_5 = $this->limit - $this->cursor;
        $this->ket = $this->cursor;
        if (!$this->r_mark_lAr()) {
            $this->cursor = $this->limit - $v_5;
            goto lab5;
        }
        $this->bra = $this->cursor;
        $this->slice_del();
        if (!$this->r_stem_suffix_chain_before_ki()) {
            $this->cursor = $this->limit - $v_5;
            goto lab5;
        }
    lab5:
    lab4:
    lab1:
        goto lab6;
    lab0:
        $this->cursor = $this->limit - $v_1;
        if (!$this->r_mark_nUn()) {
            goto lab7;
        }
        $this->bra = $this->cursor;
        $this->slice_del();
        $v_6 = $this->limit - $this->cursor;
        $this->ket = $this->cursor;
        $v_7 = $this->limit - $this->cursor;
        if (!$this->r_mark_lArI()) {
            goto lab9;
        }
        $this->bra = $this->cursor;
        $this->slice_del();
        goto lab10;
    lab9:
        $this->cursor = $this->limit - $v_7;
        $this->ket = $this->cursor;
        $v_8 = $this->limit - $this->cursor;
        if (!$this->r_mark_possessives()) {
            goto lab12;
        }
        goto lab13;
    lab12:
        $this->cursor = $this->limit - $v_8;
        if (!$this->r_mark_sU()) {
            goto lab11;
        }
    lab13:
        $this->bra = $this->cursor;
        $this->slice_del();
        $v_9 = $this->limit - $this->cursor;
        $this->ket = $this->cursor;
        if (!$this->r_mark_lAr()) {
            $this->cursor = $this->limit - $v_9;
            goto lab14;
        }
        $this->bra = $this->cursor;
        $this->slice_del();
        if (!$this->r_stem_suffix_chain_before_ki()) {
            $this->cursor = $this->limit - $v_9;
            goto lab14;
        }
    lab14:
        goto lab10;
    lab11:
        $this->cursor = $this->limit - $v_7;
        if (!$this->r_stem_suffix_chain_before_ki()) {
            $this->cursor = $this->limit - $v_6;
            goto lab8;
        }
    lab10:
    lab8:
        goto lab6;
    lab7:
        $this->cursor = $this->limit - $v_1;
        if (!$this->r_mark_ndA()) {
            return false;
        }
        $v_10 = $this->limit - $this->cursor;
        if (!$this->r_mark_lArI()) {
            goto lab15;
        }
        $this->bra = $this->cursor;
        $this->slice_del();
        goto lab16;
    lab15:
        $this->cursor = $this->limit - $v_10;
        if (!$this->r_mark_sU()) {
            goto lab17;
        }
        $this->bra = $this->cursor;
        $this->slice_del();
        $v_11 = $this->limit - $this->cursor;
        $this->ket = $this->cursor;
        if (!$this->r_mark_lAr()) {
            $this->cursor = $this->limit - $v_11;
            goto lab18;
        }
        $this->bra = $this->cursor;
        $this->slice_del();
        if (!$this->r_stem_suffix_chain_before_ki()) {
            $this->cursor = $this->limit - $v_11;
            goto lab18;
        }
    lab18:
        goto lab16;
    lab17:
        $this->cursor = $this->limit - $v_10;
        if (!$this->r_stem_suffix_chain_before_ki()) {
            return false;
        }
    lab16:
    lab6:
        return true;
    }


    public function stem(): bool
    {
        $v_1 = $this->cursor;
        $this->bra = $this->cursor;
        while (true) {
            $v_2 = $this->cursor;
            if (!($this->eq_s("'"))) {
                goto lab3;
            }
            goto lab2;
        lab3:
            $this->cursor = $v_2;
            break;
        lab2:
            $this->cursor = $v_2;
            if ($this->cursor >= $this->limit) {
                goto lab1;
            }
            $this->inc_cursor();
        }
        $this->ket = $this->cursor;
        $this->slice_del();
    lab1:
        $this->cursor = $v_1;
        $v_3 = $this->cursor;
        if (!$this->hop(2)) {
            goto lab4;
        }
        while (true) {
            $v_4 = $this->cursor;
            if (!($this->eq_s("'"))) {
                goto lab5;
            }
            $this->cursor = $v_4;
            break;
        lab5:
            $this->cursor = $v_4;
            if ($this->cursor >= $this->limit) {
                goto lab4;
            }
            $this->inc_cursor();
        }
        $this->bra = $this->cursor;
        $this->cursor = $this->limit;
        $this->ket = $this->cursor;
        $this->slice_del();
    lab4:
        $this->cursor = $v_3;
    lab0:
        $v_5 = $this->cursor;
        for ($v_6 = 2; $v_6 > 0; $v_6--) {
            if (!$this->go_out_grouping(self::G_vowel)) {
                return false;
            }
            $this->inc_cursor();
        }
        $this->cursor = $v_5;
        $this->limit_backward = $this->cursor;
        $this->cursor = $this->limit;
        $v_7 = $this->limit - $this->cursor;
        $this->ket = $this->cursor;
        $B_continue_stemming_noun_suffixes = true;
        $v_8 = $this->limit - $this->cursor;
        $v_9 = $this->limit - $this->cursor;
        if (!$this->r_mark_ymUs_()) {
            goto lab8;
        }
        goto lab9;
    lab8:
        $this->cursor = $this->limit - $v_9;
        if (!$this->r_mark_yDU()) {
            goto lab10;
        }
        goto lab9;
    lab10:
        $this->cursor = $this->limit - $v_9;
        if (!$this->r_mark_ysA()) {
            goto lab11;
        }
        goto lab9;
    lab11:
        $this->cursor = $this->limit - $v_9;
        if (!($this->eq_s_b("ken"))) {
            goto lab7;
        }
        if (!$this->r_mark_suffix_with_optional_y_consonant()) {
            goto lab7;
        }
    lab9:
        goto lab12;
    lab7:
        $this->cursor = $this->limit - $v_8;
        if ($this->find_among_b(self::A_19) === 0) {
            goto lab13;
        }
        $v_10 = $this->limit - $this->cursor;
        if (!$this->r_mark_sUnUz()) {
            goto lab14;
        }
        goto lab15;
    lab14:
        $this->cursor = $this->limit - $v_10;
        if (!$this->r_mark_lAr()) {
            goto lab16;
        }
        goto lab15;
    lab16:
        $this->cursor = $this->limit - $v_10;
        if (!$this->r_mark_yUm()) {
            goto lab17;
        }
        goto lab15;
    lab17:
        $this->cursor = $this->limit - $v_10;
        if (!$this->r_mark_sUn()) {
            goto lab18;
        }
        goto lab15;
    lab18:
        $this->cursor = $this->limit - $v_10;
        if (!$this->r_mark_yUz()) {
            goto lab19;
        }
        goto lab15;
    lab19:
        $this->cursor = $this->limit - $v_10;
    lab15:
        if (!$this->r_mark_ymUs_()) {
            goto lab13;
        }
        goto lab12;
    lab13:
        $this->cursor = $this->limit - $v_8;
        if (!$this->r_mark_lAr()) {
            goto lab20;
        }
        $this->bra = $this->cursor;
        $this->slice_del();
        $v_11 = $this->limit - $this->cursor;
        $this->ket = $this->cursor;
        $v_12 = $this->limit - $this->cursor;
        if (!$this->r_mark_DUr()) {
            goto lab22;
        }
        goto lab23;
    lab22:
        $this->cursor = $this->limit - $v_12;
        if (!$this->r_mark_yDU()) {
            goto lab24;
        }
        goto lab23;
    lab24:
        $this->cursor = $this->limit - $v_12;
        if (!$this->r_mark_ysA()) {
            goto lab25;
        }
        goto lab23;
    lab25:
        $this->cursor = $this->limit - $v_12;
        if (!$this->r_mark_ymUs_()) {
            $this->cursor = $this->limit - $v_11;
            goto lab21;
        }
    lab23:
    lab21:
        $B_continue_stemming_noun_suffixes = false;
        goto lab12;
    lab20:
        $this->cursor = $this->limit - $v_8;
        if (!$this->r_check_vowel_harmony()) {
            goto lab26;
        }
        if ($this->find_among_b(self::A_17) === 0) {
            goto lab26;
        }
        $v_13 = $this->limit - $this->cursor;
        if (!$this->r_mark_yDU()) {
            goto lab27;
        }
        goto lab28;
    lab27:
        $this->cursor = $this->limit - $v_13;
        if (!$this->r_mark_ysA()) {
            goto lab26;
        }
    lab28:
        goto lab12;
    lab26:
        $this->cursor = $this->limit - $v_8;
        $v_14 = $this->limit - $this->cursor;
        if (!$this->r_mark_sUnUz()) {
            goto lab30;
        }
        goto lab31;
    lab30:
        $this->cursor = $this->limit - $v_14;
        if (!$this->r_mark_yUz()) {
            goto lab32;
        }
        goto lab31;
    lab32:
        $this->cursor = $this->limit - $v_14;
        if (!$this->r_mark_sUn()) {
            goto lab33;
        }
        goto lab31;
    lab33:
        $this->cursor = $this->limit - $v_14;
        if (!$this->r_mark_yUm()) {
            goto lab29;
        }
    lab31:
        $this->bra = $this->cursor;
        $this->slice_del();
        $v_15 = $this->limit - $this->cursor;
        $this->ket = $this->cursor;
        if (!$this->r_mark_ymUs_()) {
            $this->cursor = $this->limit - $v_15;
            goto lab34;
        }
    lab34:
        goto lab12;
    lab29:
        $this->cursor = $this->limit - $v_8;
        if (!$this->r_mark_DUr()) {
            goto lab6;
        }
        $this->bra = $this->cursor;
        $this->slice_del();
        $v_16 = $this->limit - $this->cursor;
        $this->ket = $this->cursor;
        $v_17 = $this->limit - $this->cursor;
        if (!$this->r_mark_sUnUz()) {
            goto lab36;
        }
        goto lab37;
    lab36:
        $this->cursor = $this->limit - $v_17;
        if (!$this->r_mark_lAr()) {
            goto lab38;
        }
        goto lab37;
    lab38:
        $this->cursor = $this->limit - $v_17;
        if (!$this->r_mark_yUm()) {
            goto lab39;
        }
        goto lab37;
    lab39:
        $this->cursor = $this->limit - $v_17;
        if (!$this->r_mark_sUn()) {
            goto lab40;
        }
        goto lab37;
    lab40:
        $this->cursor = $this->limit - $v_17;
        if (!$this->r_mark_yUz()) {
            goto lab41;
        }
        goto lab37;
    lab41:
        $this->cursor = $this->limit - $v_17;
    lab37:
        if (!$this->r_mark_ymUs_()) {
            $this->cursor = $this->limit - $v_16;
            goto lab35;
        }
    lab35:
    lab12:
        $this->bra = $this->cursor;
        $this->slice_del();
    lab6:
        $this->cursor = $this->limit - $v_7;
        if (!$B_continue_stemming_noun_suffixes) {
            return false;
        }
        $v_18 = $this->limit - $this->cursor;
        $v_19 = $this->limit - $this->cursor;
        $this->ket = $this->cursor;
        if (!$this->r_mark_lAr()) {
            goto lab43;
        }
        $this->bra = $this->cursor;
        $this->slice_del();
        $v_20 = $this->limit - $this->cursor;
        if (!$this->r_stem_suffix_chain_before_ki()) {
            $this->cursor = $this->limit - $v_20;
            goto lab44;
        }
    lab44:
        goto lab45;
    lab43:
        $this->cursor = $this->limit - $v_19;
        $this->ket = $this->cursor;
        if (!$this->r_check_vowel_harmony()) {
            goto lab46;
        }
        if ($this->find_among_b(self::A_11) === 0) {
            goto lab46;
        }
        if (!$this->r_mark_suffix_with_optional_n_consonant()) {
            goto lab46;
        }
        $this->bra = $this->cursor;
        $this->slice_del();
        $v_21 = $this->limit - $this->cursor;
        $v_22 = $this->limit - $this->cursor;
        $this->ket = $this->cursor;
        if (!$this->r_mark_lArI()) {
            goto lab48;
        }
        $this->bra = $this->cursor;
        $this->slice_del();
        goto lab49;
    lab48:
        $this->cursor = $this->limit - $v_22;
        $this->ket = $this->cursor;
        $v_23 = $this->limit - $this->cursor;
        if (!$this->r_mark_possessives()) {
            goto lab51;
        }
        goto lab52;
    lab51:
        $this->cursor = $this->limit - $v_23;
        if (!$this->r_mark_sU()) {
            goto lab50;
        }
    lab52:
        $this->bra = $this->cursor;
        $this->slice_del();
        $v_24 = $this->limit - $this->cursor;
        $this->ket = $this->cursor;
        if (!$this->r_mark_lAr()) {
            $this->cursor = $this->limit - $v_24;
            goto lab53;
        }
        $this->bra = $this->cursor;
        $this->slice_del();
        if (!$this->r_stem_suffix_chain_before_ki()) {
            $this->cursor = $this->limit - $v_24;
            goto lab53;
        }
    lab53:
        goto lab49;
    lab50:
        $this->cursor = $this->limit - $v_22;
        $this->ket = $this->cursor;
        if (!$this->r_mark_lAr()) {
            $this->cursor = $this->limit - $v_21;
            goto lab47;
        }
        $this->bra = $this->cursor;
        $this->slice_del();
        if (!$this->r_stem_suffix_chain_before_ki()) {
            $this->cursor = $this->limit - $v_21;
            goto lab47;
        }
    lab49:
    lab47:
        goto lab45;
    lab46:
        $this->cursor = $this->limit - $v_19;
        $this->ket = $this->cursor;
        $v_25 = $this->limit - $this->cursor;
        if (!$this->r_mark_ndA()) {
            goto lab55;
        }
        goto lab56;
    lab55:
        $this->cursor = $this->limit - $v_25;
        if (!$this->r_check_vowel_harmony()) {
            goto lab54;
        }
        if ($this->find_among_b(self::A_5) === 0) {
            goto lab54;
        }
    lab56:
        $v_26 = $this->limit - $this->cursor;
        if (!$this->r_mark_lArI()) {
            goto lab57;
        }
        $this->bra = $this->cursor;
        $this->slice_del();
        goto lab58;
    lab57:
        $this->cursor = $this->limit - $v_26;
        if (!$this->r_mark_sU()) {
            goto lab59;
        }
        $this->bra = $this->cursor;
        $this->slice_del();
        $v_27 = $this->limit - $this->cursor;
        $this->ket = $this->cursor;
        if (!$this->r_mark_lAr()) {
            $this->cursor = $this->limit - $v_27;
            goto lab60;
        }
        $this->bra = $this->cursor;
        $this->slice_del();
        if (!$this->r_stem_suffix_chain_before_ki()) {
            $this->cursor = $this->limit - $v_27;
            goto lab60;
        }
    lab60:
        goto lab58;
    lab59:
        $this->cursor = $this->limit - $v_26;
        if (!$this->r_stem_suffix_chain_before_ki()) {
            goto lab54;
        }
    lab58:
        goto lab45;
    lab54:
        $this->cursor = $this->limit - $v_19;
        $this->ket = $this->cursor;
        $v_28 = $this->limit - $this->cursor;
        if (!$this->r_check_vowel_harmony()) {
            goto lab62;
        }
        if ($this->find_among_b(self::A_9) === 0) {
            goto lab62;
        }
        goto lab63;
    lab62:
        $this->cursor = $this->limit - $v_28;
        if (!$this->r_check_vowel_harmony()) {
            goto lab61;
        }
        if ($this->find_among_b(self::A_2) === 0) {
            goto lab61;
        }
    lab63:
        $v_29 = $this->limit - $this->cursor;
        if (!$this->r_mark_sU()) {
            goto lab64;
        }
        $this->bra = $this->cursor;
        $this->slice_del();
        $v_30 = $this->limit - $this->cursor;
        $this->ket = $this->cursor;
        if (!$this->r_mark_lAr()) {
            $this->cursor = $this->limit - $v_30;
            goto lab65;
        }
        $this->bra = $this->cursor;
        $this->slice_del();
        if (!$this->r_stem_suffix_chain_before_ki()) {
            $this->cursor = $this->limit - $v_30;
            goto lab65;
        }
    lab65:
        goto lab66;
    lab64:
        $this->cursor = $this->limit - $v_29;
        if (!$this->r_mark_lArI()) {
            goto lab61;
        }
    lab66:
        goto lab45;
    lab61:
        $this->cursor = $this->limit - $v_19;
        $this->ket = $this->cursor;
        if (!$this->r_check_vowel_harmony()) {
            goto lab67;
        }
        if ($this->find_among_b(self::A_8) === 0) {
            goto lab67;
        }
        $this->bra = $this->cursor;
        $this->slice_del();
        $v_31 = $this->limit - $this->cursor;
        $this->ket = $this->cursor;
        $v_32 = $this->limit - $this->cursor;
        if (!$this->r_mark_possessives()) {
            goto lab69;
        }
        $this->bra = $this->cursor;
        $this->slice_del();
        $v_33 = $this->limit - $this->cursor;
        $this->ket = $this->cursor;
        if (!$this->r_mark_lAr()) {
            $this->cursor = $this->limit - $v_33;
            goto lab70;
        }
        $this->bra = $this->cursor;
        $this->slice_del();
        if (!$this->r_stem_suffix_chain_before_ki()) {
            $this->cursor = $this->limit - $v_33;
            goto lab70;
        }
    lab70:
        goto lab71;
    lab69:
        $this->cursor = $this->limit - $v_32;
        if (!$this->r_mark_lAr()) {
            goto lab72;
        }
        $this->bra = $this->cursor;
        $this->slice_del();
        $v_34 = $this->limit - $this->cursor;
        if (!$this->r_stem_suffix_chain_before_ki()) {
            $this->cursor = $this->limit - $v_34;
            goto lab73;
        }
    lab73:
        goto lab71;
    lab72:
        $this->cursor = $this->limit - $v_32;
        if (!$this->r_stem_suffix_chain_before_ki()) {
            $this->cursor = $this->limit - $v_31;
            goto lab68;
        }
    lab71:
    lab68:
        goto lab45;
    lab67:
        $this->cursor = $this->limit - $v_19;
        $this->ket = $this->cursor;
        $v_35 = $this->limit - $this->cursor;
        if (!$this->r_mark_nUn()) {
            goto lab75;
        }
        goto lab76;
    lab75:
        $this->cursor = $this->limit - $v_35;
        if (!$this->r_check_vowel_harmony()) {
            goto lab74;
        }
        if ($this->find_among_b(self::A_10) === 0) {
            goto lab74;
        }
        if (!$this->r_mark_suffix_with_optional_y_consonant()) {
            goto lab74;
        }
    lab76:
        $this->bra = $this->cursor;
        $this->slice_del();
        $v_36 = $this->limit - $this->cursor;
        $v_37 = $this->limit - $this->cursor;
        $this->ket = $this->cursor;
        if (!$this->r_mark_lAr()) {
            goto lab78;
        }
        $this->bra = $this->cursor;
        $this->slice_del();
        if (!$this->r_stem_suffix_chain_before_ki()) {
            goto lab78;
        }
        goto lab79;
    lab78:
        $this->cursor = $this->limit - $v_37;
        $this->ket = $this->cursor;
        $v_38 = $this->limit - $this->cursor;
        if (!$this->r_mark_possessives()) {
            goto lab81;
        }
        goto lab82;
    lab81:
        $this->cursor = $this->limit - $v_38;
        if (!$this->r_mark_sU()) {
            goto lab80;
        }
    lab82:
        $this->bra = $this->cursor;
        $this->slice_del();
        $v_39 = $this->limit - $this->cursor;
        $this->ket = $this->cursor;
        if (!$this->r_mark_lAr()) {
            $this->cursor = $this->limit - $v_39;
            goto lab83;
        }
        $this->bra = $this->cursor;
        $this->slice_del();
        if (!$this->r_stem_suffix_chain_before_ki()) {
            $this->cursor = $this->limit - $v_39;
            goto lab83;
        }
    lab83:
        goto lab79;
    lab80:
        $this->cursor = $this->limit - $v_37;
        if (!$this->r_stem_suffix_chain_before_ki()) {
            $this->cursor = $this->limit - $v_36;
            goto lab77;
        }
    lab79:
    lab77:
        goto lab45;
    lab74:
        $this->cursor = $this->limit - $v_19;
        $this->ket = $this->cursor;
        if (!$this->r_mark_lArI()) {
            goto lab84;
        }
        $this->bra = $this->cursor;
        $this->slice_del();
        goto lab45;
    lab84:
        $this->cursor = $this->limit - $v_19;
        if (!$this->r_stem_suffix_chain_before_ki()) {
            goto lab85;
        }
        goto lab45;
    lab85:
        $this->cursor = $this->limit - $v_19;
        $this->ket = $this->cursor;
        $v_40 = $this->limit - $this->cursor;
        if (!$this->r_mark_DA()) {
            goto lab87;
        }
        goto lab88;
    lab87:
        $this->cursor = $this->limit - $v_40;
        if (!$this->r_check_vowel_harmony()) {
            goto lab89;
        }
        if (!($this->in_grouping_b(self::G_U))) {
            goto lab89;
        }
        if (!$this->r_mark_suffix_with_optional_y_consonant()) {
            goto lab89;
        }
        goto lab88;
    lab89:
        $this->cursor = $this->limit - $v_40;
        if (!$this->r_check_vowel_harmony()) {
            goto lab86;
        }
        if ($this->find_among_b(self::A_4) === 0) {
            goto lab86;
        }
        if (!$this->r_mark_suffix_with_optional_y_consonant()) {
            goto lab86;
        }
    lab88:
        $this->bra = $this->cursor;
        $this->slice_del();
        $v_41 = $this->limit - $this->cursor;
        $this->ket = $this->cursor;
        $v_42 = $this->limit - $this->cursor;
        if (!$this->r_mark_possessives()) {
            goto lab91;
        }
        $this->bra = $this->cursor;
        $this->slice_del();
        $v_43 = $this->limit - $this->cursor;
        $this->ket = $this->cursor;
        if (!$this->r_mark_lAr()) {
            $this->cursor = $this->limit - $v_43;
            goto lab92;
        }
    lab92:
        goto lab93;
    lab91:
        $this->cursor = $this->limit - $v_42;
        if (!$this->r_mark_lAr()) {
            $this->cursor = $this->limit - $v_41;
            goto lab90;
        }
    lab93:
        $this->bra = $this->cursor;
        $this->slice_del();
        $this->ket = $this->cursor;
        if (!$this->r_stem_suffix_chain_before_ki()) {
            $this->cursor = $this->limit - $v_41;
            goto lab90;
        }
    lab90:
        goto lab45;
    lab86:
        $this->cursor = $this->limit - $v_19;
        $this->ket = $this->cursor;
        $v_44 = $this->limit - $this->cursor;
        if (!$this->r_mark_possessives()) {
            goto lab94;
        }
        goto lab95;
    lab94:
        $this->cursor = $this->limit - $v_44;
        if (!$this->r_mark_sU()) {
            goto lab42;
        }
    lab95:
        $this->bra = $this->cursor;
        $this->slice_del();
        $v_45 = $this->limit - $this->cursor;
        $this->ket = $this->cursor;
        if (!$this->r_mark_lAr()) {
            $this->cursor = $this->limit - $v_45;
            goto lab96;
        }
        $this->bra = $this->cursor;
        $this->slice_del();
        if (!$this->r_stem_suffix_chain_before_ki()) {
            $this->cursor = $this->limit - $v_45;
            goto lab96;
        }
    lab96:
    lab45:
    lab42:
        $this->cursor = $this->limit - $v_18;
        $this->cursor = $this->limit_backward;
        $this->limit_backward = $this->cursor;
        $this->cursor = $this->limit;
        $v_46 = $this->limit - $this->cursor;
        if (!($this->eq_s_b("ad"))) {
            goto lab97;
        }
        $v_47 = $this->limit - $this->cursor;
        if (!($this->eq_s_b("soy"))) {
            $this->cursor = $this->limit - $v_47;
            goto lab98;
        }
    lab98:
        if ($this->cursor > $this->limit_backward) {
            goto lab97;
        }
        return false;
    lab97:
        $this->cursor = $this->limit - $v_46;
        $v_48 = $this->limit - $this->cursor;
        $this->ket = $this->cursor;
        $this->bra = $this->cursor;
        if (!($this->eq_s_b("d"))) {
            goto lab100;
        }
        goto lab101;
    lab100:
        if (!($this->eq_s_b("g"))) {
            goto lab99;
        }
    lab101:
        if (!$this->go_out_grouping_b(self::G_vowel)) {
            goto lab99;
        }
        $v_49 = $this->limit - $this->cursor;
        if (!($this->eq_s_b("a"))) {
            goto lab103;
        }
        goto lab104;
    lab103:
        if (!($this->eq_s_b("\u{131}"))) {
            goto lab102;
        }
    lab104:
        $this->slice_from("\u{131}");
        goto lab105;
    lab102:
        $this->cursor = $this->limit - $v_49;
        if (!($this->eq_s_b("e"))) {
            goto lab107;
        }
        goto lab108;
    lab107:
        if (!($this->eq_s_b("i"))) {
            goto lab106;
        }
    lab108:
        $this->slice_from("i");
        goto lab105;
    lab106:
        $this->cursor = $this->limit - $v_49;
        if (!($this->eq_s_b("o"))) {
            goto lab110;
        }
        goto lab111;
    lab110:
        if (!($this->eq_s_b("u"))) {
            goto lab109;
        }
    lab111:
        $this->slice_from("u");
        goto lab105;
    lab109:
        $this->cursor = $this->limit - $v_49;
        if (!($this->eq_s_b("\u{F6}"))) {
            goto lab112;
        }
        goto lab113;
    lab112:
        if (!($this->eq_s_b("\u{FC}"))) {
            goto lab99;
        }
    lab113:
        $this->slice_from("\u{FC}");
    lab105:
    lab99:
        $this->cursor = $this->limit - $v_48;
        $v_50 = $this->limit - $this->cursor;
        $this->ket = $this->cursor;
        $among_var = $this->find_among_b(self::A_23);
        if (0 === $among_var) {
            goto lab114;
        }
        $this->bra = $this->cursor;
        $this->slice_from(self::AS_23[$among_var - 1]);
    lab114:
        $this->cursor = $this->limit - $v_50;
        $this->cursor = $this->limit_backward;
        return true;
    }
}
