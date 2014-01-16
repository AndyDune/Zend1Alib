<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dune
 * Date: 06.11.12
 * Time: 18:58
 *
 *
 * Класс анализа формата введённого адреса электронной почты
 *
 *	Параметры адреса электронной почты:
 *
 * ----------------------------------------------------
 * | Библиотека: Alib                                  |
 * | Автор: Андрей Рыжов (Dune) <m@rznw.ru>            |
 * | Версия: 1.00                                      |
 * | Сайт: www.rznw.ru                                 |
 * ----------------------------------------------------
 *
 * История версий:
 * ----------------
 *
 * 1.00 (2012 ноябрь 06)
 * Замена для Dune_Data_Format_Mail
 *
 */
namespace Alib\String\Format;
class Email
{
    /**
     * Флаг правильного адреса электронной почты
     *
     * @access private
     * @var boolean
     */
    protected $check = false;

    /**
     * Enter description here...
     *
     * @var string
     * @access private
     */
    protected $mail = '';

    /**
     * Enter description here...
     *
     * @var string
     * @access private
     */
    protected $domain = '';

    /**
     * Конструктор. Принимает строку - адрес эл. почты
     *
     * @param string $mail адрес e-mail
     */
    public function __construct($mail)
    {
        $mail = strtolower(trim($mail));
        if(preg_match("|^[-0-9a-z_\.]+@[-0-9a-z_^\.]+\.[a-z]{2,4}$|i", $mail))
        {
            $this->mail = $mail;
            $this->domain = substr($mail, strpos($mail, '@') + 1);
            $this->check = true;
        }
    }

    /**
     * Возвращает результат проверки адреса эл. почты
     *
     * @return boolean
     */
    public function check()
    {
        return $this->check;
    }

    /**
     * Возврат адреса эл. почты
     *
     * @return string адрес эл. почты
     */
    public function getMail()
    {
        return $this->mail;
    }

    /**
     * Возврат домена эл. почты
     *
     * @return string домен эл. почты
     */
    public function getDomain()
    {
        return $this->domain;
    }}
