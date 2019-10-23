<?php
namespace ant\dynamicform\models\query;

class DynamicFieldQuery extends \yii\db\ActiveQuery {
	public function notTrashed() {
        return $this->andWhere(['is_deleted' => false]);
	}
}