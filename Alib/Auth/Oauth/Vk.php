<?php

namespace Alib\Auth\Oauth;
use Alib\Builder;

class Vk extends AbstractClass
{
    
    protected $_callbackUrl    = '';
    protected $_siteUrl        = '';
    protected $_consumerKey    = '';
    protected $_consumerSecret = '';
    
    protected $_scope = 'friends,video,photos';
    
    protected $_apiUrl = 'http://api.vk.com/api.php';

    
    protected $_data = null;
    
    protected $_error = null;
    protected $_errorDescription = null;

    /**
     *
        $config = array(
            'callbackUrl' => 'http://example.com/callback.php',
            'siteUrl' => 'http://twitter.com/oauth',
            'consumerKey' => 'gg3DsFTW9OU9eWPnbuPzQ',
            'consumerSecret' => 'tFB0fyWLSMf74lkEu9FTyoHXcazOWpbrAjTCCK48A'
        );
     * 
     * @param type $config 
     */
    public function __construct($config = null)
    {
        if (is_array($config))
        {
            if (isset($config['callbackUrl']))
                $this->_callbackUrl = $config['callbackUrl'];
            if (isset($config['siteUrl']))
                $this->_siteUrl = rtrim ($config['siteUrl'], ' /');
            if (isset($config['consumerKey']))
                $this->_consumerKey = $config['consumerKey'];
            if (isset($config['consumerSecret']))
                $this->_consumerSecret = $config['consumerSecret'];
        }
    }

    /**
     *
notify      Пользователь разрешил отправлять ему уведомления. 
friends     Доступ к друзьям. 
photos      Доступ к фотографиям. 
audio       Доступ к аудиозаписям. 
video       Доступ к видеозаписям. 
docs        Доступ к документам. 
notes       Доступ заметкам пользователя. 
pages       Доступ к wiki-страницам. 
offers      Доступ к предложениям (устаревшие методы). 
questions   Доступ к вопросам (устаревшие методы). 
wall        Доступ к обычным и расширенным методам работы со стеной. 
messages    (для Standalone-приложений) Доступ к расширенным методам работы с сообщениями. 
ads         Доступ к расширенным методам работы с рекламным API. 
offline     Доступ к API в любое время со стороннего сервера.
     * 
     * @param type $array 
     */
    public function setScope(array $array)
    {
        $this->_scope = implode(',', $array);
        
    }   

    
    public function setAccessToken($code)
    {
        $this->_accessToken = $code;
        
    }   
    
    
    public function getPathToDialog()
    {
        $path_add = '/authorize/';
        $get_builder = new Builder\Get();
        $get_builder->set('client_id', $this->_consumerKey)
                    ->set('scope', $this->_scope)
                    ->set('redirect_uri', $this->_callbackUrl)
                    ->set('response_type', 'code');

        return 'http://' . $this->_siteUrl . $path_add . $get_builder->getString();
    }
    
    public function getAccessToken($code)
    {
        $path_add = '/access_token/';
        $get_builder = new Builder\Get();
        $get_builder->set('client_id', $this->_consumerKey)
                    ->set('client_secret', $this->_consumerSecret)
                    ->set('code', $code);

        $url = 'https://' . $this->_siteUrl . $path_add . $get_builder->getString();
        
        $result = @file_get_contents($url);
        
//       echo $result;
//       die();
        
        
        // Не удалось соединиться
        if (!$result)
        {
            $this->_setMessage(self::ERROR_GET_SIDED_DATA);
            return false;
        }
        try
        {
            $result = \Zend_Json::decode($result);
        }
        catch (\Exception $e)
        {
            // Не удалсь пропарсить json
            $this->_setMessage(self::ERROR_GET_SIDED_DATA);
            return false;
        }
//        print_r($result);
//        die();
        
        if (!isset($result['access_token']))
        {
            $this->_setMessage(self::ERROR_AUTH_SIDED);
            $this->_error = $result['error'];
            $this->_errorDescription = $result['error_description'];
            return false;
        }
        $this->_setMessage(self::GOOD_AUTH_SIDED);
        $this->_data = $result;

        return $result['access_token'];
    }
    
    public function getError()
    {
        return $this->_error;
    }
    
    public function getErrorDescription()
    {
        return $this->_errorDescription;
    }
    
    
	function api($method,$params=false) 
        {
		if (!$params) $params = array(); 
		$params['api_id'] = $this->_consumerKey;
		$params['v'] = '3.0';
		$params['method'] = $method;
		$params['timestamp'] = time();
		$params['format'] = 'json';
		$params['random'] = rand(0,10000);
		ksort($params);
		$sig = '';
		foreach($params as $k=>$v) 
                {
		    $sig .= $k.'='.$v;
		}
		$sig .= $this->_consumerSecret;
		$params['sig'] = md5($sig);
		$query = $this->_apiUrl . '?' . $this->params($params);
		$res = file_get_contents($query);
		return json_decode($res, true);
	}
        
        public function apiGetProfile($user_id)
        {
            $resp = $this->api('getProfiles', array('uids' => $user_id));
            if (!is_array($resp) or !isset($resp['response'][0]))
            {
                return false;
            }
            $data = $resp['response'][0];
            return $data;
            
        }


        function params($params) 
        {
		$pice = array();
		foreach($params as $k=>$v) {
			$pice[] = $k.'='.urlencode($v);
		}
		return implode('&',$pice);
	}
    
    
    public function getUserId()
    {
        if (isset($this->_data['user_id']))
            return $this->_data['user_id'];
        return null;
        
    }    
}
