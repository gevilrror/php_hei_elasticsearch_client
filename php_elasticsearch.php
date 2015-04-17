<?php
/**
 * Gevilrror php_elasticsearch
 *
 * @author Gevilrror <Gevilrror@qq.com>
 * @version 0.0.2 20150417
 */

/**
 * php_elasticsearch
 */
class php_elasticsearch
{
    var $hosts = array();
    var $debug = false;

    /**
     * __construct
     * @param [array] $config
     */
    function __construct($config = null)
    {
        if (!is_null($config)) {

            if (isset($config['hosts'])) {
                foreach ($config['hosts'] as $host) {
                    if (strpos($host, 'http') !== 0) {
                        $host = 'http://'.$host;
                    }

                    $pu = parse_url($host);

                    if ($pu) {
                        $scheme = $pu['scheme'].'://';
                        $userinfo = isset($pu['user'])?$pu['user'].':'.$pu['pass'].'@':'';
                        $host = isset($pu['host'])?$pu['host']:'';
                        $port = isset($pu['port'])?':'.$pu['port']:'';
                        $path = isset($pu['path'])?$pu['path']:'';
                        $this->hosts[] = rtrim($scheme.$userinfo.$host.$port.$path, '/').'/';
                    }
                }
            }

            if (isset($config['debug'])) {
                $this->debug = !!$config['debug'];
            }
        }

    }

    /**
     * getHost
     * @return [string] host
     */
    function getHost()
    {
        return empty($this->hosts)?false:count($this->hosts)>1?$this->hosts[array_rand($this->hosts)]:reset($this->hosts);
    }

    function jsonFormat($data)
    {
        if (empty($data) && (is_string($data) || is_null($data) || is_array($data))) {
            return new \stdClass();
        }

        if(is_array($data)) foreach ($data as $key => $value) {
            if (empty($data[$key]) && (is_string($data[$key]) || is_null($data[$key]) || is_array($data[$key]))) {
                $data[$key] = new \stdClass();
            }else{
                $data[$key] = $this->jsonFormat($data[$key]);
            }
        }

        return $data;
    }

    /**
     * exec
     * @param  array  $params
     * @return array
     */
    function exec($params = array())
    {
        if (!$host = $this->getHost()) {
            return false;
        }

        $url = $host;
        $query = array();
        $data = null;
        $method = 'GET';
        $opt = null;

        if (isset($params['index'])) {
            $url .= $params['index'].'/';
            unset($params['index']);

            if (isset($params['type'])) {
                $url .= $params['type'].'/';
                unset($params['type']);

                if (isset($params['id'])) {
                    $url .= $params['id'].'/';
                    unset($params['id']);
                }
            }
        }

        if (isset($params['action'])) {
            $url .= $params['action'];

            if (strtolower(trim($params['action'])) == '_bulk' && isset($params['body']) && is_array($params['body'])) {
                foreach ($params['body'] as $key => $request) {
                    $params['body'][$key] = is_string($request)?$request:json_encode($this->jsonFormat($request));
                }
                $params['body'] = implode("\n", $params['body'])."\n";
            }

            unset($params['action']);
        }

        if (isset($params['body'])) {
            $data = is_string($params['body'])?$params['body']:json_encode($this->jsonFormat($params['body']));
            unset($params['body']);
        }

        if (isset($params['method'])) {
            $method = strtoupper($params['method']);
            unset($params['method']);
        }

        if (isset($params['opt'])) {
            $opt = $params['opt'];
            unset($params['opt']);
        }

        if (isset($params['query'])) {
            $query = $params['query'];
            unset($params['query']);
        }

        foreach ($params as $param_key => $param_value) {
            $query[$param_key] = $param_value;
        }

        if (!empty($query)) {

            $url .= '?'.http_build_query($query);

            // $arrstr = array();
            // foreach ($query as $query_key => $query_value) {
            //     if (is_string($query_value)) {
            //         $qs = $query_key;
            //         if (!empty($query_value)) {
            //             $qs .= '='.urlencode($query_value);
            //         }
            //         $arrstr[] = $qs;
            //         unset($query[$query_key]);
            //     }
            // }

            // $url .= '?'.implode('&', $arrstr);

            // if (!empty($query)) {
            //     $url .= '&'.http_build_query($query);
            // }
        }

        $result = RESTful_curl::exec($method, $url, $data, $opt);

        if (stripos(trim($result['content_type']), 'application/json;') === 0) {
            if ($res = json_decode($result['content'], true)) {
                unset($result['content']);

                if ($this->debug) {
                    $res['_httpinfo'] = $result;
                    $res['_data'] = $data;
                }

                return $res;
            }
        } else if ($method == 'HEAD') {
            $res = array(
                'exists' => $result['http_code'] == 200?true:false,
            );

            if ($this->debug) {
                $res['_httpinfo'] = $result;
                $res['_data'] = $data;
            }

            return $res;
        }

        if ($this->debug) {
            $res = array();
            $res['_httpinfo'] = $result;
            $res['_data'] = $data;
            return $res;
        }else{
            return false;
        }
    }
}


/**
 * RESTful_curl
 */
class RESTful_curl
{
    /**
     * RESTful
     * @param [string] $method
     * @param [string] $url
     * @param [mixed] $data
     * @param [array] $opt
     */
    public static function exec($method, $url, $data = null, $opt = null)
    {
        $ci = curl_init();

        $curlopt = array();

        switch (strtoupper($method)) {
            case 'POST':
            case 'PUT':
            case 'HEAD':
            case 'DELETE':
                $curlopt[CURLOPT_CUSTOMREQUEST] = strtoupper($method);
                break;

            case 'GET':
            default:

                break;
        }

        $curlopt[CURLOPT_RETURNTRANSFER] = true;
        $curlopt[CURLOPT_URL] = $url;

        if (!is_null($data)) {
            $curlopt[CURLOPT_POSTFIELDS] = $data;
        }

        if (!is_null($opt)) {
            foreach ($opt as $key => $value) {
                if (is_numeric($key)) {
                    $curlopt[$key] = $value;
                    unset($opt[$key]);
                }
            }
        }

        if (!empty($curlopt)) {
            curl_setopt_array($ci, $curlopt);
        }

        $content = curl_exec($ci);
        $result = curl_getinfo($ci);
        $result['content'] = $content;

        return $result;
    }
}


