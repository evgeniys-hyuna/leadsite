<?php

/**
 * String Helper
 * Stores helpful string functions
 *
 * @author eugen_siderka
 */
class String {

    /**
     * Simple string builder.
     * Builds up a string using setted format and arguments array.
     * Trims result string and clears it from multiple whitespaces.
     * Usage:
     * String::build(' Hello, {0}! My {space} name is {name}.', array('World', 'space' => '', 'name' => 'John Doe'));
     * Result: Hello, World! My name is John Doe.
     * May be useful instead of complicated string concatinations
     * @param string $string Pattern string
     * @param array $arguments Indexed array with string data
     * @return string Builded string
     */
    public static function build($string, $arguments = array()) {
        foreach ($arguments as $key => $value) {
            $string = str_replace('{' . $key . '}', $value, $string);
        }

        $string = preg_replace('/\s{2,}/', ' ', $string);
        $string = trim($string);

        return $string;
    }

    /**
     * Cuts the string to setted length (with endnig)
     * @param string $string Original string
     * @param int $length Cutted string length
     * @param string $ending Cutted string ending
     * @return string Cutted string
     */
    public static function truncate($string, $length, $ending = '...') {
        if (strlen($string) > $length &&
                $length - strlen($ending) > 0) {
            $string = substr($string, 0, $length - strlen($ending)) . $ending;
        }

        return $string;
    }

    /**
     * Brings URL to mind. Add or remove different URL parts
     * @param string $url URL
     * @param bool $protocol protocol part
     * @param bool $www www part
     * @param bool $domain domain part
     * @param bool $params rest of url
     * @return string Modified URL or FALSE
     */
    public static function rebuildUrl($url, $protocol = false, $www = false, $domain = true, $params = false) {
        if (!self::isUrl($url)) {
            return false;
        }

        $originalPart = array(
            'protocol' => false,
            'www' => false,
            'params' => false,
            'domain' => false,
        );

        // Explode URL to parts
        // Protocol
        if (preg_match('/.+:\/\//', $url, $originalPart['protocol']) == 1) {
            $url = substr($url, strlen($originalPart['protocol'][0]), strlen($url));
        }

        // Www
        if (preg_match('/www\./', $url, $originalPart['www']) == 1) {
            $url = substr($url, strlen($originalPart['www'][0]), strlen($url));
        }

        // Params
        if (preg_match('/\/.+/', $url, $originalPart['params']) == 1) {
            $url = substr($url, 0, strlen($url) - strlen($originalPart['params'][0]));
        }

        // At this moment, in URL only domain name lefts. Check it
        if (preg_match('/^(\w|\d|\.|-)+/', $url, $originalPart['domain']) != 1) {
            return false;
        }

        // Rebuild URL depending on arguments

        $url = $protocol ? $originalPart['protocol'][0] : '';
        $url .= $www ? $originalPart['www'][0] : '';
        $url .= $domain ? $originalPart['domain'][0] : '';

        // Make lowercase all but params
        $url = strtolower($url);

        $url .= $params ? $originalPart['params'][0] : '';

        return $url;
    }

    public static function rebuildEmail($email, $name = true, $at = true, $domain = true) {
        $email = self::findEmails($email);

        if (!$email ||
                count($email) > 1) {
            return false;
        }

        $email = $email[0];

        $originalPart = array(
            'name' => false,
            'domain' => false,
        );

        // Name
        if (preg_match('/^.+@/', $email, $originalPart['name']) == 1) {
            $originalPart['name'][0] = str_replace('@', '', $originalPart['name'][0]);
            $email = substr($email, strlen($originalPart['name'][0]), strlen($email));
        }

        // Domain
        if (preg_match('/@.+$/', $email, $originalPart['domain']) == 1) {
            $originalPart['domain'][0] = str_replace('@', '', $originalPart['domain'][0]);
            $email = substr($email, 0, strlen($email) - strlen($originalPart['domain'][0]));
        }

        $email = $name ? $originalPart['name'][0] : '';
        $email .= $at ? '@' : '';
        $email .= $domain ? $originalPart['domain'][0] : '';

        return $email;
    }

    /**
     * Cleans mail subject from labels like 're:' or 'fwd:'
     * @param string $subject Mail subject string
     * @return string Cleaned subject string
     */
    public static function extractEmailSubject($subject) {
        $labelsPattern = '/(.+)?(\W|^)(re|fwd): /i';
        $matches = array();
        preg_match($labelsPattern, $subject, $matches);
        return isset($matches[0]) ? substr($subject, strlen($matches[0])) : $subject;
    }

    /**
     * Creates directory and generates unique file name
     * @param string $directoryName Directory in uploads
     * @param string $fileName File name
     * @return string Path string or false
     */
    public static function reservePath($directoryName, $fileName) {
        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
        $maxAttemptsToGenerateName = 100;
        $fileNameLength = array(
            'min' => 10,
            'max' => 50,
        );
        $directory = self::build('{0}{1}..{1}uploads{1}{2}{1}{3}', array(
                    Yii::app()->basePath,
                    DIRECTORY_SEPARATOR,
                    $directoryName,
                    sDate::gm("d.m.Y"),
        ));

        if (!file_exists($directory)) {
            mkdir($directory, 0755);
        }

        for ($i = 0, $j = $fileNameLength['min']; $j < $fileNameLength['max']; $i++) {
            $reservedPath = self::build('{0}.{1}', array(
                        String::generate($j),
                        $fileExtension,
            ));

            if (!file_exists(self::build('{0}{1}{2}', array(
                                $directory,
                                DIRECTORY_SEPARATOR,
                                $reservedPath,
                    )))) {
                $isSuccess = true;
                break;
            }

            if ($i >= $maxAttemptsToGenerateName) {
                $j++;
                $i = 0;
            }
        }

        if (!$isSuccess) {
            return false;
        }

        return self::build('{0}{1}{2}', array(
                    $directory,
                    DIRECTORY_SEPARATOR,
                    $reservedPath,
        ));
    }

    /**
     * String generator
     * @param int $length
     * @param boolean $symbols
     * @param boolean $numbers
     * @param boolean $uppercase
     * @param boolean $lowercase
     * @return string
     */
    public static function generate($length, $symbols = false, $numbers = true, $uppercase = true, $lowercase = true) {
        if (!$symbols && !$numbers && !$uppercase && !$lowercase) {
            return false;
        }

        $asciiIndex = array();
        $generatedString = '';

        if ($symbols) {
            $asciiSymbols = array(33, 40, 41, 43, 45, 64, 91, 93, 95, 123, 124, 125); // !()+-@[]_{|}
            $asciiIndex = array_merge($asciiIndex, $asciiSymbols);
        }

        if ($numbers) {
            for ($i = 48; $i <= 57; $i++) {
                $asciiIndex[] = $i;
            }
        }

        if ($uppercase) {
            for ($i = 65; $i <= 90; $i++) {
                $asciiIndex[] = $i;
            }
        }

        if ($lowercase) {
            for ($i = 97; $i <= 122; $i++) {
                $asciiIndex[] = $i;
            }
        }

        for ($i = 0; $i < $length; $i++) {
            $random = rand(0, count($asciiIndex) - 1);
            $generatedString .= chr($asciiIndex[$random]);
        }

        return $generatedString;
    }

    /**
     * Check string for correct URL pattern
     * @param string $url URL
     * @return bool
     */
    public static function isUrl($url) {
        $pattern = '/^(https?:\/\/)?([\dA-Za-z\.-]+)\.([A-Za-z\.]{2,6})([\(\)\%\?\=\&\/\w \.\,-]*)*\/?$/';
        return preg_match($pattern, strtolower(trim($url))) == 1 ? true : false;
    }

    public static function isEmail($email) {
        $email_pattern = '/^[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+(?:\.[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+)*@(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?$/';
        return preg_match($email_pattern, $email) == 1 ? true : false;
    }

    public static function findEmails($string) {
        $emailPattern = '/[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+(?:\.[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+)*@(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?/';
        $matches = array();

        preg_match_all($emailPattern, $string, $matches);
        $foundedEmails = array_unique($matches[0]);

        return count($foundedEmails) > 0 ? $foundedEmails : false;
    }

    /**
     * Prevent JS executing in string
     * Removes:
     * script tags
     * events
     * anchor links
     * data- attributes
     * @param string $string
     * @return string
     */
    public static function noScript($string) {
        $openTagPattern = '/<script[^>]*>/';
        $closeTagPattern = '/<\/script[^>]*>/';
        $eventTagPattern = '/<[^<]+ on\w+\s?=\s?[\'|"]*[\'|"][^<]*>/';
        $eventPattern = '/on(\w+)(\s?)=(\s?)["\']?((?:.(?!["\']?\s+(?:\S+)=|[>"\']))+.)["\']?/'; // on\w+\s?=\s?[\'|"].*[\'|"]
        $anchorLinkPattern = '/href(\s?)=(\s?)["\']?#((?:.(?!["\']?\s+(?:\S+)=|[>"\']))+.)["\']?/';
        $dataAttributePattern = '/data-\w+(\s?)=(\s?)["\']?((?:.(?!["\']?\s+(?:\S+)=|[>"\']))+.)["\']?/';
        $matches = array();

        preg_match_all($openTagPattern, $string, $matches['open']);
        preg_match_all($closeTagPattern, $string, $matches['close']);
        preg_match_all($eventTagPattern, $string, $matches['event']);
        preg_match_all($anchorLinkPattern, $string, $matches['anchor']);
        preg_match_all($dataAttributePattern, $string, $matches['data']);

        foreach ($matches as $match) {
            foreach ($match[0] as $m) {
                $eventMatch = array();

                if (preg_match($eventPattern, $m, $eventMatch)) {
                    $noEventTag = str_replace($eventMatch[0], '', $m);
                    $string = str_replace($m, $noEventTag, $string);
                }

                $string = str_replace($m, '', $string);
            }
        }

        return $string;
    }

    /**
     * Convert string to requested character encoding
     * @param string $string
     * @return string
     */
    public static function convert($string) {
        $arrStr = explode('?', $string);
        if (isset($arrStr[1]) && in_array($arrStr[1], mb_list_encodings())) {
            $names = explode(' ', $string);
            $string = '';
            foreach ($names as $name) {
                $arrStr = explode('?', $name);
                //second part of array should be an encoding name (KOI8-R) in my case
                if (isset($arrStr[1]) && in_array($arrStr[1], mb_list_encodings())) {
                    switch ($arrStr[2]) {
                        case 'B': //base64 encoded
                            $subName = base64_decode($arrStr[3]);
                            break;
                        case 'Q': //quoted printable encoded
                            $subName = quoted_printable_decode($arrStr[3]);
                            break;
                    }
                    //convert it to UTF-8
                    $string .= iconv($arrStr[1], 'UTF-8', $subName);
                }
            }
        }
        return $string;
    }

    /**
     * Cleans up passed string from undesirable html tags
     * @param string $string
     * @return string
     */
    public static function cleanTags($string) {
        $allowedTags = array(
            // Block-level
            '<blockquote><dd><div><dl><h1><h2><h3><h4><h5><h6><hr><nav><noscript><ol><p><pre><table><tfoot><ul>',
            // Inline
            '<b><big><i><small><tt><abbr><acronym><cite><code><dfn><em><kbd><strong><samp><var>',
            '<a><bdo><br><img><map><object><q><script><span><sub><sup><button><input><label><select><textarea>',
            // Additional
            '<td><tr><th><li>',
        );

        return strip_tags($string, implode('', $allowedTags));
    }

    /**
     * Removes setted html tag with contents from string
     * Usage: $htmlString = String::removeTag('div', '#quote', $htmlString);
     * @param string $tag Tag name to search
     * @param string $selector CSS-like selector
     * @param string $string HTML source
     * @return string|false
     */
    public static function removeTag($tag, $selector, $string) {
        $dom = new DOMDocument();
        $dom->loadHTML(mb_convert_encoding($string, 'HTML-ENTITIES', 'utf-8'));
        $elements = $dom->getElementsByTagName($tag);
        $attribute = false;

        switch ($selector[0]) {
            case '.':
                $attribute = 'class';
                break;
            case '#':
                $attribute = 'id';
                break;
        }

        if (!$attribute) {
            return false;
        }

        foreach ($elements as $e) {
            if ($e->getAttribute($attribute) == substr($selector, 1)) {
                $e->parentNode->removeChild($e);
            }
        }

        return self::cleanTags($dom->saveHTML());
    }
    
    public static function getTagsBySelector($tag, $selector, $string) {
        $dom = new DOMDocument();
        
        libxml_use_internal_errors(true);
        $dom->loadHTML(mb_convert_encoding($string, 'HTML-ENTITIES', 'utf-8'));
        
        $elements = $dom->getElementsByTagName($tag);
        $attribute = false;
        $result = array();

        switch ($selector[0]) {
            case '.':
                $attribute = 'class';
                break;
            case '#':
                $attribute = 'id';
                break;
        }

        if (!$attribute) {
            return false;
        }

        foreach ($elements as $e) {
            if ($e->getAttribute($attribute) == substr($selector, 1)) {
                $result[] = $dom->saveHTML($e);
            }
        }

        return $result;
    }
    
    public static function getTagContent($string, $tag) {
        $pattern = String::build('/<{tag}.*>.*<\/{tag}.*>/', array(
            'tag' => $tag,
        ));
        $match = array();
        preg_match($pattern, $string, $match);
        
        if (!empty($match[0])) {
            return strip_tags($match[0]);
        }
        
        return false;
    }
    
    public static function getTagAttribute($string, $tag, $attribute) {
        $pattern = String::build('/<{tag}.*{attribute}=["\']?((?:.(?!["\']?\s+(?:\S+)=|[>"\']))+.)["\']?/', array(
            'tag' => $tag,
            'attribute' => $attribute,
        ));
        $match = array();
        preg_match($pattern, $string, $match);
        
        if (!empty($match[1])) {
            return strip_tags($match[1]);
        }
        
        return false;
    }
    
    /**
     * Search and close opened HTML tags
     * @param string $string HTML text
     * @return string
     */
    public static function closeTags($string) {
        $result = array();

        preg_match_all('#<(?!meta|img|br|hr|input\b)\b([a-z]+)(?: .*)?(?<![/|/ ])>#iU', $string, $result);
        $openedTags = $result[1];

        preg_match_all('#</([a-z]+)>#iU', $string, $result);
        $closedTags = $result[1];

        $openedTagsCount = count($openedTags);

        if (count($closedTags) == $openedTagsCount) {
            return $string;
        }

        $openedTags = array_reverse($openedTags);

        for ($i = 0; $i < $openedTagsCount; $i++) {
            if (!in_array($openedTags[$i], $closedTags)) {
                $string .= '</' . $openedTags[$i] . '>';
            } else {
                unset($closedTags[array_search($openedTags[$i], $closedTags)]);
            }
        }

        return $string;
    }

    /**
     * Find text with web address and wrap it to tag 'a'
     * @param string $string
     * @return string
     */
    public static function wrapLinks($string) {
        $pattern = '/(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\+\:\?\=\&\/\w\.-]*)*\/?/';
        $result = array();

        preg_match_all($pattern, $string, $result);

        foreach ($result[0] as $l) {
            $string = str_replace($l, String::build('<a href="{link}">{link}</a>', array(
                        'link' => $l,
                    )), $string);
        }

        return $string;
    }

    /**
     * Apply required methods to handle HTML string
     * @param string $string HTML text
     * @return string
     */
    public static function autoHtml($string) {
        $string = self::closeTags($string);
        $string = self::cleanTags($string);
        $string = self::noScript($string);
//		$string = self::wrapLinks($string);

        return $string;
    }

}
