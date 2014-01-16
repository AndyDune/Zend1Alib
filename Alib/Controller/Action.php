<?php
/*
 * V.06
 *
 * Базовый
 *
 * Версии:
 *
 *   2011-09-15 Внедрен новый метод isAjax() - проверка на аяксовость.
 *   2011-08-31 Доработка методов _redirectToUrlIfNotAjax() и _redirectIfNotAjax
 *              Возможна передача первым параметром объектов Request и помошников действия
 *   2011-07-29 _redirectToUrlIfNotAjax() - Редирект на урл, если не аякс.
 *   2011-03-09 init() - сделан финальным. Но запускает все методы класса с префиксом "_init"
 *
 *   2011-02-21 Введены методы _setLayout() и _setView()
 */
namespace Alib\Controller;
use Alib;
class Action extends \Zend_Controller_Action
{
    use \Alib\System\Traits\BuildClassName;

    /**
     * Информация о пользователе, если он авторизован.
     *
     * @var array|null
     */
    protected $_user     = null;

    /**
     * Авторизованный ли пользователь работате с контроллером
     * @var boolean
     */
    protected $_userAuth = false;


    /**
     * Контейнер-модификатор урла
     * @var Alib\Request
     */
    protected $_url = null;


    final public function init()
    {
        /*
         * Вынесено в контроллеры модулей
         *
        $auth = Alib\Auth::getInstance()->getIdentity();
        if ($auth)
        {
            $this->_user           = $auth;
            $this->_userAuth       = true;

            $this->view->i      = $auth;
            $this->view->iAuth  = true;
        }
        else
        {
            $auto = new Alib\User\AutoEnter();
            $auto->login();
            $auth = Alib\Auth::getInstance()->getIdentity();
            if ($auth)
            {
                $this->_user           = $auth;
                $this->_userAuth       = true;

                $this->view->i      = $auth;
                $this->view->iAuth  = true;

            }
        }

        */

        $this->_init();
    }

    protected function _init()
    {

    }

    /**
     * @return \Zend_Controller_Action_HelperBroker
     */
    public function getHelperBroker()
    {
        return $this->_helper;
    }

    public function pr($value, $stop = false)
    {
        \Alib\Test::pr($value, $stop);
    }
    /**
     * @param $name
     * @param $group
     * @param string $realization
     * @param string $module
     * @return \Alib\Record\AbstractClass\Record
     */
    public function getRecord($name, $group, $realization = 'base', $module = 'Www')
    {
        return $this->_getRecordObject($name, $group, $realization, $module);
    }


    public function preDispatch()
    {
        $this->_url = $this->view->url = Alib\Request::getInstance();

        $reg = Alib\Registry::getInstance();
        $this->view->domain = $reg->get('domain');


//        $this->_redirect('/access/login/');
//        $this->_forward('login', 'access');
    }


    /**
     * Проверка на аяксовость запроса.
     *
     * @param string $get_key ключ в $_GET который может означать что запрос ajax
     * @return boolean
     */
    public function isAjax($get_key = null)
    {
        $ajax = false;
        if ($this->getRequest()->isXmlHttpRequest())
        {
            $ajax = true;
        }
        else if ($get_key and $this->_helper->get()->getValue($get_key))
        {
            $ajax = true;
        }

        return $ajax;
    }

    /**
     * Упрощенная работа с хелпером layout из действия.
     *
     * @param string $layout
     * @return Action
     */
    protected function _setLayout($layout = null)
    {
        // Отключить мегашаблон для действия
        if ($layout === null)
        {
            $this->_helper->layout->disableLayout();
        }
        // Установка нестандартного layout
        else
        {
            $this->_helper->layout->setLayout($layout);
        }
        return $this;
    }

    /**
     * Упрощенная работа с хелпером layout из действия.
     *
     * @param string|null $view без расширения от папки видов контроллера
     * @return Action
     */
    protected function _setView($view = null)
    {
        // Отключить мегашаблон для действия
        if ($view === null)
        {
            $this->_helper->viewRenderer->setNoRender();
        }
        // Установка нестандартного вида
        else
        {
            $this->_helper->viewRenderer->setScriptAction($view);
        }
        return $this;
    }


    /**
     * Делать редирект, если запрос не через аякс.
     *
     * @param string $url урл, по которому надо редиректнуть
     * @param array $formdata данныеиз которых сробирается урл для редиректа.
     * @return boolean
     */
    protected function _redirectIfNotAjax($url = null, $formdata = null)
    {
        $redirectOptions = ['exit' => false];
        // Если аякс - не ридеректим.
        if ($this->getRequest()->isXmlHttpRequest())
        {
            return false;
        }

        $reg = Alib\Registry::getInstance();
        $domain = $reg->get('domain');

        // Если есть данные для сборки урла
        if (isset($formdata['return']['module']) and isset($formdata['return']['url']))
        {
            $module = $formdata['return']['module'];
            $url = $formdata['return']['url'];


            $this->redirect('http://' . $module . '.' . $domain . $url);
        }
        // Из параметра HTTP_REFERER
        else if (isset($_SERVER['HTTP_REFERER']))
        {
            $this->redirect($_SERVER['HTTP_REFERER']);
        }
        else if ($url !== null)
        {
            if ($url instanceof Alib\Request)
                $url = $url->getUrl();
            else if ($url instanceof \Application\Www\ControllerHelper\UrlConstructor)
                $url = $url->getUrl();

            $this->redirect($url);
        }
        // на главную портала
        else
            $this->redirect('http://www.' . $domain);
        return true;
    }


    /**
     * Делать редирект, если запрос не через аякс.
     *
     * @param string $url урл, по которому надо редиректнуть
     * @param array $formdata данныеиз которых сробирается урл для редиректа.
     * @return boolean
     */
    protected function _redirectToUrlIfNotAjax($url = null, $formdata = null)
    {
        // Если аякс - не ридеректим.
        if ($this->getRequest()->isXmlHttpRequest())
        {
            return false;
        }

        $reg = Alib\Registry::getInstance();
        $domain = $reg->get('domain');


        // Если есть данные для сборки урла
        if (isset($formdata['return']['module']) and isset($formdata['return']['url']))
        {
            $module = $formdata['return']['module'];
            $url = $formdata['return']['url'];

            $this->redirect('http://' . $module . '.' . $domain . $url);
        }
        // Из параметра HTTP_REFERER
        else if ($url !== null)
        {
            if ($url instanceof Alib\Request
                /*
                                or
                                $url instanceof Controller_Action_Helper_UrlConstructor
                                or
                                $url instanceof Controller_Action_Helper_Request
                */
            )
                $url = $url->getUrl();

            $this->redirect($url);
        }
        else if (isset($_SERVER['HTTP_REFERER']))
        {
            $this->redirect($_SERVER['HTTP_REFERER']);
        }
        // на главную портала
        else
            $this->redirect('http://www.' . $domain);
        return true;
    }

    public function redirect($url, array $options = array())
    {
        if (!array_key_exists('exit', $options))
            $options['exit'] = false;
        parent::redirect($url, $options);
    }


}
