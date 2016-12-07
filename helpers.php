<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

if (!function_exists('highlight_words')) {
    /**
     * 高亮关键词
     * @param string|array $words
     * @param string $string
     * @return mixed
     */
    function highlight_words($words, $string)
    {
        if (!is_string($string) || (!is_array($words) && !is_string($words))) {
            return $string;
        }
        if (is_array($words)) {
            $pattern = '/(' . implode('|', $words) . ')/i';
        } else {
            $pattern = '/(' . $words . ')/i';
        }
        return preg_replace($pattern, "<font style=\"color:red;font-weight:700;\">\\1</font>", $string);
    }
}

if (!function_exists('key_extra')) {
    /**
     * 从文本中提取关键词
     * @param string $string
     * @param int $limit 获取词的数量
     * @return array
     */
    function key_extra($string, $limit = 10)
    {
        if (function_exists('scws_new')) {
            $matches = null;
            if (preg_match_all("/[a-zA-Z0-9\x{4e00}-\x{9fa5}]+/u", $string, $matches)) {
                if (isset($matches[0])) {
                    $string = '';
                    foreach ($matches[0] as $match) {
                        $string .= $match;
                    }
                    $so = scws_new();
                    $so->set_charset('utf8');
                    $so->send_text($string);
                    $tmp = $so->get_tops($limit);
                    $so->close();
                    $words = [];
                    foreach ($tmp as $key => $value) {
                        $words[] = $value['word'];
                    }
                    return $words;
                }
            }
        }
        return [];
    }
}

if (!function_exists('pullword')) {
    /**
     * 分词
     * @param string $string
     * @return array 分词结果
     */
    function pullword($string)
    {
        if (function_exists('scws_new')) {
            $matches = null;
            if (preg_match_all("/[a-zA-Z0-9\x{4e00}-\x{9fa5}]+/u", $string, $matches)) {
                if (isset($matches[0])) {
                    $string = '';
                    foreach ($matches[0] as $match) {
                        $string .= $match;
                    }
                    $so = scws_new();
                    $so->set_charset('utf8');
                    $so->send_text($string);
                    $words = [];
                    while ($tmp = $so->get_result()) {
                        $words = array_merge($words,$tmp);
                    }
                    $so->close();
                    return $words;
                }
            }
        }
        return [];
    }
}