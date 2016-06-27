<?php namespace yii\modelAgent;

use yii\base\Model;

abstract class FormModel extends Model implements IFormModel, IEntityObserver
{
    private $_dependencyId;

    public function rules()
    {
        return [
            'dependencyIdPattern' => ['dependencyId', 'integer'],
        ];
    }

    public function getAssocAttr()
    {
        return $this->setAssocAttr();
    }

    public function setDependencyId($id)
    {
        $this->_dependencyId = $id;
    }

    public function getDependencyId()
    {
        return $this->_dependencyId;
    }
}