<?php namespace yii\modelAgent;

interface IFormModel
{
    /**
     * Установить зависимый id. Нужен для связных моделей форм.
     *
     * @param $id
     */
    public function setDependencyId($id);

    /**
     * Получить зависимый id.
     */
    public function getDependencyId();

    /**
     * Соответствия значений модели формы со значениями глобальной формы.
     *
     * @return FormModel
     */
    public function setAssocAttr();

    /**
     * Получить соответствия значений модели формы.
     *
     * @return array
     */
    public function getAssocAttr();
}