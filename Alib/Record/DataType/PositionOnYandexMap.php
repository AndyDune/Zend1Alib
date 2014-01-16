<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dune
 * Date: 03.10.12
 * Time: 13:52
 *
 * Указатель на карте Яндекс.
 *
 */
namespace Alib\Record\DataType;
use Alib\Record;
class PositionOnYandexMap extends Record\DataType
{

    const BAD_DATA = 'BAD_DATA_YANDEX';
    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $_messageTemplates = array(
        self::BAD_DATA    => "Неверные данные.",
    );


    public function _processReady()
    {
        $result = true;


        return $result;
    }

}