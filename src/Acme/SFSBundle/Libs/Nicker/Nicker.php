<?php
/*******************************************************************************
 * La siguiente clase genera nombres de usuario aleatorios
 * 
 * Creada el: 17/07/2014
 ******************************************************************************/

class Nicker {
    public $words_frags = array(
        " " => null,
        "a" => null,
        "b" => array("_", "l", "r"),
        "c" => array("_", "l", "r", "h"),
        "d" => array("_", "l", "r"),
        "e" => null,
        "f" => array("_", "l", "r"),
        "g" => array("_", "l", "r"),
        "h" => array("_"),
        "i" => null,
        "j" => array("_"),
        "k" => array("_", "r"),
        "l" => array("_", "l"),
        "m" => array("_"),
        "n" => array("_"),
        "o" => null,
        "p" => array("_", "l", "r"),
        "q" => array("ui", "ue"),
        "r" => array("_", "r"),
        "s" => array("_", "h"),
        "t" => array("_", "l", "r", "h"),
        "u" => null,
        "v" => array("_", "r"),
        "w" => array("_", "r"),
        "x" => array("_"),
        "y" => array("_"),
        "z" => array("_")
    );

    function getNextLetter($letter_original) {
        $letter_original = substr($letter_original, -1);
        $letters = $this->words_frags[$letter_original];
        $posibilities = array();
        if ($letters === null) {
            $alpha = str_replace($letter_original, "", "abcdefghijklmopqrstuvwxyz");
            for ($x = 0; $x < strlen($alpha); ++$x) {
                $value = $alpha[$x];
                array_push($posibilities, $value);
            }
        } else {
            foreach ($letters as $letter) {
                if ($letter === "_") {
                    $vocals = str_replace($letter_original, "", "aeiou");
                    for ($x = 0; $x < strlen($vocals); ++$x) {
                        $value = $vocals[$x];
                        array_push($posibilities, $value);
                    }
                } else if ($letter !== $letter_original) {
                    array_push($posibilities, $letter);
                }
            }
        }
        $random_index = rand(0, count($posibilities) - 1);
        return $posibilities[$random_index];
    }
    
    function getText($size){
        $txt = "";
        while($size > 0){
            --$size;
            $txt .= $this->getName(rand(1, 20)) . " ";
        }
        return $txt;
    }
    
    function getName($size) {
        $name = "";
        $last = $this->getNextLetter(" ");
        //$first = true;
        while ($size > 0 || (substr_count("aeious", $last) === 0)) {
            $letter = $this->getNextLetter($last);
            $last = $letter;
            //if($first){
            //    $letter = strtoupper($letter);
            //    $first = false;
            //}
            $name .= $letter;
            --$size;
            $last = substr($last, -1);
        }
        return ucwords(strtolower($name));
    }
}
