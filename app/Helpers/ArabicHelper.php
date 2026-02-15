<?php
namespace App\Helpers;

use ArPHP\I18N\Arabic;

class ArabicHelper
{
    public static function normalizeArabic($text)
    {
        // Remove Arabic diacritics (Harakat)
        $diacritics = [
            "\u{064B}", "\u{064C}", "\u{064D}", "\u{064E}",
            "\u{064F}", "\u{0650}", "\u{0651}", "\u{0652}"
        ];
        $text = str_replace($diacritics, '', $text);

        // Normalize Alif variations
        $text = str_replace(['أ', 'إ', 'آ'], 'ا', $text);

        // Replace Taa Marbuta with Haa
        $text = str_replace('ة', 'ه', $text);

        // Handle spaces in compound names
        $text = preg_replace('/\s+عبد\s+/', ' عبد', $text); // Normalize "عبد الرحمن" to "عبدالرحمن"

        // Collapse multiple spaces into a single space
        $text = preg_replace('/\s+/', ' ', $text);

        // Trim any extra spaces
        return trim($text);
    }

    public static function arabicSoundex($text)
    {

        // Define the Soundex mapping for Arabic letters
        $soundexMap = [
            'ا' => 'A', 'ب' => 'B', 'ت' => 'T', 'ث' => 'TH',
            'ج' => 'J', 'ح' => 'H', 'خ' => 'KH', 'د' => 'D',
            'ذ' => 'TH', 'ر' => 'R', 'ز' => 'Z', 'س' => 'S',
            'ش' => 'SH', 'ص' => 'S', 'ض' => 'D', 'ط' => 'T',
            'ظ' => 'TH', 'ع' => 'A', 'غ' => 'GH', 'ف' => 'F',
            'ق' => 'Q', 'ك' => 'K', 'ل' => 'L', 'م' => 'M',
            'ن' => 'N', 'ه' => 'H', 'و' => 'W', 'ي' => 'Y',
            'أ' => 'A', 'إ' => 'A', 'آ' => 'A', 'ة' => 'H'
        ];

        // Normalize the text
        $text = self::normalizeArabic($text);

        // Remove non-Arabic characters
        $text = preg_replace('/[^ا-يأإآة]/u', '', $text);

        // Convert the Arabic text to Soundex using the mapping
        $soundex = '';
        for ($i = 0; $i < mb_strlen($text); $i++) {
            $char = mb_substr($text, $i, 1);
            $soundex .= $soundexMap[$char] ?? ''; // Use mapped value or skip unknown characters
        }
        return strtoupper($soundex);
    }
}
