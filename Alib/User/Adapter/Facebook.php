<?php

/**
 * 
 */
namespace Alib\User\Adapter;

use Alib\User;
use Alib\Auth;

class Facebook extends AbstractClass
{
    protected $_type = 'facebook';
    
    protected $_oauth  = null;
    protected $_userId = null;
    
    public function login($id, Auth\Oauth\AbstractClass $oauth, $with_registration = true)
    {
        // Информация о пользователе из базы
        $user_data = null;
        $this->_userId = $id;
        $this->_oauth  = $oauth;
        
        $select = $this->_base->getUserDataTableObject()->getSelect();
        
        $select->addFilterAnd('type', $this->_type);
        $select->addFilterAnd('type_id', (string)$this->_userId);
        $data = $select->get(1);
        if (count($data))
            $user_data = $this->_data = $data->current();
            
        if ($with_registration and !$user_data)
        {
            $result = $this->_registration($id, $oauth);
            
            // Ошибка регистрации. Это ошибка вставки данных или запроса из vk
            if (!$result)
            {
                $this->_base->message(User\Adapter::ERROR_NO_DATA_FROM_SIDED);
                return false;
            }
            // Повторная попытка залогиниться после регистрации.
            $this->login($id, $oauth, false);
        }
        
        if (!$this->_data)
        {
            // Ошибка входа
            $this->_base->message(User\Adapter::ERROR_SIDED_AUTH);
            return false;
        }
        
        if ($this->_data['status'] < 1)
        {
            $this->_data = null;
            // Пользователь заблокирован
            $this->_base->message(User\Adapter::ERROR_USER_BLOCK);
            return false;
        }
        
        return true;
    }

    
    protected function _registration($id, Auth\Oauth\Facebook $oauth)
    {
        $data = $oauth->apiGetProfile($id);
        
        if (!$data or !isset($data['id']))
            return false;
/*        
Array
(
    [uid] => 142577219
    [first_name] => Андрей
    [last_name] => Рыжов
)        
*/        
        $table = $this->_base->getUserDataTableObject();
        
        $name = $data['first_name'];
        if ($data['last_name'])
        {
            $name .= ' ' . $data['last_name'];
        }
        
        if (!$name)
            $name = $data['name'];
        
        $data = array
        (
            'status'  => 1,
            'type'    => $this->_type,
            'type_id' => (string)$id,
            'name'    => $name,
            
        );
        
        
        return $table->insert($data);
        
        print_r($data);
        die();
        
        return true;
    }
    
    
}
