<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Alib;
use Alib\String\Format\Email;
class Mail extends \Zend_Mail
{
    /*
    public function __construct($charset = null)
    {
        parent::__construct('UTF-8');
    }
     * 
     */

    protected $_goodEmailDomains = ['gmail.com', 'yandex.ru', 'mail.ru', 'list.ru', 'pochta.ru', 'inbox.ru', 'bk.ru'];

    public static function factory(array $params = [])
    {
        /*
        $from = 'sendmail@1rzn.ru';
        $config = array('auth' => 'login',
            'username' => $from,
            'password' => 'password');

        $transport = new \Zend_Mail_Transport_Smtp('smtp.yandex.ru', $config);
        \Zend_Mail::setDefaultTransport($transport);
        */
        $options = Registry::get('options');
        $from = $options['site']['email']['smtp']['email'];

        if (isset($params['from']))
        {
            $from = $params['from'];
        }

        $domain = Registry::get('domain');
        $obj = new self('UTF-8');
        $obj->setFrom($from, $domain);
        return $obj;
    }

    public function send($transport = null)
    {
        $mails = $this->_to;
        foreach($mails as $mail)
        {
            $mailCheck = new Email($mail);
            if (!in_array($mailCheck->getDomain(), $this->_goodEmailDomains))
            {
                goto send;
            }
        }
        try
        {
            parent::send($transport);
        }
        catch (\Exception $e)
        {
            goto send;
        }

        if (false)
        {
            send:
            $options = Registry::get('options');
            $from = $options['site']['email']['smtp']['email'];
            $config = array('auth' => $options['site']['email']['smtp']['auth'],
                'username' => $from,
                'password' => $options['site']['email']['smtp']['password']);

            $transport = new \Zend_Mail_Transport_Smtp($options['site']['email']['smtp']['server'], $config);
            parent::send($transport);

        }
    }


}
