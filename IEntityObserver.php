<?php namespace yii\modelAgent;

interface IEntityObserver
{
    /**
     * Запустить обработку модели формы.
     *
     * @param IEntitySubject $obj
     * @return integer | bool
     */
    public function run(IEntitySubject $obj);
}