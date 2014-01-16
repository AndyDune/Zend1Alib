<?php

/**
 * Интерфейс для классов, объекты которых передаются хелперу вода для использования как декоратор.
 * 
 * Версии:
 *   2011-09-01 Создан. Идея декораторов хорошо себя показала. Добавлено больше контроля.
 */
namespace Alib\View\Helper\InterfaceClass;
interface TableRowDecorator
{
    /**
     * Передать значение, которое необходимо обработать
     */
    public function setValue($value);
    
    /**
     * Передача всего рассматриваемого массива.
     */
    public function setData($data);
    
    /**
     * Выбрать результат.
     */
    public function get();
    
}
