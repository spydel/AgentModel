<?php namespace yii\modelAgent;

interface IEntitySubject
{
    /**
     * Регистрирует модель формы.
     *
     * @param IEntityObserver | IFormModel $obj
     */
    public function registerObserver(IEntityObserver $obj);

    /**
     * Уведомляет всех зарегистрированные модели форм.
     *
     * @param \common\components\many_models\IEntitySubject $subj
     */
    public function notifyObservers(IEntitySubject $subj);
}