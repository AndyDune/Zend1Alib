<?php

/** 
 * Генерация случайной строки
 * В начале строки используется более широкий набор символов.
 *
 *
 *
 */
namespace Alib\String;
class GenerateRandom
{
    /**
     * По умолчанию строкуа имеет длину 20 символов
     *
     * @var int|null
     */
    protected $_length = 7;
    
    protected $_chars = array
    (
            'a', 'b', 'v',
            'g', 'd', 'e',
            'z',
            'i', 'k',
            'l', 'm', 'n',
            'o', 'p', 'r',
            's', 't', 'u',
            'f', 'h', 'c',
            's',
            'y', 'x', 'j', 'q', 'w',
            '1','2','3','4','5','6','7','8','9','0'
     );    
    
        public function __construct($length = null)
        {
            if ($length)
                $this->_length = $length;
            
        }
        
        /**
         *
         * @param integer|null $length требуемая длина строки
         * @return string 
         */
        public function get($length = null, $plusUniqid = true)
        {
            if (!$length)
                $length = $this->_length;
            $max = count($this->_chars) - 1;
            $result = '';
            for ($x = 0; $x < $length; $x++)
            {
                $result .= $this->_chars[rand(0, $max)];
            }
            if ($plusUniqid)
                return $result . uniqid();
            else
                return $result;
        }
    
}