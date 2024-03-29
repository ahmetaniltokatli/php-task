<?php

/*
  On Tanimlar:
    - Lutfen bu task icin bir github reposu acin ve linki paylasin.
    - Dilediginiz kaynak/snippet'tan faydalanabilirsiniz. Lutfen kullandiginiz kaynaklari comment olarak belirtin.
    - Dilediginiz framework/library'leri kullanabilirsiniz.
    - Dilediginiz PHP versiyonunu kullanabilirsiniz.

  $inputColors Kosullari:
    - Tek bir renk string'inde, ayrilmis sekilde birden fazla renk adi bulunabilir.
    - Multi renk iceren satirlar ['/', '_', '-', ',', ' '] karakterleri ile, bosluklu veya bosluksuz ayrilmis olabilir.
    - Ayni satirda birden fazla ayirici karakter kullanilmis olabilir.
    - İki kelimeden olusan renk adlari icin, ilk kelime '.' (nokta) karakteri ile kisaltilmis olabilir.
    - Renk adi transliterate edilmis olabilir.

  Sonuc Ciktisi:
    - $inputColors array'inda verilen tum renklerin -tek bir istisna ile- $definedColors array value
        degerlerini donmesini bekliyoruz. İstisna, sonraki maddede belirtilmistir.
    - $inputColors array'inda verilen "B.MAVİSİ" renginin, birden fazla b.mavisi kisaltmasina match
        ettiği icin null donmesi, diger renklerin match donmesi gerekiyor.
 */

class Color
{
    protected static $definedColors = [
        'mavi' => 'blue',
        'bebe mavisi' => 'baby_blue',
        'buz mavisi' => 'ice_blue',
        'petrol mavisi' => 'petrol_blue',
        'petrol yeşili' => 'petrol_green',
        'su yeşili' => 'water_green',
        'yeşil' => 'green',
        'petrol' => 'petrol',
        'siyah' => 'black',
        'lila' => 'lila',
        'lime' => 'lime',
        'kahve rengi' => 'brown',
        'karışık renkli' => 'mixed',
    ];
  
    public static function trimDefinedColors() {
        $trimDefinedColors = [];
        foreach (self::$definedColors as $key => $definedColor) {
            $trimDefinedColors[str_replace(" ", "", $key)] = $definedColor;
        }

        return $trimDefinedColors;
    }

    /**
     * @param string $colorString
     * @return array
     */
    public static function getMatches($colorString)
    {

        $colorArray = self::checkMultiColors($colorString, true);
        $matches = [];

        foreach ($colorArray as $color) {
            $matches[] = self::checkList($color);
        }

        return $matches;
    }


    public static function checkMultiColors($color, $multiCheck = false) {
        $splitCharacters = ['/', '_', '-', ',', ' '];
        $array = [];

        foreach ($splitCharacters as $splitCharacter) {
            if($multiCheck)
                $explodeString = " " . $splitCharacter . " ";
            else
                $explodeString = $splitCharacter;

            if(count(explode($explodeString, $color)) > 1)
            {
                $array = explode($explodeString, $color);
            }
        }

        if(count($array) == 0)
            $array[] = $color;

        return $array;
    }

    public static function checkList($color)
    {
        $array = self::checkMultiColors($color);

        $match = true;
        $matches = [];
        $trimDefinedColors = self::trimDefinedColors();

        foreach ($array as $item) {
            $checkPoint = self::checkPoint($item);

            if($checkPoint) {
                $match = true;
                $matches[] = $checkPoint;
            }else {
                $lowercase = mb_strtolower(str_replace("I", "ı", $item));

                if (!array_key_exists($lowercase, self::$definedColors)  && !array_key_exists($lowercase, $trimDefinedColors))
                    $match = false;
                else
                     $matches[] = isset(self::$definedColors[$lowercase]) ? self::$definedColors[$lowercase] : $trimDefinedColors[$lowercase];
            }

        }

        if ($match)
            return implode(" ", $matches);
        else {
            $implode = mb_strtolower(str_replace("I", "ı", implode(" ", $array)));

            if (!array_key_exists($implode, self::$definedColors)) {
                return $implode;
            } else
                return self::$definedColors[$implode];
        }
    }

    public static function checkPoint($color) {
        $pointArray = explode(".", $color);


        if (count($pointArray) > 1) {
            $keys = array_keys(self::$definedColors);
            $multiNotFound = 0;
            $existKey = "";

            foreach ($keys as $key) {
                $keyExplode = explode(" ", $key);
                if(count($keyExplode) > 1) {
                    $firstCharacter = mb_strtolower(substr($keyExplode[0], 0, 1));

                    if ($firstCharacter == mb_strtolower($pointArray[0]) && self::pre_up($keyExplode[1]) == mb_strtolower($pointArray[1])) {
                        $multiNotFound++;
                        $existKey = $key;
                    }
                }
            }

            if ($multiNotFound == 1)
                return self::$definedColors[$existKey];
            else
                return "null";
        }

        return false;
    }

    public static function pre_up($str){
        $str = str_replace('ş', 's', $str);
        $str = str_replace('ı', 'i', $str);
        return $str;
    }
}

$inputColors = [
    'PETROL-MAVİSİ / SU YEŞİLİ',
    'petrol mavi - BUZ MAVİSİ',
    'B.MAVİSİ - S.YESILI-mavi',
    'lila-rengi - kahverengi',
    'KARIŞIK-RENKLİ',
    'KAHVE RENGİ',
    'siyah lila',
];

foreach ($inputColors as $color) {
    $matches = Color::getMatches($color);
    echo $color . ': ' . implode(', ', $matches);
    echo "<br/>";
}
