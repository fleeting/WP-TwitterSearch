<?php

/**
 * LICENSE
 *
 * This source file is subject to the new BSD license bundled with this package
 * in the file, LICENSE. This license is also available through the web at:
 * {@link http://www.opensource.org/licenses/bsd-license.php}. If you did not
 * receive a copy of the license, and are unable to obtain it through the web,
 * please send an email to matt@mattwilliamsnyc.com, and I will send you a copy.
 */
require_once 'Arc90/Service/Twitter/Response.php';

class Arc90_Service_Twitter_Search
{
    /** Entry point for the Twitter Search API */
    const API_URI = 'http://search.twitter.com';

    /** {@link http://apiwiki.twitter.com/Search+API+Documentation#Search} */
    const PATH_SEARCH = '/search';

    /** {@link http://apiwiki.twitter.com/Search+API+Documentation#Trends} */
    const PATH_TRENDS = '/trends';

    /** Callback function used to wrap JSON responses */
    protected $_callback;

    /** Response format */
    protected $_format  = 'json';

    /** Supported response formats */
    protected $_formats = array('json', 'atom');

    public function __construct($format ='json', $callback ='')
    {
        $this->setFormat($format)->setCallback($callback);
    }

    public function setCallback($callback)
    {
        $this->_callback = $callback;

        return $this;
    }

    public function setFormat($format)
    {
        if(!in_array(($format = strtolower($format)), $this->_formats))
        {
            self::_throwException(sprintf(
                '"%s" is not a valid response format. Valid formats include: %s',
                $format,
                join(', ', $this->_formats)
            ));
        }

        $this->_format = $format;

        return $this;
    }
    
    public function search($query, array $params =array())
    {
        $query = array('q' => $query);

        if('json' == $this->_format && '' != $this->_callback)
        {
            $query['callback'] = $this->_callback;
        }

        foreach($params as $key => $value)
        {
            switch($key)
            {
                case 'lang':
                {
                    if(2 == strlen($value))
                    {
                        $query[$key] = $value;
                    }

                    break;
                }
                case 'phrase':
                {
                  $query[$key] = $value;
                  break;
                }
                case 'nots':
                {
                  $query[$key] = $value;
                }
                case 'from':
                {
                  $query[$key] = $value;
                  break;
                }
                case 'geocode':
                {
                    // latitude,longitude,radius
                    if(preg_match('/^[+-]?\d+(\.\d+)?,[+-]?\d+(\.\d+)?,\d+(mi|km)$/', $value))
                    {
                        $query[$key] = $value;
                    }
                    break;
                }
                case 'page':
                case 'since_id':
                {
                    $value = intval($value);

                    if(1 <= $value)
                    {
                        $query[$key] = $value;
                    }
                    break;
                }
                case 'rpp':
                {
                    $value = intval($value);
                    
                    if(100 < $value)
                    {
                        $value = 100;
                    }
                    else if(1 > $value)
                    {
                        $value = 1;
                    }

                    $query[$key] = $value;
                    break;
                }
                case 'show_user':
                {
                    $query[$key] = 'true';
                }
            }
        }
        
        $uri = sprintf('%s.%s?%s', self::PATH_SEARCH, $this->_format, http_build_query($query));

        return $this->_sendRequest($uri);
    }

    public function trends()
    {
        return $this->_sendRequest(self::PATH_TRENDS . '.json');
    }

    protected static function _sendRequest($uri)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, self::API_URI . $uri);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, TRUE);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:', 'Accept-Charset: ISO-8859-1,utf-8'));

        $data = curl_exec($ch);
        $meta = curl_getinfo($ch);

        curl_close($ch);

        return new Arc90_Service_Twitter_Response($data, $meta);
    }

    protected static function _throwException($message)
    {
        /** @see Arc90_Service_Twitter_Search_Exception */
        require_once 'Arc90/Service/Twitter/Search/Exception.php';

        throw new Arc90_Service_Twitter_Search_Exception($message);
    }
}
