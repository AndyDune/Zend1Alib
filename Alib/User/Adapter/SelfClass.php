<?php

/**
 * 
 */
namespace Alib\User\Adapter;

use Alib;
use Alib\Exception;
use Alib\Hash;
use Alib\User;
use Alib\String;

class SelfClass extends AbstractClass
{
    protected $_statisticData = null;
    
    protected $_saltLength = 5;
    
    /**
     * Время на верификацию email.
     * По прошествии этого интервала запись надо удалить.
     * 
     * @var integer
     */
    protected $_verificationInterval = 604800;
    
    
    protected $_tryCount    = 5;
    protected $_tryInterval = 3600;
    
    protected $_data    = null;

    
    /**
     * Время на смену пароля.
     * По прошествии этого интервала возможность аннулируется.
     * 
     * @var integer
     */
    protected $_passwordRecallInterval = 7200;
    
    protected $_passwordNew = null;
    protected $_secretCode = null;

    public function changePassword()
    {
        $password_new    = $this->_getArgument(1, true);
        $password_old    = $this->_getArgument(2, true);
        $user_data = $this->_base->getCurrentUserData();
        
        $table = $this->_base->getUserDataTableObject();
        $select = $table->getSelect();
        $data = $this->_getUserData($user_data['id'], 'id', $select);
        
        if (!$this->_comparePassword($password_old, $data['password']))
        {
            $this->_base->message(User\Adapter::ERROR_WRONG_PASSWORD);
            return false;
        }
        
        $data = array();
        $data['password'] = $this->_encodePassword($password_new);
        
        $table->updateWithField($data, $user_data['id']);
        $this->_base->message(User\Adapter::GOOD_PASSWORD_CHANGE);
        return true;
    }

    public function recallOrderPassword()
    {
        
        $email  = $this->_getArgument(1, true);
        
        $table = $this->_base->getUserDataTableObject();
        $select = $table->getSelect();
        $this->_data = $data = $this->_getUserData($email, 'email', $select);
        if (!$data)
        {
            $this->_base->message(User\Adapter::ERROR_NO_DATA);
            return false;
        }
        
        // Слишком часто не надо
        if ($data['secret_time'])
        {
            $date = new \Zend_Date($data['secret_time']);
            $secret_time = $date->get();
            if ($secret_time > time() - $this->_passwordRecallInterval)
            {
                $this->_base->message(User\Adapter::ERROR_NO_TIME_REST);
                return false;
            }        
        }
        $this->_secretCode = md5(time());
        
        $data_to_update = array(
            'secret_code' => $this->_secretCode,
            'secret_mode' => 1,
            'secret_time' => date('Y-m-d H:i:s'),
        );
        $table->updateWithId($data_to_update, $data['id']);
        $this->_base->message(User\Adapter::GOOD_PASSWORD_RECALL_ORDER);
        return true;        
        
    }
    
    public function getSecretCode()
    {
        return $this->_secretCode;
    }    
    

    /**
     *
     * @return boolean флаг успеха операции
     */
    public function recallMakePassword()
    {
        $data_in['id'] = $this->_getArgument(1, true);
        $data_in['code'] = $this->_getArgument(2, true);

        if (!is_array($data_in))
        {
            $this->_base->message(User\Adapter::ERROR_NO_DATA);
            return false;
        }
        
        if (!isset($data_in['id']) or !isset($data_in['code']))
        {
            $this->_base->message(User\Adapter::ERROR_NO_DATA);
            return false;
        }
        
        $table = $this->_base->getUserDataTableObject();
        
        $this->_data = $data = $table->get((int)$data_in['id']);
        
        if (!$data)
        {
            $this->_base->message(User\Adapter::ERROR_NO_DATA);
            return false;
        }    
        
        if ($data['status'] < 1 or $data['secret_mode'] != 1)
        {
            $this->_base->message(User\Adapter::ERROR_NO_PASSWORD_RECALL);
            return false;
        }    
        // Проверка на время верификации
        // Нет данных о времени метки
        if (!$data['secret_time'])
        {
            $this->_base->message(User\Adapter::ERROR_NO_DATA);
            return false;
        }    
        
        // Проверка на время верификации
        // Прошло много времени
        
        $date = new \Zend_Date($data['secret_time']);
        $secret_time = $date->get();
        if ($secret_time < time() - $this->_passwordRecallInterval)
        {
            $data_to_update = array(
                'secret_code' => null
            );
            $table->updateWithId($data_to_update, $data['id']);
            
            $this->_base->message(User\Adapter::ERROR_NO_DATA);
            return false;
        }    
        
        if ($data_in['code'] != $data['secret_code'])
        {
            $this->_base->message(User\Adapter::ERROR_BAD_PASSWORD_RECALL);
            return false;
        }        
        
        $new_password = uniqid();
        $this->_passwordNew = $new_password;
        
        $data_to_update = array(
            'secret_code' => null,
            'password'    => $this->_encodePassword($new_password)
        );
        $table->updateWithId($data_to_update, $data['id']);
        $this->_base->message(User_Adapter::GOOD_PASSWORD_RECALL_MAKE);
        return true;        
    }
    
    public function getNewPassword()
    {
        return $this->_passwordNew;
    }    
    
    public function login()
    {
        $data = null;
        $login    = $this->_getArgument(1, true);
        
        $using_email = $this->_getArgument(3);
        
        // Нормализуем логин
        $login = $this->_prepareLoginNormal($login);
        
        $select = $this->_base->getUserDataTableObject()->getSelect();
        
        // Проверим
        if ($using_email and String::strpos($login, '@'))
        {
            $data = $this->_getUserData($login, 'email', $select);
        }
        
        if (!$data)
        {
            $data = $this->_getUserData($login, 'login_normal', $select);
        }
        
        
        if (!$data)
        {
            $this->_base->message(User\Adapter::ERROR_NO_DATA);
            return false;
        }

        
        if (!$this->_checkTryEnter($data['id'], $this->_tryCount, $this->_tryInterval))
        {
            $this->_base->message(User\Adapter::ERROR_RICH_TRY_LIMIT);
            return false;
        }
        
        $password = $this->_getArgument(2, true);

        // Пользователь заблокирован
        if ($data['status'] < 1)
        {
            $this->_base->message(User\Adapter::ERROR_USER_BLOCK);
            return false;
        }        
        
        if (!$password or !$data['password'])
        {
            $this->_base->message(User\Adapter::ERROR_WRONG_MODE);
            return false;
        }
        
       
        if (!$this->_comparePassword($password, $data['password']))
        {
            $this->_base->message(User\Adapter::ERROR_WRONG_PASSWORD);
            return false;
        }
        $data['statistics'] = $this->_getStatisticData($data['id']);
        $this->_data = $data;
        return true;
    }

    public function getSecretCodeOld()
    {
        if (isset($this->_data['secret_code']))
            return $this->_data['secret_code'];
        return null;
    }    
    
    protected function _prepareLoginNormal($login)
    {
        // Нормализуем логин
        $login_normal = String::strtolower($login);
        // Заменяем ё на е
        return preg_replace('|ё|u', 'е', $login_normal);
        
    }


    public function registration()
    {
        $data = $this->_getArgument(1, true);

        try
        {

            if (!is_array($data))
            {
                throw new Exception('Параметр должен быть один и массивом он быть должен.');
            }
            if (!isset($data['email']) or !isset($data['password']))
            {
                throw new Exception('Должны быть обязятельные ключи: email и password.');
            }

            $this->_passwordNew = $data['password'];

            $data['password'] = $this->_encodePassword($data['password']);

            // Без логина
            //$data['login_normal'] = $this->_prepareLoginNormal($data['login']);

            $data['login_normal'] = null;
            $data['login'] = null;

            $data['date_insert'] = date('Y-m-d H:i:s');


            // Статус меньше 1 - пользователь заблокирован
            $data['status'] = 0;
            $data['type'] = 'self';
            if (!isset($data['name']))
                $data['name'] = $data['login'];

            $this->_secretCode = md5(time());

            $data['secret_code'] = $this->_secretCode;
            $data['secret_time'] = $data['date_insert'];
            $data['secret_mode'] = 0;

            $table = $this->_base->getUserDataTableObject();

            $id = $table->insert($data);
            $data['id'] = $id;

            $this->_data = $data;
            return true;
        }
        catch (Exception $e)
        {
            $this->_base->message(User\Adapter::ERROR_NO_DATA);
            return false;
        }
    }    

    /**
     * Верификация аккаунта через email
     * Принимает параметн массив с ключами:
     * id - идентификатор записи или email
     * code - секретный код
     * 
     * @return boolean
     */
    public function verification()
    {
        $data_in = $this->_getArgument(1, true);

        if (!is_array($data_in))
        {
            $this->_base->message(User\Adapter::ERROR_NO_DATA);
            return false;
        }
        
        if (!isset($data_in['id']) or !isset($data_in['code']))
        {
            $this->_base->message(User\Adapter::ERROR_NO_DATA);
            return false;
        }
        $table = $this->_base->getUserDataTableObject();
        
        if (!String::strpos($data_in['id'], '@'))
        {
            $this->_data = $data = $table->get((int)$data_in['id']);
        }
        // Попытка проверки с использованеим email
        else
        {
            $select = $table->getSelect();
            $this->_data = $data = $this->_getUserData($data_in['id'], 'email', $select);
        }     

        //Alib\Test::pr($data, 1);

        if (!$data)
        {
            $this->_base->message(User\Adapter::ERROR_NO_DATA);
            return false;
        }    
        if ($data['status'] != 0 or $data['secret_mode'] != 0)
        {
            $this->_base->message(User\Adapter::ERROR_NO_NEED_VERIFICATION);
            return false;
        }    
        // Проверка на время верификации
        // Нет данных о времени мтки
        if (!$data['secret_time'])
        {
            $this->_base->message(User\Adapter::ERROR_NO_DATA);
            return false;
        }    
        
        // Проверка на время верификации
        // Прошло много времени
        
        $date = new \Zend_Date($data['secret_time']);
        $secret_time = $date->get();
        if ($secret_time < time() - $this->_verificationInterval)
        {
            $table->deleteWithId($data['id']);
            $this->_base->message(User\Adapter::ERROR_NO_DATA);
            return false;
        }    
        
        if ($data_in['code'] != $data['secret_code'])
        {
            $this->_base->message(User\Adapter::ERROR_BAD_VERIFICATION);
            return false;
        }        
        
        $data_to_update = array(
            'status' => 1,
            'secret_code' => null
        );
        $table->updateWithId($data_to_update, $data['id']);
        $this->_base->message(User\Adapter::GOOD_VERIFICATION);
        return true;
    }    
    
    
    /**
     * Сравнение строки с отхешированням паролем.
     * 
     * @param string $password строка для сравнения с паролем
     * @param string $password_have_hashed отхешированный пароль
     * @return boolean 
     */
    protected function _comparePassword($password, $password_have_hashed)
    {
        $hash = new Hash($password, $password_have_hashed);
        $hash->setSaltLength($this->_saltLength);
        return $hash->compare();

    }
    
    /**
     * Хеширование пароля.
     * 
     * @param string $password 
     * @return string 
     */
    protected function _encodePassword($password)
    {
        $hash = new Hash($password);
        $hash->setSaltLength($this->_saltLength);
        return $hash->hash();
    }
    
    
    protected function _getUserData($value, $field, $select)
    {
        $select->addFilterAnd($field, $value);
        $data = $select->get(1);
        if (count($data))
        {
            $data = $data->current()->toArray();
        }
        else
            $data = null;
        return $data;
    }




    protected function _getStatisticData($id, $statistic = null)
    {
        if (!$statistic)
            $statistic = $this->_base->getUserDataTableObject()->getTableObjectOfGroup('statistics');
        if (!$this->_statisticData)
        {
            $this->_statisticData = $statistic->get($id);
        }
        return $this->_statisticData;
    }

    protected function _checkTryEnter($id, $count_enters_can, $time_interval)
    {
        $statistic_table_object = $this->_base->getUserDataTableObject()->getTableObjectOfGroup('statistics');
        
        $time_now = time();
        $id = (int)$id;
//        $data = $data_statistics;
        $data = $this->_getStatisticData($id, $statistic_table_object);


//        print_r($data);
        //enter_try_count

        $statistic_table_object->useId($id);
        // Нет статистики. Ответ положительный.
        if ($data === null)
        {
            $insert_data = array(
                                'time_last_enter_try' => date('Y-m-d H:i:s'),
                                'user_id' => $id,
                                'enter_try_count' => 1
                                );
            $statistic_table_object->insert($insert_data, false);
            return true;
        }



        $date_object = new Alib\Format\Date($data['time_last_enter_try']);
        // Прошло много времени с послед$date_object->getTimeStamp()ней попытки входа. Ответ положительный.
        if ($date_object->getTimeStamp() + $time_interval < $time_now)
        { 
            $update_data = array(
                                'time_last_enter_try' => date('Y-m-d H:i:s'),
                                'enter_try_count' => 1
                                );
            $statistic_table_object->updateWithId($update_data);
            return true;
        }

        // Попыток входа больше позволенного. Ответ отрицательный.
        if ($data['enter_try_count'] > $count_enters_can)
            return false;

        // Увеличиваем счетчик. Ответ положительный
        $update_data = array(
                            'enter_try_count' => $data['enter_try_count'] + 1
                            );
        $statistic_table_object->updateWithId($update_data);
        return true;
    }    
    
}
