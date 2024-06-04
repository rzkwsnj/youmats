<?php


namespace App\Helpers\Classes;


use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class GenerateSentence
{
    private $default_locale;
    private $result_ar;
    private $result_en;
    private $locales;

    /**
     * GenerateSentence constructor.
     */
    public function __construct()
    {
        $this->locales = LaravelLocalization::getSupportedLanguagesKeys();
        $this->default_locale = 'ar';
        $this->result_ar = [];
        $this->result_en = [];
    }

    /**
     * @param $arr
     * @param false $trim
     * @return array
     */
    public function printf($arr, $trim = false) {
        $output = [];
        $result = [];

        foreach ($this->locales as $locale) {
            for ($i = 0; $i < $arr['maxLength']; $i++) {
                if (isset($arr['data'][$locale][0]['value'][$i]) && $arr['data'][$locale][0]['value'][$i] != '')
                    $result[$locale] = $this->printUntil($arr['data'][$locale], count($arr['data'][$locale]), $arr['maxLength'], 0, $i, $output, $locale, $trim);
            }
        }

        return $result;
    }

    /**
     * @param $template
     * @param $templateLength
     * @param $maxLength
     * @param $m
     * @param $n
     * @param $output
     * @param $locale
     * @param $trim
     * @return array|void
     */
    public function printUntil($template, $templateLength, $maxLength, $m, $n, $output, $locale, $trim) {
        $string = '';
        $output[$m] = [
            'order' => $template[$m]['order'],
            'value' => $template[$m]['value'][$n],
            'n'     => $n
        ];

        if ($m == $templateLength - 1) {
            for ($i = 0; $i < $templateLength; $i++) {
                if (empty($output[$i]['order'])) {
                    if ($templateLength - $i == 1 || $trim)
                        $string .= $output[$i]['value'];
                    else
                        $string .= $output[$i]['value'] . ' ';
                } else {
                    if($locale == $this->default_locale) {
                        if ($templateLength - $i == 1 || $trim)
                            $string .= '#' . $output[$i]['order'] . '.' . $output[$i]['n'] . '#';
                        else
                            $string .= '#' . $output[$i]['order'] . '.' . $output[$i]['n'] . '# ';
                    } else {
                        if ($templateLength - $i == 1 || $trim)
                            $string .= '#' . $output[$i]['order'] . '#';
                        else
                            $string .= '#' . $output[$i]['order'] . '# ';
                    }
                }
            }

            if($locale == 'ar') {
                $this->result_ar[] = $string;
            } elseif($locale == 'en') {
                $this->result_en[] = $string;
            }
            return;
        }

        for ($j = 0; $j < $maxLength; $j++) {
            if (isset($template[$m+1]['value'][$j]) && $template[$m+1]['value'][$j] != '')
                $this->printUntil($template, $templateLength, $maxLength, $m+1, $j, $output, $locale, $trim);
        }
        if($locale == 'ar') {
            return $this->result_ar;
        } elseif($locale == 'en') {
            return $this->result_en;
        }
    }
}
