<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dune
 * Date: 15.11.12
 * Time: 17:01
 *
 *
 *
 */
namespace Alib\Controller\Traits;
trait FormProcessingController
{
    protected $_traitValueToStoreInSession = [];
    protected $_traitParametersToStoreInSession = [];

    public function formGetReturnParameters()
    {
        $all = $this->getAllParams();
        if (isset($all['return']['module']) and isset($all['return']['url'])
            and $all['return']['url']
            )
        {
            return $all['return'];
        }
        return null;
    }

    public function formValueToSession($key, $value)
    {
        $this->_traitValueToStoreInSession[$key] = $value;
        return $this;
    }
    public function formGetValueToSession($key)
    {
        /** @var $session \Alib\Session\FormData */
        $session = $this->_helper->session()->get();
        return $session->{$key};
    }


    /**
     *
     * todo Доделать сохранение только перечисленных параметров и исключение.
     *
     * @param null $leaveOnly
     * @param $exclude
     */
    public function formParamsToSession($leaveOnly = null, $exclude = null)
    {
        $params = $this->getAllParams();
        $this->__traitParametersToStoreInSession = $params;
        return $this;
    }

    public function formSessionToView($name = 'session')
    {
        /** @var $session \Alib\Session\FormData */
        $session = $this->_helper->session()->get();
        $this->view->$name = $session;
        return $this;
    }



    public function formStoreSessionData()
    {
        /** @var $session \Alib\Session\FormData */
        $session = $this->_helper->session()->get();
        foreach($this->_traitParametersToStoreInSession as $key => $value)
        {
            $session->{$key} = $value;
        }
        foreach($this->_traitValueToStoreInSession as $key => $value)
        {
            $session->{$key} = $value;
        }

        return $this;
    }

    public function formClearSessionData()
    {
        /** @var $session \Alib\Session\FormData */
        $session = $this->_helper->session()->get();
        $session->unsetAll();
    }



    /**
     * Ответ на результат сохранения данных формы аякс ответом.
     * Переадча сообщения и html содержимого (если такая переменная передана виду).
     *
     * В модуле, который использует этот метод должен быть лейаут ajax
     *
     */
    public function formAjaxResponse()
    {
        // Если аякс - не ридеректим.
        if ($this->isAjax())
        {
            return false;
        }
        $this->view->message = $this->_helper->message()->get();
        $this->_setLayout('ajax')->_setView();
    }


}
