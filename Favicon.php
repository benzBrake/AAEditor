<?php

namespace Jerrybendy\Favicon;
class Favicon
{
    public $debug_mode = false;
    private $params = array();
    private $full_host = '';
    private $data = NULL;
    private $_last_time_spend = 0;
    private $_last_memory_usage = 0;
    private $_file_map = [];
    private $_default_icon = '';

    public function getFavicon($url = '', $return = FALSE)
    {
        if (!$url) {
            throw new \InvalidArgumentException(__CLASS__ . ': Url cannot be empty', E_ERROR);
        }
        $this->params['origin_url'] = $url;
        $ret = $this->formatUrl($url);
        if (!$ret) {
            throw new \InvalidArgumentException(__CLASS__ . ': Invalided url', E_WARNING);
        }
        $time_start = microtime(TRUE);
        $this->_log_message('Begin to get icon, ' . $url);
        $data = $this->getData();
        $time_end = microtime(TRUE);
        $this->_last_time_spend = $time_end - $time_start;
        $this->_last_memory_usage = ((!function_exists('memory_get_usage')) ? '0' : round(memory_get_usage() / 1024 / 1024, 2)) . 'MB';
        $this->_log_message('Get icon complate, spent time ' . $this->_last_time_spend . 's, Memory_usage ' . $this->_last_memory_usage);
        if ($data === FALSE && $this->_default_icon) {
            $data = @file_get_contents($this->_default_icon);
        }
        if ($return) {
            return $data;
        } else {
            if ($data !== FALSE) {
                foreach ($this->getHeader() as $header) {
                    @header($header);
                }
                echo $data;
            } else {
                header('Content-type: application/json');
                echo json_encode(array('status' => -1, 'msg' => 'Unknown Error'));
            }
        }
        return NULL;
    }

    public function getHeader()
    {
        return array(
            'X-Robots-Tag: noindex, nofollow',
            'Content-type: image/x-icon',
            'Cache-Control: public, max-age=604800'
        );
    }

    public function setFileMap(array $map)
    {
        $this->_file_map = $map;
        return $this;
    }

    public function setDefaultIcon($filePath)
    {
        $this->_default_icon = $filePath;
        return $this;
    }

    protected function getData()
    {
        $this->data = $this->_match_file_map();
        if ($this->data !== NULL) {
            $this->_log_message('Get icon from static file map, ' . $this->full_host);
            return $this->data;
        }

        $html = $this->getFile($this->params['origin_url']);

        if ($html && $html['status'] == 'OK') {
            $html = str_replace(array("\n", "\r"), '', $html);
            if (@preg_match('/((<link[^>]+rel=.(icon|shortcut icon|alternate icon|apple-touch-icon)[^>]+>))/i', $html['data'], $match_tag)) {
                if (isset($match_tag[1]) && $match_tag[1] && @preg_match('/href=(\'|\")(.*?)\1/i', $match_tag[1], $match_url)) {
                    if (isset($match_url[2]) && $match_url[2]) {
                        $match_url[2] = $this->filterRelativeUrl(trim($match_url[2]), $this->params['origin_url']);
                        $icon = $this->getFile($match_url[2], true);
                        if ($icon && $icon['status'] == 'OK') {
                            $this->_log_message("Success get icon from {$this->params['origin_url']}, icon url is {$match_url[2]}");
                            $this->data = $icon['data'];
                        }
                    }
                }
            }
        }

        if ($this->data != NULL) {
            return $this->data;
        }

        $redirected_url = $html['real_url'];
        $data = $this->getFile($this->full_host . '/favicon.ico', true);

        if ($data && $data['status'] == 'OK') {
            $this->_log_message("Success get icon from website root: {$this->full_host}/favicon.ico");
            $this->data = $data['data'];
        } else {
            $ret = $this->formatUrl($redirected_url);

            if ($ret) {
                $data = $this->getFile($this->full_host . '/favicon.ico', true);
                if ($data && $data['status'] == 'OK') {
                    $this->_log_message("Success get icon from redirect file: {$this->full_host}/favicon.ico");
                    $this->data = $data['data'];
                }

            }
        }
        if ($this->data == NULL) {
            $this->_log_message("Cannot get icon from {$this->params['origin_url']}");
            return FALSE;
        }
        return $this->data;
    }

    public function formatUrl($url)
    {
        $parsed_url = parse_url($url);

        if (!isset($parsed_url['host']) || !$parsed_url['host']) {
            if (!preg_match('/^https?:\/\/.*/', $url))
                $url = 'http://' . $url;
            $parsed_url = parse_url($url);
            if ($parsed_url == FALSE) {
                return FALSE;
            } else {
                $this->params['origin_url'] = $url;
            }
        }
        $this->full_host = (isset($parsed_url['scheme']) ? $parsed_url['scheme'] : 'http') . '://' . $parsed_url['host'] . (isset($parsed_url['port']) ? ':' . $parsed_url['port'] : '');
        return $this->full_host;
    }

    private function filterRelativeUrl($url, $URI = '')
    {
        if (strpos($url, '://') !== FALSE) {
            return $url;
        }

        $URI_part = parse_url($URI);
        if ($URI_part == FALSE)
            return FALSE;

        $URI_root = $URI_part['scheme'] . '://' . $URI_part['host'] . (isset($URI_part['port']) ? ':' . $URI_part['port'] : '');

        if (substr($url, 0, 2) === '//') {
            return $URI_part['scheme'] . ':' . $url;
        }

        if (strpos($url, '/') === 0) {
            return $URI_root . $url;
        }

        $URI_dir = (isset($URI_part['path']) && $URI_part['path']) ? '/' . ltrim(dirname($URI_part['path']), '/') : '';
        if (strpos($url, './') === FALSE) {
            if ($URI_dir != '') {
                return $URI_root . $URI_dir . '/' . $url;
            } else {
                return $URI_root . '/' . $url;
            }
        }

        $url = preg_replace('/[^\.]\.\/|\/\//', '/', $url);
        if (strpos($url, './') === 0)
            $url = substr($url, 2);

        $URI_full_dir = ltrim($URI_dir . '/' . $url, '/');
        $URL_arr = explode('/', $URI_full_dir);

        if ($URL_arr[0] == '..') {
            array_shift($URL_arr);
        }

        for ($i = 1; $i < count($URL_arr); $i++) {
            if ($URL_arr[$i] == '..') {
                $j = 1;
                while (TRUE) {
                    if (isset($dst_arr[$i - $j]) && $dst_arr[$i - $j] != FALSE) {
                        $dst_arr[$i - $j] = FALSE;
                        $dst_arr[$i] = FALSE;
                        break;
                    } else {
                        $j++;
                    }
                }
            }
        }

        $dst_str = $URI_root;
        foreach ($dst_arr as $val) {
            if ($val != FALSE)
                $dst_str .= '/' . $val;
        }
        return $dst_str;
    }


    private function getFile($url, $isimg = false, $timeout = 2)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        $ret = $this->curlExecFollow($ch, 2);

        if ($isimg) {
            $mime = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
            $mimeArray = explode('/', $mime);
        }
        $arr = array(
            'status' => 'FAIL',
            'data' => '',
            'real_url' => ''
        );
        if (!$isimg || $mimeArray[0] == 'image') {
            if ($ret != false) {
                $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $arr = array(
                    'status' => ($status >= 200 && $status <= 299) ? TRUE : FALSE,
                    'data' => $ret,
                    'real_url' => curl_getinfo($ch, CURLINFO_EFFECTIVE_URL)
                );
            }
            curl_close($ch);
            return $arr;
        } else {
            $this->_log_message("不是图片：{$url}");
            return $arr;
        }
    }

    private function curlExecFollow(&$ch, $maxredirect = null)
    {
        $mr = $maxredirect === null ? 5 : intval($maxredirect);
        if (ini_get('open_basedir') == '' && ini_get('safe_mode' == 'Off')) {
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $mr > 0);
            curl_setopt($ch, CURLOPT_MAXREDIRS, $mr);
        } else {
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
            if ($mr > 0) {
                $newurl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);

                $rch = curl_copy_handle($ch);
                curl_setopt($rch, CURLOPT_HEADER, true);
                curl_setopt($rch, CURLOPT_NOBODY, true);
                curl_setopt($rch, CURLOPT_NOSIGNAL, 1);
                curl_setopt($rch, CURLOPT_CONNECTTIMEOUT_MS, 800);
                curl_setopt($rch, CURLOPT_FORBID_REUSE, false);
                curl_setopt($rch, CURLOPT_RETURNTRANSFER, true);
                do {
                    curl_setopt($rch, CURLOPT_URL, $newurl);
                    $header = curl_exec($rch);
                    if (curl_errno($rch)) {
                        $code = 0;
                    } else {
                        $code = curl_getinfo($rch, CURLINFO_HTTP_CODE);
                        if ($code == 301 || $code == 302) {
                            preg_match('/Location:(.*?)\n/', $header, $matches);
                            $newurl = trim(array_pop($matches));
                            $newurl = $this->filterRelativeUrl($newurl, $this->params['origin_url']);
                        } else {
                            $code = 0;
                        }
                    }
                } while ($code && --$mr);
                curl_close($rch);
                if (!$mr) {
                    if ($maxredirect === null) {
                        trigger_error('Too many redirects. When following redirects, libcurl hit the maximum amount.', E_USER_WARNING);
                    } else {
                        $maxredirect = 0;
                    }
                    return false;
                }
                curl_setopt($ch, CURLOPT_URL, $newurl);
            }
        }
        return curl_exec($ch);
    }

    private function _match_file_map()
    {
        foreach ($this->_file_map as $rule => $file) {
            if (preg_match($rule, $this->full_host)) {
                return @file_get_contents($file);
            }
        }
        return NULL;
    }

    private function _log_message($message)
    {
        if ($this->debug_mode) {
            error_log(date('d/m/Y H:i:s : ') . $message . PHP_EOL, 3, "./my-errors.log");
        }
    }
}
