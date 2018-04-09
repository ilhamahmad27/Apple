<?php
error_reporting(0);
ini_set('max_execution_time', 0);
ini_set('memory_limit', -1);
date_default_timezone_set('Asia/Tokyo');
if (!file_exists('Cookies')) {
    mkdir('Cookies');
}
function isEmail($email)
{
    if (!preg_match('/^[_a-z0-9-]+(.[_a-z0-9-]+)*@[a-z0-9-]+(.[a-z0-9-]+)*(.[a-z]{2,3})$/i', $email)) {
        return false;
    }
    return true;
}
function ms_sleep($seconds)
{
    $seconds = abs($seconds);
    if ($seconds < 1):
        usleep($seconds * 1000000);
    else:
        sleep($seconds);
    endif;
}
function process($total, $count)
{
    if ($total > 0) {
        return round($count / ($total / 100), 2);
    } else {
        return 0;
    }
}
class Curl
{
    public $ch, $agent, $error, $info, $cookiefile;
    public function __construct()
    {
        $this->agent = $this->get_agent(rand(0, 83));
        $this->ch    = curl_init();
        curl_setopt($this->ch, CURLOPT_USERAGENT, $this->agent);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, true);
    }
    public function getStr($str, $find_start, $find_end)
    {
        $start = strpos($str, $find_start);
        if ($start === false) {
            return '';
        }
        $length = strlen($find_start);
        $end    = strpos(substr($str, $start + $length), $find_end);
        return trim(substr($str, $start + $length, $end));
    }
    public function fetchCurlCookies($source)
    {
        preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $source, $matches);
        $cookies = array();
        foreach ($matches[1] as $item) {
            parse_str($item, $cookie);
            $cookies = array_merge($cookies, $cookie);
        }
        return $cookies;
    }
    public function timeout($time)
    {
        curl_setopt($this->ch, CURLOPT_TIMEOUT, $time);
        curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, $time);
    }
    public function ssl($veryfyPeer, $verifyHost)
    {
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, $veryfyPeer);
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, $verifyHost);
    }
    public function header($header)
    {
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $header);
    }
    public function cookies($cookie_file_path)
    {
        $this->cookiefile = $cookie_file_path;
        $fp               = fopen($this->cookiefile, 'wb');
        fclose($fp);
        curl_setopt($this->ch, CURLOPT_COOKIEJAR, $this->cookiefile);
        curl_setopt($this->ch, CURLOPT_COOKIEFILE, $this->cookiefile);
    }
    public function ref($ref)
    {
        curl_setopt($this->ch, CURLOPT_REFERER, $ref);
    }
    public function socks($sock)
    {
        curl_setopt($this->ch, CURLOPT_PROXY, $sock);
        curl_setopt($this->ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
    }
    public function socks4($sock)
    {
        curl_setopt($this->ch, CURLOPT_HTTPPROXYTUNNEL, true);
        curl_setopt($this->ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS4);
        curl_setopt($this->ch, CURLOPT_PROXY, $sock);
    }
    public function post($url, $data)
    {
        curl_setopt($this->ch, CURLOPT_POST, 1);
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $data);
        return $this->getPage($url);
    }
    public function data($url, $data, $hasHeader = true, $hasBody = true)
    {
        curl_setopt($this->ch, CURLOPT_POST, 1);
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, http_build_query($data));
        return $this->getPage($url, $hasHeader, $hasBody);
    }
    public function get($url, $hasHeader = true, $hasBody = true)
    {
        curl_setopt($this->ch, CURLOPT_POST, 0);
        return $this->getPage($url, $hasHeader, $hasBody);
    }
    public function getPage($url, $hasHeader = true, $hasBody = true)
    {
        curl_setopt($this->ch, CURLOPT_NOBODY, $hasBody ? 0 : 1);
        curl_setopt($this->ch, CURLOPT_URL, $url);
        $data        = curl_exec($this->ch);
        $this->error = curl_error($this->ch);
        $this->info  = curl_getinfo($this->ch);
        return $data;
    }
    public function close()
    {
        unlink($this->cookiefile);
        curl_close($this->ch);
    }
    public function get_agent($z)
    {
        switch ($z) {
            case 0:
                $agent = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1) Gecko/20061010 Firefox/2.0';
                break;
            case 1:
                $agent = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.1.1) Gecko/20090715 Firefox/3.5.1';
                break;
            case 2:
                $agent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)';
                break;
            case 3:
                $agent = 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0)';
                break;
            case 4:
                $agent = 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1)';
                break;
            case 5:
                $agent = 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)';
                break;
            case 6:
                $agent = 'Mozilla/5.0 (X11; U; Linux x86_64; en-US; rv:1.9a8) Gecko/2007100619 GranParadiso/3.0a8';
                break;
            case 7:
                $agent = 'Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US; rv:1.9.1b3) Gecko/20090305 Firefox/3.1b3';
                break;
            case 8:
                $agent = 'Mozilla/5.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4325)';
                break;
            case 9:
                $agent = 'Mozilla/4.0 (Windows; MSIE 6.0; Windows NT 6.0)';
                break;
            case 10:
                $agent = 'Mozilla/4.0 (compatible; MSIE 5.5b1; Mac_PowerPC)';
                break;
            case 11:
                $agent = 'Mozilla/5.0 (Windows; U; WinNT; en; rv:1.0.2) Gecko/20030311 Beonex/0.8.2-stable';
                break;
            case 12:
                $agent = 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X; en; rv:1.8.1.4pre) Gecko/20070521 Camino/1.6a1pre';
                break;
            case 13:
                $agent = 'Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US; rv:1.9b5) Gecko/2008032620 Firefox/3.0b5 ';
                break;
            case 14:
                $agent = 'Mozilla/5.0 (X11; U; Linux i686; de-AT; rv:1.8.0.2) Gecko/20060309 SeaMonkey/1.0';
                break;
            case 15:
                $agent = 'Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.9.1b2pre) Gecko/20081015 Fennec/1.0a1';
                break;
            case 16:
                $agent = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.6b) Gecko/20031212 Firebird/0.7+';
                break;
            case 17:
                $agent = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; ru; rv:1.9b3) Gecko/2008020514 Firefox/3.0b3';
                break;
            case 18:
                $agent = 'Mozilla/5.0 (Windows; U; Windows NT 6.0; it; rv:1.8.1.9) Gecko/20071025 Firefox/2.0.0.9';
                break;
            case 19:
                $agent = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; fr; rv:1.8.1.5) Gecko/20070713 Firefox/2.0.0.5';
                break;
            case 20:
                $agent = 'Mozilla/4.76 [en] (X11; U; Linux 2.4.9-34 i686)';
                break;
            case 21:
                $agent = 'Mozilla/4.75 [fr] (WinNT; U)';
                break;
            case 22:
                $agent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1) Opera 7.52 [en]';
                break;
            case 23:
                $agent = 'Mozilla/4.0 (compatible; MSIE 6.0; ; Linux i686) Opera 7.50 [en]';
                break;
            case 24:
                $agent = 'Mozilla/5.0 (Macintosh; U; PPC Mac OS X Mach-O; en-US; rv:1.0.1) Gecko/20021216 Chimera/0.6';
                break;
            case 25:
                $agent = 'Mozilla/4.0 (compatible; MSIE 7.0; AOL 9.0; Windows NT 5.1; .NET CLR 2.0.50727; .NET CLR 3.0.4506.2152; .NET CLR 3.5.30729)';
                break;
            case 26:
                $agent = 'Mozilla/4.0 (compatible; MSIE 7.0; AOL 9.0; Windows NT 5.1; .NET CLR 1.1.4322; InfoPath.1; MS-RTC LM 8)';
                break;
            case 27:
                $agent = 'Mozilla/4.0 (compatible; MSIE 7.0; AOL 9.0; Windows NT 5.1; .NET CLR 1.1.4322; .NET CLR 2.0.50727; InfoPath.1; .NET CLR 3.0.04506.30)';
                break;
            case 28:
                $agent = 'Mozilla/4.0 (compatible; MSIE 7.0; AOL 9.0; Windows NT 5.1; .NET CLR 1.1.4322; .NET CLR 2.0.50727; .NET CLR 3.0.04506.30; InfoPath.1)';
                break;
            case 29:
                $agent = 'Mozilla/4.0 (compatible; MSIE 7.0; AOL 9.0; Windows NT 5.1; .NET CLR 1.1.4322; .NET CLR 2.0.50727; .NET CLR 3.0.04506.30)';
                break;
            case 30:
                $agent = 'Mozilla/4.0 (compatible; MSIE 6.0; America Online Browser 1.1; rev1.5; Windows NT 5.1; SV1; .NET CLR 1.1.4322; .NET CLR 2.0.50727)';
                break;
            case 31:
                $agent = 'Mozilla/5.0 (X11; U; Linux; it-IT) AppleWebKit/527+ (KHTML, like Gecko, Safari/419.3) Arora/0.4 (Change: 413 12f13f8)';
                break;
            case 32:
                $agent = 'Mozilla/5.0 (X11; U; Linux; en-GB) AppleWebKit/527+ (KHTML, like Gecko, Safari/419.3) Arora/0.3 (Change: 239 52c6958)';
                break;
            case 33:
                $agent = 'Mozilla/5.0 (X11; U; Linux; en-US) AppleWebKit/523.15 (KHTML, like Gecko, Safari/419.3) Arora/0.2 (Change: 189 35c14e0)';
                break;
            case 34:
                $agent = 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; Trident/4.0; Avant Browser; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0)';
                break;
            case 35:
                $agent = 'Mozilla/5.0 (X11; U; Linux x86_64; en-GB; rv:1.8.1b1) Gecko/20060601 BonEcho/2.0b1 (Ubuntu-edgy)';
                break;
            case 36:
                $agent = 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X; en) AppleWebKit/419 (KHTML, like Gecko, Safari/419.3) Cheshire/1.0.ALPHA';
                break;
            case 37:
                $agent = 'Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US) AppleWebKit/530.1 (KHTML, like Gecko) Chrome/2.0.164.0 Safari/530.1';
                break;
            case 38:
                $agent = 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; InfoPath.2; .NET CLR 2.0.50727; .NET CLR 1.1.4322; Crazy Browser 3.0.0 Beta2)';
                break;
            case 39:
                $agent = 'Mozilla/5.0 (X11; U; Linux i686; en; rv:1.8.1.12) Gecko/20080208 (Debian-1.8.1.12-2) Epiphany/2.20';
                break;
            case 40:
                $agent = 'Mozilla/5.0 (X11; U; Linux i686; it-IT; rv:1.9.0.2) Gecko/2008092313 Ubuntu/9.04 (jaunty) Firefox/3.5';
                break;
            case 41:
                $agent = 'Mozilla/5.0 (Macintosh; U; PPC Mac OS X 10.5; en-US; rv:1.9.1b3pre) Gecko/20081212 Mozilla/5.0 (Windows; U; Windows NT 5.1; en) AppleWebKit/526.9 (KHTML, like Gecko) Version/4.0dp1 Safari/526.8';
                break;
            case 42:
                $agent = 'Mozilla/5.0 (X11; U; Linux i686; en-GB; rv:1.7.6) Gecko/20050405 Epiphany/1.6.1 (Ubuntu) (Ubuntu package 1.0.2)';
                break;
            case 43:
                $agent = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.0.5) Gecko/20060731 Firefox/1.5.0.5 Flock/0.7.4.1';
                break;
            case 44:
                $agent = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/525.19 (KHTML, like Gecko) Chrome/0.2.153.1 Safari/525.19 ';
                break;
            case 45:
                $agent = 'Opera/9.80 (Windows NT 6.1; U; zh-tw) Presto/2.5.22 Version/10.50';
                break;
            case 46:
                $agent = 'Opera/9.80 (Windows NT 6.1; U; zh-cn) Presto/2.5.22 Version/10.50';
                break;
            case 47:
                $agent = 'Opera/9.80 (Windows NT 6.1; U; sk) Presto/2.6.22 Version/10.50';
                break;
            case 48:
                $agent = 'Opera/9.80 (Windows NT 6.1; U; ja) Presto/2.5.22 Version/10.50';
                break;
            case 49:
                $agent = 'Opera/9.80 (Windows NT 6.0; U; zh-cn) Presto/2.5.22 Version/10.50';
                break;
            case 50:
                $agent = 'Opera/9.80 (Windows NT 5.1; U; ru) Presto/2.5.22 Version/10.50';
                break;
            case 51:
                $agent = 'Opera/10.50 (Windows NT 6.1; U; en-GB) Presto/2.2.2';
                break;
            case 52:
                $agent = 'Opera/9.80 (S60; SymbOS; Opera Tablet/9174; U; en) Presto/2.7.81 Version/10.5';
                break;
            case 53:
                $agent = 'Opera/9.80 (X11; U; Linux i686; en-US; rv:1.9.2.3) Presto/2.2.15 Version/10.10';
                break;
            case 54:
                $agent = 'Opera/9.80 (X11; Linux x86_64; U; it) Presto/2.2.15 Version/10.10';
                break;
            case 55:
                $agent = 'Opera/9.80 (Windows NT 6.1; U; de) Presto/2.2.15 Version/10.10';
                break;
            case 56:
                $agent = 'Opera/9.80 (Windows NT 6.0; U; Gecko/20100115; pl) Presto/2.2.15 Version/10.10';
                break;
            case 57:
                $agent = 'Opera/9.80 (Windows NT 6.0; U; en) Presto/2.2.15 Version/10.10';
                break;
            case 58:
                $agent = 'Opera/9.80 (Windows NT 5.1; U; de) Presto/2.2.15 Version/10.10';
                break;
            case 69:
                $agent = 'Opera/9.80 (Windows NT 5.1; U; cs) Presto/2.2.15 Version/10.10';
                break;
            case 60:
                $agent = 'Mozilla/5.0 (Windows NT 6.0; U; tr; rv:1.8.1) Gecko/20061208 Firefox/2.0.0 Opera 10.10';
                break;
            case 61:
                $agent = 'Mozilla/4.0 (compatible; MSIE 6.0; X11; Linux i686; de) Opera 10.10';
                break;
            case 62:
                $agent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 6.0; tr) Opera 10.10';
                break;
            case 63:
                $agent = 'Opera/9.80 (X11; Linux x86_64; U; en-GB) Presto/2.2.15 Version/10.01';
                break;
            case 64:
                $agent = 'Opera/9.80 (X11; Linux x86_64; U; en) Presto/2.2.15 Version/10.00';
                break;
            case 65:
                $agent = 'Opera/9.80 (X11; Linux x86_64; U; de) Presto/2.2.15 Version/10.00';
                break;
            case 66:
                $agent = 'Opera/9.80 (X11; Linux i686; U; ru) Presto/2.2.15 Version/10.00';
                break;
            case 67:
                $agent = 'Opera/9.80 (X11; Linux i686; U; pt-BR) Presto/2.2.15 Version/10.00';
                break;
            case 68:
                $agent = 'Opera/9.80 (X11; Linux i686; U; pl) Presto/2.2.15 Version/10.00';
                break;
            case 69:
                $agent = 'Opera/9.80 (X11; Linux i686; U; nb) Presto/2.2.15 Version/10.00';
                break;
            case 70:
                $agent = 'Opera/9.80 (X11; Linux i686; U; en-GB) Presto/2.2.15 Version/10.00';
                break;
            case 71:
                $agent = 'Opera/9.80 (X11; Linux i686; U; en) Presto/2.2.15 Version/10.00';
                break;
            case 72:
                $agent = 'Opera/9.80 (X11; Linux i686; U; Debian; pl) Presto/2.2.15 Version/10.00';
                break;
            case 73:
                $agent = 'Opera/9.80 (X11; Linux i686; U; de) Presto/2.2.15 Version/10.00';
                break;
            case 74:
                $agent = 'Opera/9.80 (Windows NT 6.1; U; zh-cn) Presto/2.2.15 Version/10.00';
                break;
            case 75:
                $agent = 'Opera/9.80 (Windows NT 6.1; U; fi) Presto/2.2.15 Version/10.00';
                break;
            case 76:
                $agent = 'Opera/9.80 (Windows NT 6.1; U; en) Presto/2.2.15 Version/10.00';
                break;
            case 77:
                $agent = 'Opera/9.80 (Windows NT 6.1; U; de) Presto/2.2.15 Version/10.00';
                break;
            case 78:
                $agent = 'Opera/9.80 (Windows NT 6.1; U; cs) Presto/2.2.15 Version/10.00';
                break;
            case 79:
                $agent = 'Opera/9.80 (Windows NT 6.0; U; en) Presto/2.2.15 Version/10.00';
                break;
            case 80:
                $agent = 'Opera/9.80 (Windows NT 6.0; U; de) Presto/2.2.15 Version/10.00';
                break;
            case 81:
                $agent = 'Opera/9.80 (Windows NT 5.2; U; en) Presto/2.2.15 Version/10.00';
                break;
            case 82:
                $agent = 'Opera/9.80 (Windows NT 5.1; U; zh-cn) Presto/2.2.15 Version/10.00';
                break;
            case 83:
                $agent = 'Opera/9.80 (Windows NT 5.1; U; ru) Presto/2.2.15 Version/10.00';
                break;
        }
        return $agent;
    }
}
class Colors
{
    private $foreground_colors = array();
    private $background_colors = array();
    public function __construct()
    {
        $this->foreground_colors['black']        = '0;30';
        $this->foreground_colors['dark_gray']    = '1;30';
        $this->foreground_colors['blue']         = '0;34';
        $this->foreground_colors['light_blue']   = '1;34';
        $this->foreground_colors['green']        = '0;32';
        $this->foreground_colors['light_green']  = '1;32';
        $this->foreground_colors['cyan']         = '0;36';
        $this->foreground_colors['light_cyan']   = '1;36';
        $this->foreground_colors['red']          = '0;31';
        $this->foreground_colors['light_red']    = '1;31';
        $this->foreground_colors['purple']       = '0;35';
        $this->foreground_colors['light_purple'] = '1;35';
        $this->foreground_colors['brown']        = '0;33';
        $this->foreground_colors['yellow']       = '1;33';
        $this->foreground_colors['light_gray']   = '0;37';
        $this->foreground_colors['white']        = '1;37';
        $this->background_colors['black']        = '40';
        $this->background_colors['red']          = '41';
        $this->background_colors['green']        = '42';
        $this->background_colors['yellow']       = '43';
        $this->background_colors['blue']         = '44';
        $this->background_colors['magenta']      = '45';
        $this->background_colors['cyan']         = '46';
        $this->background_colors['light_gray']   = '47';
    }
    public function getColoredString($string, $foreground_color = null, $background_color = null)
    {
        $colored_string = '';
        if (isset($this->foreground_colors[$foreground_color])) {
            $colored_string .= "\033[" . $this->foreground_colors[$foreground_color] . 'm';
        }
        if (isset($this->background_colors[$background_color])) {
            $colored_string .= "\033[" . $this->background_colors[$background_color] . 'm';
        }
        $colored_string .= $string . "\033[0m";
        return $colored_string;
    }
    public function getForegroundColors()
    {
        return array_keys($this->foreground_colors);
    }
    public function getBackgroundColors()
    {
        return array_keys($this->background_colors);
    }
}
function validator($email)
{
    $curl  = new Curl();
    $color = new Colors();
    $curl->cookies('Cookies/#BLANKCODE-COOKIES-' . md5(rand(0,5)) . '.txt');
    $curl->ssl(0, 2);
    $curl->timeout(30);
    $page = $curl->get('https://appleid.apple.com/account#!&page=create');
    $curl->header(array(
        'Host: appleid.apple.com',
        'Referer: https://appleid.apple.com/account',
        'Accept: application/json, text/javascript, */*; q=0.01',
        'Scnt: ' . $curl->getStr($page, "scnt: '", "'"),
        'User-Agent: ' . $curl->get_agent(rand(0, 83)),
        'X-Requested-With: XMLHttpRequest',
        'X-Apple-Api-Key: ' . $curl->getStr($page, "apiKey: '", "'"),
        'Set-Cookie: adsp=' . $curl->getStr($page, "sessionId: '", "'"),
        'X-Apple-Id-Session-Id: ' . $curl->getStr($page, "sessionId: '", "'"),
        'Content-Type: application/json',
        'Accept-Language: en-US,en;q=0.8',
        'X-Apple-App-Id: 3810',
        'X-Apple-Request-Context: create',
        'Origin: https://appleid.apple.com',
        'Cookie: idclient=web; dslang=US-EN; site=USA; aidsp=' . $curl->getStr($page, "sessionId: '", "'") . '; geo=US',
        'Connection: keep-alive'
    ));
    $decode = json_decode($curl->post('https://appleid.apple.com/account/validation/appleid', json_encode(array('emailAddress' => $email))));
    $curl->close();
    if ($decode->used == true || $decode->appleOwnedDomain == true) {
        $log = fopen('apple-live.txt', 'a');
        fwrite($log, $email . "\r\n");
        fclose($log);
        return $color->getColoredString('live', 'light_green');
    } elseif ($decode->used == false || $decode->appleOwnedDomain == false) {
        $log = fopen('apple-die.txt', 'a');
        fwrite($log, $email . "\r\n");
        fclose($log);
        return $color->getColoredString('died', 'light_red');
    } else {
        $log = fopen('apple-invalid.txt', 'a');
        fwrite($log, $email . "\r\n");
        fclose($log);
        return $color->getColoredString('invalid', 'light_blue');
    }
}
if (isset($argv[1]) && !empty($argv[1])) {
    if(strtoupper(substr(PHP_OS, 0,3)) === "WIN") {
        system('cls');
    } else {
        system('clear');
    }
    $file  = array_unique(file(dirname(__FILE__) . DIRECTORY_SEPARATOR . $argv[1], FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));
    $total = count($file);
    echo "\r\n";
    echo "\033[1;31m             ###\033[0m\r\n";
    echo "\033[1;31m           ####\033[0m\r\n";
    echo "\033[1;31m           ###\033[0m\r\n";
    echo "\033[1;31m   #######    #######\033[0m\r\n";
    echo "\033[1;31m ###################### \033[0m\r\n";
    echo "\033[1;31m#####################   \033[1;31m(author) Ilham Ahmad | Setan Code\033[0m\r\n";
    echo "\033[1;31m####################    \033[0m\r\n";
    echo "\033[1;37m#####################   \033[1;37m(message) trimo mundur timbang loro ati :)\033[0m\r\n";
    echo "\033[1;37m ######################\033[0m\r\n";
    echo "\033[1;37m  ####################\033[0m\r\n";
    echo "\033[1;37m    ################\033[0m\r\n";
    echo "\033[1;37m     #### [V4] ####\033[0m\r\n";
    echo "\n";
    echo "[+] Email List : \033[1;32m" . $argv[1] . "\033[0m | Total List : \033[1;31m" . $total . "\033[0m [+]\r\n";
    echo "\n";
    sleep(5);
    for ($i = 0; $i < $total; ++$i) {
        foreach ($file as $key => $email) {
            $count = ++$i;
            $time  = date('G:i, d M Y');
            if (isEmail($list)) {
                echo "Checked at " . $time . " | " . $count . "/" . $total . " | " . validator($email) . " | " . $email . " | Coded by BlankCode.org\n";
            }
            if ($count == $total) {
                echo "\r\n";
                echo "Report      : Successfully Checking Email List " . $count . "/" . $total . " at " . $time . "\r\n";
                echo "Server Name : " . php_uname() . "\r\n";
                echo "\r\n";
            }
        }
    }
}
?> 
