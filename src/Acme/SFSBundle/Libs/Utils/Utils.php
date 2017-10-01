<?php

class Utils {  
    static function underscoreToCamelCase($string, $first_char_caps = true) {
        if ($first_char_caps == true) {
            $string[0] = strtoupper($string[0]);
        }
        $func = create_function('$c', 'return strtoupper($c[1]);');
        return preg_replace_callback('/_([a-z])/', $func, $string);
    }
    static function generateRandomPass($length, $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789") {        
        return substr(str_shuffle($chars), 0, $length);
    }

    static function formatSizeUnits($bytes) {
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            $bytes = $bytes . ' bytes';
        } elseif ($bytes == 1) {
            $bytes = $bytes . ' byte';
        } else {
            $bytes = '0 bytes';
        }

        return $bytes;
    }

    static function getStringBetween($string1, $string2, $subject) {
        $startsAt = strpos($subject, $string1) + strlen($string1);
        $endsAt = strpos($subject, $string2, $startsAt);
        $result = substr($subject, $startsAt, $endsAt - $startsAt);
        return $result;
    }

    static function extractFirstVideo($contenido, $play_button = false) {
        $first_video = $contenido;
        $first_video = strip_tags($first_video, "<iframe>");
        preg_match_all('/<iframe[^>]+>/i', $first_video, $result);
        if (isset($result[0][0])) {
            $first_video = $result[0][0];
        } else {
            $first_video = null;
        }
        //$first_video = str_ireplace("iframe", 'iframe style="width: 240px;height: 200px;" ', $first_video) . "</iframe>";
        //Youtube filter
        if (self::containsStringInsensitive($first_video, "youtube")) {
            $video_url = $first_video;
            //preg_match('/src="([^"]+)"/', $video_url, $match);
            //$video_url = $match[1];
            //http://www.youtube.com/embed/w5hI6Hx-dj8?wmode=transparent
            $video_url = self::getStringBetween("embed/", '"', $video_url);
            //$array = explode("/embed/", $video_url);
            //if (count($array) > 1) {
            //    $video_url = $array[0];
            //}

            if ($video_url != "") {
                if ($play_button) {
                    $first_video = "http://i3.ytimg.com/vi$video_url/default.jpg"; //'http://img.youtube.com/vi/' . $video_url . '/0.jpg';
                } else {
                    $first_video = "http://img.youtube.com/vi/$video_url/0.jpg"; //'http://img.youtube.com/vi/' . $video_url . '/0.jpg';
                }
            }

            /*
              parse_str(parse_url($video_url, PHP_URL_QUERY), $my_array_of_vars);
              $video_url = $my_array_of_vars['v'];

              if($video_url != ""){
              $first_video = "http://img.youtube.com/vi/$video_url/0.jpg";
              }
             */
        }
        //    return "";
        //}
        if (self::containsString($first_video, "<") || $first_video == "") {
            return null;
        }
        return $first_video;
    }

    static function getSecureJsonDecode($json) {
        try {
            return self::jsonDecode($json);
        } catch (Exception $ex) {
            return array();
        }
    }

    static function extractFirstImageURL($contenido) {
        $first_img = $contenido;
        $first_img = strip_tags($first_img, "<img>");
        preg_match_all('/<img[^>]+>/i', $first_img, $result);

        if (isset($result[0][0])) {
            $first_img = $result[0][0];
        } else {
            $first_img = null;
        }

        if ($first_img != null) {
            $url = preg_match_all('/src=\"[^\"]+\"/i', $first_img, $result);
            //echo($result[0][0]);
            if (isset($result[0][0])) {
                $url = $result[0][0];
                $url = explode('"', $url);
                if (isset($url[1])) {
                    $first_img = $url[1];
                }
            }
        }
        if (self::containsString($first_img, "o1.t26.net")) {
            $first_img = null;
        }
        if (self::containsString($first_img, "kn3")) {
            $first_img = null;
        }
        /*
          if ($first_img == null) {
          $first_img = null;
          }
         */
        //if ($first_img != "" && !(Utils::containsString($first_img, "gif") || Utils::containsString($first_img, "GIF"))) {
        //    $first_img = "http://proxy.boxresizer.com/convert?resize=220;source=" . $first_img;
        //}
        //http://proxy.boxresizer.com/convert?resize=300;source=
        return $first_img;
    }

    static function isMobil() {
        $useragent = "HTTP_USER_AGENT";
        if (preg_match('/android|avantgo|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i', $useragent) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|e\-|e\/|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(di|rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|xda(\-|2|g)|yas\-|your|zeto|zte\-/i', substr($useragent, 0, 4))) {
            return true;
        }
        return false;
    }

    static function isRobot() {
        $robots = array(
            "Accoona-AI-Agent",
            "AOLspider",
            "BlackBerry",
            "bot@bot.bot",
            "CazoodleBot",
            "CFNetwork",
            "ConveraCrawler",
            "Cynthia",
            "Dillo",
            "discoveryengine.com",
            "DoCoMo",
            "ee://aol/http",
            "exactseek.com",
            "fast.no",
            "FAST MetaWeb",
            "FavOrg",
            "FS-Web",
            "Gigabot",
            "GOFORITBOT",
            "gonzo",
            "Googlebot-Image",
            "holmes",
            "HTC_P4350",
            "HTML2JPG Blackbox",
            "http://www.uni-koblenz.de/~flocke/robot-info.txt",
            "iArchitect",
            "ia_archiver",
            "ICCrawler",
            "ichiro",
            "IEAutoDiscovery",
            "ilial",
            "IRLbot",
            "Keywen",
            "kkliihoihn nlkio",
            "larbin",
            "libcurl-agent",
            "libwww-perl",
            "Mediapartners-Google",
            "Metasearch Crawler",
            "Microsoft URL Control",
            "MJ12bot",
            "T-H-U-N-D-E-R-S-T-O-N-E",
            "voodoo-it",
            "www.aramamotorusearchengine.com",
            "archive.org_bot",
            "Teoma",
            "Ask Jeeves",
            "AvantGo",
            "Exabot-Images",
            "Exabot",
            "Google Keyword Tool",
            "Googlebot",
            "heritrix",
            "www.livedir.net",
            "iCab",
            "Interseek",
            "jobs.de",
            "MJ12bot",
            "pmoz.info",
            "SnapPreviewBot",
            "Slurp",
            "Danger hiptop",
            "MQBOT",
            "msnbot-media",
            "msnbot",
            "MSRBOT",
            "NetObjects Fusion",
            "nicebot",
            "nrsbot",
            "Ocelli",
            "Pagebull",
            "PEAR HTTP_Request class",
            "Pluggd/Nutch",
            "psbot",
            "Python-urllib",
            "Regiochannel",
            "SearchEngine",
            "Seekbot",
            "segelsuche.de",
            "Semager",
            "ShopWiki",
            "Snappy",
            "Speedy Spider",
            "sproose",
            "TurnitinBot",
            "Twiceler",
            "VB Project",
            "VisBot",
            "voyager",
            "VWBOT",
            "Wells Search",
            "West Wind",
            "Wget",
            "WWW-Mechanize",
            "www.show-tec.net",
            "xxyyzz",
            "yacybot",
            "Yahoo-MMCrawler",
            "yetibot",
        );

        $is_robot = false;
        foreach ($robots as $robot) {
            if (stristr($_SERVER["HTTP_USER_AGENT"], $robot)) {
                $is_robot = true;
                break;
            }
        }

        return $is_robot;
    }

    static function getSecureHTMLString($value) {
        $value = str_replace("&", "&amp;", $value);
        $value = str_replace('"', "&quot;", $value);
        $value = str_replace("<", "&lt;", $value);
        $value = str_replace(">", "&gt;", $value);
        return $value;
    }

    static function getSanitizedFileName($string) {

        $string = strtolower($string);
        $string = str_replace("ñ", "ni", $string);
        $match = array("/\s+/", "/[^a-zA-Z0-9\-]/", "/-+/", "/^-+/", "/-+$/");
        $replace = array("-", "", "-", "", "");
        $string = preg_replace($match, $replace, $string);
        //$string = strtolower($string);
        return $string;
    }

    static function countStringOcurrences($string, $ocurrence) {
        if ($ocurrence == "") {
            return 0;
        }
        return substr_count($string, $ocurrence);
    }

    static function seems_utf8($str) {
        $length = strlen($str);
        for ($i = 0; $i < $length; $i++) {
            $c = ord($str[$i]);
            if ($c < 0x80)
                $n = 0;# 0bbbbbbb
            elseif (($c & 0xE0) == 0xC0)
                $n = 1;# 110bbbbb
            elseif (($c & 0xF0) == 0xE0)
                $n = 2;# 1110bbbb
            elseif (($c & 0xF8) == 0xF0)
                $n = 3;# 11110bbb
            elseif (($c & 0xFC) == 0xF8)
                $n = 4;# 111110bb
            elseif (($c & 0xFE) == 0xFC)
                $n = 5;# 1111110b
            else
                return false;# Does not match any model
            for ($j = 0; $j < $n; $j++) { # n bytes matching 10bbbbbb follow ?
                if (( ++$i == $length) || ((ord($str[$i]) & 0xC0) != 0x80))
                    return false;
            }
        }
        return true;
    }

    /**
     * Converts all accent characters to ASCII characters.
     *
     * If there are no accent characters, then the string given is just returned.
     *
     * @param string $string Text that might have accent characters
     * @return string Filtered string with replaced "nice" characters.
     */
    static function remove_accents($string) {
        if (!preg_match('/[\x80-\xff]/', $string))
            return $string;

        if (self::seems_utf8($string)) {
            $chars = array(
                // Decompositions for Latin-1 Supplement
                chr(195) . chr(128) => 'A', chr(195) . chr(129) => 'A',
                chr(195) . chr(130) => 'A', chr(195) . chr(131) => 'A',
                chr(195) . chr(132) => 'A', chr(195) . chr(133) => 'A',
                chr(195) . chr(135) => 'C', chr(195) . chr(136) => 'E',
                chr(195) . chr(137) => 'E', chr(195) . chr(138) => 'E',
                chr(195) . chr(139) => 'E', chr(195) . chr(140) => 'I',
                chr(195) . chr(141) => 'I', chr(195) . chr(142) => 'I',
                chr(195) . chr(143) => 'I', chr(195) . chr(145) => 'N',
                chr(195) . chr(146) => 'O', chr(195) . chr(147) => 'O',
                chr(195) . chr(148) => 'O', chr(195) . chr(149) => 'O',
                chr(195) . chr(150) => 'O', chr(195) . chr(153) => 'U',
                chr(195) . chr(154) => 'U', chr(195) . chr(155) => 'U',
                chr(195) . chr(156) => 'U', chr(195) . chr(157) => 'Y',
                chr(195) . chr(159) => 's', chr(195) . chr(160) => 'a',
                chr(195) . chr(161) => 'a', chr(195) . chr(162) => 'a',
                chr(195) . chr(163) => 'a', chr(195) . chr(164) => 'a',
                chr(195) . chr(165) => 'a', chr(195) . chr(167) => 'c',
                chr(195) . chr(168) => 'e', chr(195) . chr(169) => 'e',
                chr(195) . chr(170) => 'e', chr(195) . chr(171) => 'e',
                chr(195) . chr(172) => 'i', chr(195) . chr(173) => 'i',
                chr(195) . chr(174) => 'i', chr(195) . chr(175) => 'i',
                chr(195) . chr(177) => 'n', chr(195) . chr(178) => 'o',
                chr(195) . chr(179) => 'o', chr(195) . chr(180) => 'o',
                chr(195) . chr(181) => 'o', chr(195) . chr(182) => 'o',
                chr(195) . chr(182) => 'o', chr(195) . chr(185) => 'u',
                chr(195) . chr(186) => 'u', chr(195) . chr(187) => 'u',
                chr(195) . chr(188) => 'u', chr(195) . chr(189) => 'y',
                chr(195) . chr(191) => 'y',
                // Decompositions for Latin Extended-A
                chr(196) . chr(128) => 'A', chr(196) . chr(129) => 'a',
                chr(196) . chr(130) => 'A', chr(196) . chr(131) => 'a',
                chr(196) . chr(132) => 'A', chr(196) . chr(133) => 'a',
                chr(196) . chr(134) => 'C', chr(196) . chr(135) => 'c',
                chr(196) . chr(136) => 'C', chr(196) . chr(137) => 'c',
                chr(196) . chr(138) => 'C', chr(196) . chr(139) => 'c',
                chr(196) . chr(140) => 'C', chr(196) . chr(141) => 'c',
                chr(196) . chr(142) => 'D', chr(196) . chr(143) => 'd',
                chr(196) . chr(144) => 'D', chr(196) . chr(145) => 'd',
                chr(196) . chr(146) => 'E', chr(196) . chr(147) => 'e',
                chr(196) . chr(148) => 'E', chr(196) . chr(149) => 'e',
                chr(196) . chr(150) => 'E', chr(196) . chr(151) => 'e',
                chr(196) . chr(152) => 'E', chr(196) . chr(153) => 'e',
                chr(196) . chr(154) => 'E', chr(196) . chr(155) => 'e',
                chr(196) . chr(156) => 'G', chr(196) . chr(157) => 'g',
                chr(196) . chr(158) => 'G', chr(196) . chr(159) => 'g',
                chr(196) . chr(160) => 'G', chr(196) . chr(161) => 'g',
                chr(196) . chr(162) => 'G', chr(196) . chr(163) => 'g',
                chr(196) . chr(164) => 'H', chr(196) . chr(165) => 'h',
                chr(196) . chr(166) => 'H', chr(196) . chr(167) => 'h',
                chr(196) . chr(168) => 'I', chr(196) . chr(169) => 'i',
                chr(196) . chr(170) => 'I', chr(196) . chr(171) => 'i',
                chr(196) . chr(172) => 'I', chr(196) . chr(173) => 'i',
                chr(196) . chr(174) => 'I', chr(196) . chr(175) => 'i',
                chr(196) . chr(176) => 'I', chr(196) . chr(177) => 'i',
                chr(196) . chr(178) => 'IJ', chr(196) . chr(179) => 'ij',
                chr(196) . chr(180) => 'J', chr(196) . chr(181) => 'j',
                chr(196) . chr(182) => 'K', chr(196) . chr(183) => 'k',
                chr(196) . chr(184) => 'k', chr(196) . chr(185) => 'L',
                chr(196) . chr(186) => 'l', chr(196) . chr(187) => 'L',
                chr(196) . chr(188) => 'l', chr(196) . chr(189) => 'L',
                chr(196) . chr(190) => 'l', chr(196) . chr(191) => 'L',
                chr(197) . chr(128) => 'l', chr(197) . chr(129) => 'L',
                chr(197) . chr(130) => 'l', chr(197) . chr(131) => 'N',
                chr(197) . chr(132) => 'n', chr(197) . chr(133) => 'N',
                chr(197) . chr(134) => 'n', chr(197) . chr(135) => 'N',
                chr(197) . chr(136) => 'n', chr(197) . chr(137) => 'N',
                chr(197) . chr(138) => 'n', chr(197) . chr(139) => 'N',
                chr(197) . chr(140) => 'O', chr(197) . chr(141) => 'o',
                chr(197) . chr(142) => 'O', chr(197) . chr(143) => 'o',
                chr(197) . chr(144) => 'O', chr(197) . chr(145) => 'o',
                chr(197) . chr(146) => 'OE', chr(197) . chr(147) => 'oe',
                chr(197) . chr(148) => 'R', chr(197) . chr(149) => 'r',
                chr(197) . chr(150) => 'R', chr(197) . chr(151) => 'r',
                chr(197) . chr(152) => 'R', chr(197) . chr(153) => 'r',
                chr(197) . chr(154) => 'S', chr(197) . chr(155) => 's',
                chr(197) . chr(156) => 'S', chr(197) . chr(157) => 's',
                chr(197) . chr(158) => 'S', chr(197) . chr(159) => 's',
                chr(197) . chr(160) => 'S', chr(197) . chr(161) => 's',
                chr(197) . chr(162) => 'T', chr(197) . chr(163) => 't',
                chr(197) . chr(164) => 'T', chr(197) . chr(165) => 't',
                chr(197) . chr(166) => 'T', chr(197) . chr(167) => 't',
                chr(197) . chr(168) => 'U', chr(197) . chr(169) => 'u',
                chr(197) . chr(170) => 'U', chr(197) . chr(171) => 'u',
                chr(197) . chr(172) => 'U', chr(197) . chr(173) => 'u',
                chr(197) . chr(174) => 'U', chr(197) . chr(175) => 'u',
                chr(197) . chr(176) => 'U', chr(197) . chr(177) => 'u',
                chr(197) . chr(178) => 'U', chr(197) . chr(179) => 'u',
                chr(197) . chr(180) => 'W', chr(197) . chr(181) => 'w',
                chr(197) . chr(182) => 'Y', chr(197) . chr(183) => 'y',
                chr(197) . chr(184) => 'Y', chr(197) . chr(185) => 'Z',
                chr(197) . chr(186) => 'z', chr(197) . chr(187) => 'Z',
                chr(197) . chr(188) => 'z', chr(197) . chr(189) => 'Z',
                chr(197) . chr(190) => 'z', chr(197) . chr(191) => 's',
                // Euro Sign
                chr(226) . chr(130) . chr(172) => 'E',
                // GBP (Pound) Sign
                chr(194) . chr(163) => '');

            $string = strtr($string, $chars);
        } else {
            // Assume ISO-8859-1 if not UTF-8
            $chars['in'] = chr(128) . chr(131) . chr(138) . chr(142) . chr(154) . chr(158)
                    . chr(159) . chr(162) . chr(165) . chr(181) . chr(192) . chr(193) . chr(194)
                    . chr(195) . chr(196) . chr(197) . chr(199) . chr(200) . chr(201) . chr(202)
                    . chr(203) . chr(204) . chr(205) . chr(206) . chr(207) . chr(209) . chr(210)
                    . chr(211) . chr(212) . chr(213) . chr(214) . chr(216) . chr(217) . chr(218)
                    . chr(219) . chr(220) . chr(221) . chr(224) . chr(225) . chr(226) . chr(227)
                    . chr(228) . chr(229) . chr(231) . chr(232) . chr(233) . chr(234) . chr(235)
                    . chr(236) . chr(237) . chr(238) . chr(239) . chr(241) . chr(242) . chr(243)
                    . chr(244) . chr(245) . chr(246) . chr(248) . chr(249) . chr(250) . chr(251)
                    . chr(252) . chr(253) . chr(255);

            $chars['out'] = "EfSZszYcYuAAAAAACEEEEIIIINOOOOOOUUUUYaaaaaaceeeeiiiinoooooouuuuyy";

            $string = strtr($string, $chars['in'], $chars['out']);
            $double_chars['in'] = array(chr(140), chr(156), chr(198), chr(208), chr(222), chr(223), chr(230), chr(240), chr(254));
            $double_chars['out'] = array('OE', 'oe', 'AE', 'DH', 'TH', 'ss', 'ae', 'dh', 'th');
            $string = str_replace($double_chars['in'], $double_chars['out'], $string);
        }

        return $string;
    }

    static function clearStringAcent($string) {
        /*
          $string = str_replace("Á", "A", $string);
          $string = str_replace("É", "E", $string);
          $string = str_replace("Í", "I", $string);
          $string = str_replace("Ó", "O", $string);
          $string = str_replace("Ú", "U", $string);
          $string = str_replace("Ñ", "N", $string);
          $string = str_replace("á", "a", $string);
          $string = str_replace("é", "e", $string);
          $string = str_replace("í", "i", $string);
          $string = str_replace("ó", "o", $string);
          $string = str_replace("ú", "u", $string);
          $string = str_replace("ñ", "n", $string);
          return $string;
         */
        return self::remove_accents($string);
    }

    static function getCurrentSiteURL() {
        $pageURL = 'http';
        if ($_SERVER["HTTPS"] == "on") {
            $pageURL .= "s";
        }
        return "$pageURL://" . $_SERVER['HTTP_HOST'];
    }

    static function getCurrentPageURL() {
        $pageURL = 'http';
        if ($_SERVER["HTTPS"] == "on") {
            $pageURL .= "s";
        }
        $pageURL .= "://";
        if ($_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
        } else {
            $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
        }
        return $pageURL;
    }

    static function getArrayKeyByValue($val, $array) {
        foreach ($array as $key => $value) {
            if ($value == $val) {
                return $key;
            }
        }
        return null;
    }

    static function getArrayRelPair($array) {
        $array_r = array();
        foreach ($array as $value) {
            $array_r["$value"] = $value;
        }
        return $array_r;
    }

    static function containsStringInsensitive($text, $find) {
        return stristr($text, $find);
    }

    static function containsArrayInsensitive($text, $array) {
        foreach ($array as $value) {
            if ($value == "") {
                continue;
            }
            if (stristr($text, $value)) {
                return true;
            }
        }
        return false;
    }

    static function containsString($text, $find) {
        return strpos(" " . $text . " ", $find) || $find == $text;
    }

    static function maskValue($mask, $value) {
        if ($value == null || $value == "" || $value == "null") {
            return "";
        }
        return sprintf($mask, $value);
    }

    static function jsonEncode($var) {
        return json_encode($var);
    }

    static function jsonDecode($text) {
        try {
            return json_decode($text, true);
        } catch (Exception $x) {
            return null;
        }
    }

    static function getRealRemoteIP() {
        if (isset($_SERVER["HTTP_CLIENT_IP"])) {
            return $_SERVER["HTTP_CLIENT_IP"];
        } elseif (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
            return $_SERVER["HTTP_X_FORWARDED_FOR"];
        } elseif (isset($_SERVER["HTTP_X_FORWARDED"])) {
            return $_SERVER["HTTP_X_FORWARDED"];
        } elseif (isset($_SERVER["HTTP_FORWARDED_FOR"])) {
            return $_SERVER["HTTP_FORWARDED_FOR"];
        } elseif (isset($_SERVER["HTTP_FORWARDED"])) {
            return $_SERVER["HTTP_FORWARDED"];
        } else {
            return $_SERVER["REMOTE_ADDR"];
        }
    }

    static function isAssocArray($arr) {
        return array_keys($arr) !== range(0, count($arr) - 1);
    }

    static function isGitFolder() {
        return file_exists(Utils::getAbsolutePath() . "deplist.txt");
    }

    static function strToBool($str) {
        $str = strtoupper($str);
        $str = str_replace(" ", "", $str);
        if ($str == "TRUE") {
            return TRUE;
        }
        if ($str == "FALSE") {
            return FALSE;
        }
        return FALSE;
    }

    /*
      static function getAbsolutePath($file = "") {
      $folder_depth = substr_count($_SERVER["PHP_SELF"], "/");
      if ($folder_depth == false)
      $folder_depth = 1;

      $path_tmp = str_repeat("../", $folder_depth - 1);
      $path_tmp_git = str_repeat("../", $folder_depth - 3);
      $path_tmp_xampp = str_repeat("../", $folder_depth - 2);

      if (file_exists($path_tmp_xampp . "deplist.txt")) {
      return ($path_tmp_git . $file);
      } elseif(file_exists($path_tmp_xampp . "xampp.txt")) {
      return ($path_tmp_xampp . $file);
      }else{
      return ($path_tmp . $file);
      }
      }
     */

    static function getShortDate($originalDate) {
        if ($originalDate == null || $originalDate == "") {
            return "";
        }
        //d ([ .\t-])* m
        return date("d M", strtotime($originalDate));
    }

    static function getFrindlyTimeName($segundos) {
//dias es la division de n segs entre 86400 segundos que representa un dia;
        $anios = floor($segundos / 31104000);

        $meses = floor($segundos / 2592000);

        $dias = floor($segundos / 86400);

//mod_hora es el sobrante, en horas, de la division de días;    
        $mod_hora = $segundos % 86400;

//hora es la division entre el sobrante de horas y 3600 segundos que representa una hora;
        $horas = floor($mod_hora / 3600);

//mod_minuto es el sobrante, en minutos, de la division de horas;       
        $mod_minuto = $mod_hora % 3600;

//minuto es la division entre el sobrante y 60 segundos que representa un minuto;
        $minutos = floor($mod_minuto / 60);
        if ($minutos <= 0) {
            if ($segundos <= 1) {
                return 'instantes';
            }
            return $segundos . ' segundos';
        } elseif ($horas <= 0) {
            if ($minutos == 1) {
                return $minutos . ' minuto';
            }
            return $minutos . ' minutos';
        } elseif ($dias <= 0) {
            if ($horas == 1) {
                return $horas . ' hora y ' . $minutos . ' minutos';
            }
            return $horas . ' horas y ' . $minutos . ' minutos';
        } elseif ($meses <= 0) {
            if ($dias == 1) {
                return $dias . ' dia y ' . $horas . ' horas';
            }
            return $dias . ' dias y ' . $horas . ' horas';
        } elseif ($anios <= 0) {
            if ($meses == 1) {
                return $meses . ' mes';
            }
            return $meses . ' meses';
        } else {
            if ($anios == 1) {
                return $anios . ' año';
            }
            return $anios . ' años';
        }
    }

    static function getDateSeconds($fecha_unix) {
//obtener la hora en formato unix
        $ahora = time();

        $segundos = 0;
//obtener la diferencia de segundos
        if (is_int($fecha_unix)) {
            $segundos = $ahora - $fecha_unix;
        } else {
            $segundos = $ahora - strtotime($fecha_unix);
        }
        return $segundos;
    }

    static function getFriendlyTime($fecha_unix) {
        $segundos = self::getDateSeconds($fecha_unix);
        return self::getFrindlyTimeName($segundos);
    }

    static function getFriendlyTimeFromDatetime($datetime) {
        if($datetime === null){
            return "";
        }
        $fecha_unix = $datetime->getTimestamp();
        return self::getFriendlyTime($fecha_unix);
    }

    static function proccessPHPTemplate($file, $data = null) {
        ob_start();
        include $file;
        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }

    static function getTemplate($file) {
        $document = file_get_contents($file);
        return $document;
    }

    static function proccessTemplate($file, $params) {
        $document = file_get_contents($file);
        return Utils::proccessTemplateContent($document, $params);
    }

    static function proccessTemplateContent($content, $params) {
        $document = $content;
        foreach (array_keys($params) as $x) {
            $value = $params[$x];
            $segs = explode("<@>", $value);
            if ($segs[0] == "" && isset($segs[1])) {
                $value = $segs[1];
            }
            $document = str_replace("<%=$x%>", $value, $document);
        }
        return $document;
    }

    static function sendTwigEmail(Symfony\Bundle\FrameworkBundle\Controller\Controller $c, $from, $to, $cc, $subject, $template, $params = array()) {
        $html = $c->renderView($template, $params);
        return self::sendHTMLEmail($from, $cc, $to, $subject, $html);
    }

    static function sendHTMLEmail($from, $cc, $to, $subject, $html) {
//$document = Utils::proccessTemplate($file, $params);
        $headers = "From: $from\r\n";
        $headers .= "Reply-To: $to\r\n";
        $headers .= "CC: $cc\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        try {
            return @mail($to, $subject, $html, $headers);
        } catch (\Exception $x) {
            return false;
        }
    }

    static function getAjaxParsed($id, $val) {
        return "$id<=@=>$val<[@]>";
    }

    static function isDevMode() {
        return file_exists("../check");
    }

    static function getNumber($lenght) {
        $number = microtime() . rand(0, 1000);
        return substr($number . "", -$lenght);
    }

    static function getValue($origen, $value) {
        $result = $origen[$value];
        if (!isset($result))
            return "";
        return $result;
    }

    static function getParam($request, $value, $default) {
        if (!isset($request[$value]))
            return $default;
        return $request[$value];
    }

    static function getRequestParam($value, $default = "") {
        return self::getParam($_REQUEST, $value, $default);
    }

    static function isURL($url) {
        //$url = "http://codekarate.com";
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            return true;
        } else {
            return false;
        }
    }

    static function checkArray($result) {
        return count($result) > 0;
    }

    static function checkGetArray(&$result) {
        if ($result == null | (count($result) <= 0)) {
            return Array();
        }
        return $result;
    }

    static function getSizedString($string, $size) {
        if (strlen($string) <= $size)
            return $string;
        $sizedStr = substr($string, 0, $size - 3) . "...";
        return $sizedStr;
    }

    static function getPathFromURI($uri) {
        $count = 0;
        $parts = explode("/", $uri);
        $end = (count($parts) - 1);
        $path = "";
        while (true) {
            if ($count == $end)
                break;
            if ($parts[$count] != "")
                $path .= "/" . $parts[$count];
            ++$count;
        }
        return $path;
    }

    static function recursiveDelete($path) {
        $files = glob("$path/*");
        foreach ($files as $file) {
            if (is_dir($file) && !is_link($file)) {
                Utils::recursiveDelete($file);
            } else {
                unlink($file);
            }
        }
        if (is_dir($path))
            rmdir($path);
    }

    static function contains($array, $value) {
        foreach ($array as $x)
            if ($x == $value)
                return true;
        return false;
    }

}
