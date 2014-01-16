<?php
/*
 * V.02
 * 
 * Динамическое представление файла Dune_request
 * Использование этого вида предпочтительней
 * 
 * Версии:
 * 2011-09-19 Добавлен метод clearGet() - аналог cleanGet()
 *
 *
 */
namespace Alib;
class Request extends \Dune_Request
{
    static protected $instance = null;
    public function __toString()
    {
        return $this->getUrl();
    }

    /**
     * ������� ��� ��������� _GET
     *
     * @return Dune_Request
     */
    public function resetGet()
    {
        $this->_GET = $_GET;
        return $this;
    }
    
    
    /**
     * Сбросить GET за исключением ключей в $exept.
     * 
     * @param string|array $exept ключи GET, которые не надо удалять
     * @return  rzn\lib\www\Request
     */
    public function clearGet($exept = null)
    {
        return $this->cleanGet($exept);
    }    

}


