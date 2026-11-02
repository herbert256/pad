<?php
// Generated from hungarian.sbl by Snowball 3.2.0 - https://snowballstem.org/

class PadSearchHungarianStemmer extends PadSearchGeneratedStemmer
{
    private const A_0 = [
        ["\u{E1}", -1, 1],
        ["\u{E9}", -1, 2]
    ];

    private const AS_0 = ["a", "e"];

    private const A_1 = [
        ["bb", -1, -1],
        ["cc", -1, -1],
        ["dd", -1, -1],
        ["ff", -1, -1],
        ["gg", -1, -1],
        ["jj", -1, -1],
        ["kk", -1, -1],
        ["ll", -1, -1],
        ["mm", -1, -1],
        ["nn", -1, -1],
        ["pp", -1, -1],
        ["rr", -1, -1],
        ["ccs", -1, -1],
        ["ss", -1, -1],
        ["zzs", -1, -1],
        ["tt", -1, -1],
        ["vv", -1, -1],
        ["ggy", -1, -1],
        ["lly", -1, -1],
        ["nny", -1, -1],
        ["tty", -1, -1],
        ["ssz", -1, -1],
        ["zz", -1, -1]
    ];

    private const A_2 = [
        ["al", -1, 1],
        ["el", -1, 1]
    ];

    private const A_3 = [
        ["ba", -1, -1],
        ["ra", -1, -1],
        ["be", -1, -1],
        ["re", -1, -1],
        ["ig", -1, -1],
        ["nak", -1, -1],
        ["nek", -1, -1],
        ["val", -1, -1],
        ["vel", -1, -1],
        ["ul", -1, -1],
        ["b\u{151}l", -1, -1],
        ["r\u{151}l", -1, -1],
        ["t\u{151}l", -1, -1],
        ["n\u{E1}l", -1, -1],
        ["n\u{E9}l", -1, -1],
        ["b\u{F3}l", -1, -1],
        ["r\u{F3}l", -1, -1],
        ["t\u{F3}l", -1, -1],
        ["\u{FC}l", -1, -1],
        ["n", -1, -1],
        ["an", 19, -1],
        ["ban", 20, -1],
        ["en", 19, -1],
        ["ben", 22, -1],
        ["k\u{E9}ppen", 22, -1],
        ["on", 19, -1],
        ["\u{F6}n", 19, -1],
        ["k\u{E9}pp", -1, -1],
        ["kor", -1, -1],
        ["t", -1, -1],
        ["at", 29, -1],
        ["et", 29, -1],
        ["k\u{E9}nt", 29, -1],
        ["ank\u{E9}nt", 32, -1],
        ["enk\u{E9}nt", 32, -1],
        ["onk\u{E9}nt", 32, -1],
        ["ot", 29, -1],
        ["\u{E9}rt", 29, -1],
        ["\u{F6}t", 29, -1],
        ["hez", -1, -1],
        ["hoz", -1, -1],
        ["h\u{F6}z", -1, -1],
        ["v\u{E1}", -1, -1],
        ["v\u{E9}", -1, -1]
    ];

    private const A_4 = [
        ["\u{E1}n", -1, 2],
        ["\u{E9}n", -1, 1],
        ["\u{E1}nk\u{E9}nt", -1, 2]
    ];

    private const AS_4 = ["e", "a"];

    private const A_5 = [
        ["stul", -1, 1],
        ["astul", 0, 1],
        ["\u{E1}stul", 0, 2],
        ["st\u{FC}l", -1, 1],
        ["est\u{FC}l", 3, 1],
        ["\u{E9}st\u{FC}l", 3, 3]
    ];

    private const AS_5 = ["", "a", "e"];

    private const A_6 = [
        ["\u{E1}", -1, 1],
        ["\u{E9}", -1, 1]
    ];

    private const A_7 = [
        ["k", -1, 3],
        ["ak", 0, 3],
        ["ek", 0, 3],
        ["ok", 0, 3],
        ["\u{E1}k", 0, 1],
        ["\u{E9}k", 0, 2],
        ["\u{F6}k", 0, 3]
    ];

    private const AS_7 = ["a", "e", ""];

    private const A_8 = [
        ["\u{E9}i", -1, 1],
        ["\u{E1}\u{E9}i", 0, 3],
        ["\u{E9}\u{E9}i", 0, 2],
        ["\u{E9}", -1, 1],
        ["k\u{E9}", 3, 1],
        ["ak\u{E9}", 4, 1],
        ["ek\u{E9}", 4, 1],
        ["ok\u{E9}", 4, 1],
        ["\u{E1}k\u{E9}", 4, 3],
        ["\u{E9}k\u{E9}", 4, 2],
        ["\u{F6}k\u{E9}", 4, 1],
        ["\u{E9}\u{E9}", 3, 2]
    ];

    private const AS_8 = ["", "e", "a"];

    private const A_9 = [
        ["a", -1, 1],
        ["ja", 0, 1],
        ["d", -1, 1],
        ["ad", 2, 1],
        ["ed", 2, 1],
        ["od", 2, 1],
        ["\u{E1}d", 2, 2],
        ["\u{E9}d", 2, 3],
        ["\u{F6}d", 2, 1],
        ["e", -1, 1],
        ["je", 9, 1],
        ["nk", -1, 1],
        ["unk", 11, 1],
        ["\u{E1}nk", 11, 2],
        ["\u{E9}nk", 11, 3],
        ["\u{FC}nk", 11, 1],
        ["uk", -1, 1],
        ["juk", 16, 1],
        ["\u{E1}juk", 17, 2],
        ["\u{FC}k", -1, 1],
        ["j\u{FC}k", 19, 1],
        ["\u{E9}j\u{FC}k", 20, 3],
        ["m", -1, 1],
        ["am", 22, 1],
        ["em", 22, 1],
        ["om", 22, 1],
        ["\u{E1}m", 22, 2],
        ["\u{E9}m", 22, 3],
        ["o", -1, 1],
        ["\u{E1}", -1, 2],
        ["\u{E9}", -1, 3]
    ];

    private const AS_9 = ["", "a", "e"];

    private const A_10 = [
        ["id", -1, 1],
        ["aid", 0, 1],
        ["jaid", 1, 1],
        ["eid", 0, 1],
        ["jeid", 3, 1],
        ["\u{E1}id", 0, 2],
        ["\u{E9}id", 0, 3],
        ["i", -1, 1],
        ["ai", 7, 1],
        ["jai", 8, 1],
        ["ei", 7, 1],
        ["jei", 10, 1],
        ["\u{E1}i", 7, 2],
        ["\u{E9}i", 7, 3],
        ["itek", -1, 1],
        ["eitek", 14, 1],
        ["jeitek", 15, 1],
        ["\u{E9}itek", 14, 3],
        ["ik", -1, 1],
        ["aik", 18, 1],
        ["jaik", 19, 1],
        ["eik", 18, 1],
        ["jeik", 21, 1],
        ["\u{E1}ik", 18, 2],
        ["\u{E9}ik", 18, 3],
        ["ink", -1, 1],
        ["aink", 25, 1],
        ["jaink", 26, 1],
        ["eink", 25, 1],
        ["jeink", 28, 1],
        ["\u{E1}ink", 25, 2],
        ["\u{E9}ink", 25, 3],
        ["aitok", -1, 1],
        ["jaitok", 32, 1],
        ["\u{E1}itok", -1, 2],
        ["im", -1, 1],
        ["aim", 35, 1],
        ["jaim", 36, 1],
        ["eim", 35, 1],
        ["jeim", 38, 1],
        ["\u{E1}im", 35, 2],
        ["\u{E9}im", 35, 3]
    ];

    private const AS_10 = ["", "a", "e"];

    private const G_v = ["a"=>true, "e"=>true, "i"=>true, "o"=>true, "u"=>true, "\u{E1}"=>true, "\u{E9}"=>true, "\u{ED}"=>true, "\u{F3}"=>true, "\u{F6}"=>true, "\u{FA}"=>true, "\u{FC}"=>true, "\u{151}"=>true, "\u{171}"=>true];



    protected function r_double(): bool
    {
        $v_1 = $this->limit - $this->cursor;
        if ($this->find_among_b(self::A_1) === 0) {
            return false;
        }
        $this->cursor = $this->limit - $v_1;
        return true;
    }


    protected function r_undouble(): bool
    {
        if ($this->cursor <= $this->limit_backward) {
            return false;
        }
        $this->dec_cursor();
        $this->ket = $this->cursor;
        if ($this->cursor <= $this->limit_backward) {
            return false;
        }
        $this->dec_cursor();
        $this->bra = $this->cursor;
        $this->slice_del();
        return true;
    }


    public function stem(): bool
    {
        $v_1 = $this->cursor;
        $I_p1 = $this->limit;
        $v_2 = $this->cursor;
        if (!($this->in_grouping(self::G_v))) {
            goto lab1;
        }
        $v_3 = $this->cursor;
        if (!$this->go_in_grouping(self::G_v)) {
            goto lab2;
        }
        $this->inc_cursor();
        $I_p1 = $this->cursor;
    lab2:
        $this->cursor = $v_3;
        goto lab3;
    lab1:
        $this->cursor = $v_2;
        if (!$this->go_out_grouping(self::G_v)) {
            goto lab0;
        }
        $this->inc_cursor();
        $I_p1 = $this->cursor;
    lab3:
    lab0:
        $this->cursor = $v_1;
        $this->limit_backward = $this->cursor;
        $this->cursor = $this->limit;
        $v_4 = $this->limit - $this->cursor;
        $this->ket = $this->cursor;
        if ($this->find_among_b(self::A_2) === 0) {
            goto lab4;
        }
        $this->bra = $this->cursor;
        if ($I_p1 > $this->cursor) {
            goto lab4;
        }
        if (!$this->r_double()) {
            goto lab4;
        }
        $this->slice_del();
        if (!$this->r_undouble()) {
            goto lab4;
        }
    lab4:
        $this->cursor = $this->limit - $v_4;
        $v_5 = $this->limit - $this->cursor;
        $this->ket = $this->cursor;
        if ($this->find_among_b(self::A_3) === 0) {
            goto lab5;
        }
        $this->bra = $this->cursor;
        if ($I_p1 > $this->cursor) {
            goto lab5;
        }
        $this->slice_del();
        $this->ket = $this->cursor;
        $among_var = $this->find_among_b(self::A_0);
        if (0 === $among_var) {
            goto lab5;
        }
        $this->bra = $this->cursor;
        if ($I_p1 > $this->cursor) {
            goto lab5;
        }
        $this->slice_from(self::AS_0[$among_var - 1]);
    lab5:
        $this->cursor = $this->limit - $v_5;
        $v_6 = $this->limit - $this->cursor;
        $this->ket = $this->cursor;
        $among_var = $this->find_among_b(self::A_4);
        if (0 === $among_var) {
            goto lab6;
        }
        $this->bra = $this->cursor;
        if ($I_p1 > $this->cursor) {
            goto lab6;
        }
        $this->slice_from(self::AS_4[$among_var - 1]);
    lab6:
        $this->cursor = $this->limit - $v_6;
        $v_7 = $this->limit - $this->cursor;
        $this->ket = $this->cursor;
        $among_var = $this->find_among_b(self::A_5);
        if (0 === $among_var) {
            goto lab7;
        }
        $this->bra = $this->cursor;
        if ($I_p1 > $this->cursor) {
            goto lab7;
        }
        $this->slice_from(self::AS_5[$among_var - 1]);
    lab7:
        $this->cursor = $this->limit - $v_7;
        $v_8 = $this->limit - $this->cursor;
        $this->ket = $this->cursor;
        if ($this->find_among_b(self::A_6) === 0) {
            goto lab8;
        }
        $this->bra = $this->cursor;
        if ($I_p1 > $this->cursor) {
            goto lab8;
        }
        if (!$this->r_double()) {
            goto lab8;
        }
        $this->slice_del();
        if (!$this->r_undouble()) {
            goto lab8;
        }
    lab8:
        $this->cursor = $this->limit - $v_8;
        $v_9 = $this->limit - $this->cursor;
        $this->ket = $this->cursor;
        $among_var = $this->find_among_b(self::A_8);
        if (0 === $among_var) {
            goto lab9;
        }
        $this->bra = $this->cursor;
        if ($I_p1 > $this->cursor) {
            goto lab9;
        }
        $this->slice_from(self::AS_8[$among_var - 1]);
    lab9:
        $this->cursor = $this->limit - $v_9;
        $v_10 = $this->limit - $this->cursor;
        $this->ket = $this->cursor;
        $among_var = $this->find_among_b(self::A_9);
        if (0 === $among_var) {
            goto lab10;
        }
        $this->bra = $this->cursor;
        if ($I_p1 > $this->cursor) {
            goto lab10;
        }
        $this->slice_from(self::AS_9[$among_var - 1]);
    lab10:
        $this->cursor = $this->limit - $v_10;
        $v_11 = $this->limit - $this->cursor;
        $this->ket = $this->cursor;
        $among_var = $this->find_among_b(self::A_10);
        if (0 === $among_var) {
            goto lab11;
        }
        $this->bra = $this->cursor;
        if ($I_p1 > $this->cursor) {
            goto lab11;
        }
        $this->slice_from(self::AS_10[$among_var - 1]);
    lab11:
        $this->cursor = $this->limit - $v_11;
        $v_12 = $this->limit - $this->cursor;
        $this->ket = $this->cursor;
        $among_var = $this->find_among_b(self::A_7);
        if (0 === $among_var) {
            goto lab12;
        }
        $this->bra = $this->cursor;
        if ($I_p1 > $this->cursor) {
            goto lab12;
        }
        $this->slice_from(self::AS_7[$among_var - 1]);
    lab12:
        $this->cursor = $this->limit - $v_12;
        $this->cursor = $this->limit_backward;
        return true;
    }
}
