<?php
/**
 * 
 * Контейнер строки. Функции-обертки над стандартными.
 * 
 * Версии:
 *    2011-07-20 Устранил ошибку.
 * 
 * 
 */
namespace Alib;
class String
{
    /**
     * Строка.
     *
     * @var string
     */
	protected $_string = '';
	protected $_charset = 'UTF-8';
	
	public function __construct($string = '')
	{
	    $this->_result = $this->_string = (string)$string;
	}
	
	
	/**
	 * Установка обрабатываемой строки.
	 *
	 * @param tring $string
	 */
	public function setString($string)
	{
	    $this->_string = (string)$string;
	    return $this;
	}
	
	
	/**
	 * Равенство строк.
	 *
	 * @param string $str Приводится к типу string.
	 * @param  $min Минимальное число символов в строке.
	 * @return boolean
	 */
	public function equal($str, $min = 0)
	{
	    $str = (string)$str;
	    if ($min)
	    {
	        if ($this->lenlocal($str) >= $min and $this->_string === $str)
	           return true;
	    }
	    else 
	    {
	        if ($this->_string == $str)
	           return true;
	    }
	    return false;
	}
	
	/**
	 * Длина строки.
	 *
	 * @return integer
	 */
	public function len()
	{
	    return $this->_lenlocal($this->_string);
	}
	
	/**
	 * Для внутренних нужд.
	 *
	 * @param string $string
	 * @return integer
	 */
	protected function _lenlocal($string)
	{
	    return mb_strlen($string, $this->_charset);
	}
	
	/**
	 * Возвращает числовую позицию первого вхождения $string.
	 *
	 * @param string $string
	 * @param integer $offset
	 * @return mixed
	 */
	public function pos($string, $offset = 0)
	{
	    return mb_strpos($this->_string, $string, $offset, $this->_charset);
	}

	/**
	 * Возвращает числовую позицию последнего вхождения $string.
	 *
	 * @param string $string
	 * @param integer $offset
	 * @return mixed
	 */
	public function posr($string, $offset = null)
	{
	    return mb_strrpos($this->_string, $string, $offset, $this->_charset);
	}
	
	/**
	 * Возвращает часть строки haystack от первого вхождения needle до конца haystack.
     *
     * Если needle не найден, возвращает FALSE.
     * Если needle не строка, он конвертируется в integer и применяется как порядковое значение символа.
	 *
	 * @param unknown_type $string
	 * @return unknown
	 */
	public function str($string)
	{
	    return mb_strstr($this->_string, $string, null, $this->_charset);
	}

	/**
	 *  Метод str() без учёта регистра.
	 *
	 * @param unknown_type $string
	 * @return unknown
	 */
	public function stri($string)
	{
	    return mb_stristr($this->_string, $string, null, $this->_charset);
	}
	
	/**
	 * Возвращает часть строки.
	 * 
	 * Если start отрицательный, возвращаемая строка начинается со start'ового символа, считая от конца строки string.
	 * 
	 * Если length задан и положительный, возвращаемая строка будет содержать максимум length символов,
	 *  начиная со start (в зависимости от длины строки string. Если string меньше start, возвращается FALSE).
     *
     * Если length задан и негативный, то это количество символов будет пропущено,
     *  начиная с конца string (после вычисления стартовой позиции, когда start негативный). Если start задаёт позицию за пределами этого усечения, возвращается пустая строка.
	 *
	 * @param integer $start Если start отрицательный, возвращаемая строка начинается со start'ового символа, считая от конца строки string.
	 * @param integer $length
	 * @return string
	 */
	public function substr($start, $length = null)
	{
	    return mb_substr($this->_string, $start, $length, $this->_charset);
	}
	
	
	public function trim($charlist = '')
	{
        if($charlist == '') return trim($this->_string);
        return $this->triml($this->trimr($this->_string));
	}
	
	public function trimr($charlist = '')
	{
        if($charlist == '') return rtrim($this->_string);
        //quote charlist for use in a characterclass
        $charlist = preg_replace('!([\\\\\\-\\]\\[/])!', '\\\$1}', $charlist);
        return preg_replace('/[' . $charlist . ']+$/u','', $this->_string);

	}
	
	public function triml($charlist = '')
	{
        if($charlist == '') return ltrim($this->_string);
        //quote charlist for use in a characterclass
        $charlist = preg_replace('!([\\\\\\-\\]\\[/])!','\\\$1}',$charlist);
        return preg_replace('/^[' . $charlist . ']+/u', '', $this->_string);
	}
	
	
	public function tolower()
	{
	    return mb_strtolower($this->_string, $this->_charset);
	}
	public function toupper()
	{
	    return mb_strtoupper($this->_string, $this->_charset);
	}
	

	public function ucfirst()
	{
	    $string = $this->_string;
	    $string = mb_strtoupper(mb_substr($string, 0, 1, $this->_charset), $this->_charset).mb_substr($string, 1, mb_strlen($string), $this->_charset);
	    return $string;
	}
	
        
        
        public static function strtolower($string)
        {
            $object = new self($string);
            return $object->tolower();
        }
        
        public static function strpos($haystack, $string, $offset = 0)
        {
            $object = new self($haystack);
            return $object->pos($string, $offset);
        }


	
	
////////////////////////////////////////////////////////////////////
////////        Магические методы	

	// Печать строки - результата.
    public function __toString()
    {
    	return  $this->_string;
    }
	
	
}


