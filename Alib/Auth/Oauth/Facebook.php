<?php

namespace Alib\Auth\Oauth;
use Alib\Builder;
class Facebook extends AbstractClass
{
    
    protected $_callbackUrl    = '';
    protected $_siteUrl        = '';
    protected $_consumerKey    = '';
    protected $_consumerSecret = '';
    
    protected $_scope = 'email,read_stream';
    
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
        $path_add = '/dialog/oauth';
        $get_builder = new Builder\Get();
        $get_builder->set('client_id', $this->_consumerKey)
                    ->set('scope', $this->_scope)
                    ->set('redirect_uri', $this->_callbackUrl)
//                    ->set('response_type', 'code')
                    ;

        return 'https://www.' . $this->_siteUrl . $path_add . $get_builder->getString();
    }
    
    public function getAccessToken($code)
    {
        $path_add = '/oauth/access_token';
        $get_builder = new Builder\Get();
        $get_builder->set('client_id', $this->_consumerKey)
                    ->set('client_secret', $this->_consumerSecret)
                    ->set('redirect_uri', $this->_callbackUrl)
//                    ->set('type','client_cred') // Иногда без этого ключа не входит
                    ->set('code', $code);

        $url = 'https://graph.' . $this->_siteUrl . $path_add . $get_builder->getString();
        
        $result = @file_get_contents($url);
        
//       echo $result;
//       die();
        
        
        // Не удалось соединиться
        if (!$result)
        {
            $this->_setMessage(self::ERROR_GET_SIDED_DATA);
            return false;
        }
        
     $params = null;
     parse_str($result, $params);        
        
//        print_r($result);
//        die();
        $result = $params;
        if (!isset($result['access_token']))
        {
            $this->_setMessage(self::ERROR_AUTH_SIDED);
            $this->_error = 'facebookError';
            $this->_errorDescription = 'Ошибка при запросе данный с фейсбука';
            return false;
        }
        $this->_setMessage(self::GOOD_AUTH_SIDED);
        $this->_data = $result;
/*
 $graph_url = "https://graph.facebook.com/me?access_token=" 
       . $params['access_token'];

     $user = json_decode(file_get_contents($graph_url));        
*/        
        $path_add = '/me';
        $get_builder = new Builder\Get();
        $get_builder->set('access_token', $result['access_token'])
                     ;
        $url = 'https://graph.' . $this->_siteUrl . $path_add . $get_builder->getString();
        $result_2 = @file_get_contents($url);
     

        try
        {
            $result_2 = \Zend_Json::decode($result_2);
        }
        catch (\Exception $e)
        {
            // Не удалсь пропарсить json
            $this->_setMessage(self::ERROR_GET_SIDED_DATA);
            return false;
        }
        
        $this->_data = $result_2;
//        print_r($this->_data);
//        die();
        
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
        
        
/**
 *
Array
(
    [id] => 100001979986314
    [name] => Андрей Рыжов
    [first_name] => Андрей
    [last_name] => Рыжов
    [link] => http://www.facebook.com/profile.php?id=100001979986314
    [location] => Array
        (
            [id] => 109385842420478
            [name] => Ryazan
        )

    [bio] => Мужчина в самом расцвете сил, наделенный супер Тохманом и женой в придачу.
    [education] => Array
        (
            [0] => Array
                (
                    [school] => Array
                        (
                            [id] => 117974698245339
                            [name] => РГРТА
                        )

                    [year] => Array
                        (
                            [id] => 144560162276732
                            [name] => 1998
                        )

                    [type] => College
                )

        )

    [gender] => male
    [email] => dune@rznlf.ru
    [timezone] => 4
    [locale] => ru_RU
    [verified] => 1
    [updated_time] => 2011-03-04T00:17:16+0000
)
 * 
 * @param type $user_id
 * @return type 
 */        
        
        public function apiGetProfile($user_id)
        {
            if ($this->_data and is_array($this->_data) and count($this->_data))
                    return $this->_data;
            
            return false;
            
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
        if (isset($this->_data['id']))
            return $this->_data['id'];
        return null;
        
    }    
}
