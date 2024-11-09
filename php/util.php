<?php

    function formatDate($date, $abbr = false)
    {
        $months = [
            "január", "február", "március", "április", "május", "június", "július", "augusztus", "szeptember", "október", "november", "december"
        ];
        $amonths = [
            "jan.", "feb.", "márc.", "ápr.", "máj.", "jún.", "júl.", "aug.", "szept.", "okt.", "nov.", "dec."
        ];

        $d = explode(" ", $date)[0];
        $time = explode(" ", $date)[1];

        $y = explode("-", $d)[0];
        $m = explode("-", $d)[1];
        $d = explode("-", $d)[2];

        $result = "";

        if ($y != date("Y")) {
            $result = $result.$y.". ";
        }

        if ($abbr) {
            $result = $result.$amonths[intval($m)-1];
        } else {
            $result = $result.$months[intval($m)-1];
        }

        $result = $result." ".intval($d).", ";
        $t = explode(":", $time);

        return $result.$t[0].":".$t[1];

    }