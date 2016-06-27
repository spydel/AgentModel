<?php namespace yii\modelAgent;

use yii\base\Model;
use yii\db\ActiveRecord;

class FormModelAgent extends Model implements IEntitySubject
{
    /**
     * Все зарегистрированные объекты.
     *
     * @var IFormModel[]
     */
    private $_observers = [];

    /**
     * Основная модель
     *
     * @var ActiveRecord
     */
    private $_globalModel;

    /**
     * Содержит id основной модели.
     *
     * @var integer
     */
    private $_globalId;

    /**
     * Зависимое имя класса, на основе которого определяется основной id,
     * для связных моделей форм.
     *
     * @var string
     */
    private $_dependencyClassForm = '';

    /**
     * Регистрирует модель формы.
     *
     * @param IEntityObserver | IFormModel $obj
     */
    public function registerObserver(IEntityObserver $obj)
    {
        $this->_observers[] = $obj;
    }

    /**
     * Уведомляет всех зарегистрированные модели форм.
     *
     * @param IEntitySubject $subj
     */
    public function notifyObservers(IEntitySubject $subj)
    {
        /** @var $observer IEntityObserver | IFormModel **/
        foreach($this->_observers as $observer) {
            if ($this->getGlobalId()) {
                $observer->setDependencyId($this->getGlobalId());
            }
            $id = $observer->run($subj);
            $dependencyObj = $this->getDependencyClassForm();
            if ($observer instanceof $dependencyObj) {
                $this->setGlobalId($id);
            }
        }
    }

    /**
     * Проверяет валидацию, всех зарегистрированных моделей форм.
     *
     * @return bool
     */
    public function validateObservers()
    {
        /** @var $observer IEntityObserver | Model **/
        foreach($this->_observers as $observer) {
            if (!$observer->validate()) {
                $this->addErrors($observer->getErrors());
            }
        }

        return !$this->getErrors();
    }

    /**
     * Контрольная точка для запуска очереди объектов.
     * Содержит транзакцию БД, для целостности данных.
     *
     * @return bool
     * @throws AgentException
     */
    public function start()
    {
        $db = \Yii::$app->db->beginTransaction();
        try {
            $subj = clone $this;
            $this->notifyObservers($subj);
            $db->commit();
        } catch (AgentException $e) {
            $db->rollBack();
            return false;
        }

        return true;
    }

    /**
     * Установить глабальную модель.
     * Требуется для заполнения значениями моделей форм.
     * Для получения модели во вьюхах.
     *
     * @param Model $model
     */
    public function setGlobalModel(Model $model)
    {
        if (isset($model->id)) {
            $this->setGlobalId($model->id);
        }
        $this->_globalModel = $model;
    }

    /**
     * Получить глобальную модель.
     *
     * @return ActiveRecord
     */
    public function getGlobalModel()
    {
        return $this->_globalModel;
    }

    /**
     * Установить зависимый класс формы.
     *
     * @param IFormModel $obj
     */
    public function setDependencyClassForm(IFormModel $obj)
    {
        $this->_dependencyClassForm = get_class($obj);
    }

    /**
     * Получить зависимый класс формы.
     *
     * @return string
     */
    public function getDependencyClassForm()
    {
        return $this->_dependencyClassForm;
    }

    /**
     * Установить глобальный id.
     *
     * @param $id
     */
    public function setGlobalId($id)
    {
        $this->_globalId = $id;
    }

    /**
     * Получить глобальный id.
     *
     * @return int
     */
    public function getGlobalId()
    {
        return $this->_globalId;
    }

    public function isNew()
    {
        return $this->getGlobalId() === null;
    }

    /**
     * Установить значения модели формы, значениями из основной модели.
     *
     * @param FormModel $model
     * @return FormModel
     */
    public function writeAssocAttr(FormModel $model)
    {
        $assocAttr = $model->getAssocAttr();
        if ($assocAttr) {
            foreach ($assocAttr as $key => $attr) {
                if (is_numeric($key)) {
                    $model->{$attr} = $this->getGlobalModel()->getAttribute($attr);
                } elseif (is_callable($attr)) {
                    $model->{$key} = call_user_func($attr, $this->getGlobalModel());
                } else {
                    $model->{$key} = $this->getGlobalModel()->getAttribute($attr);
                }
            }
        }

        return $model;
    }
}