<?php

namespace ant\attributev2\models;

use Yii;
use ant\attributev2\models\Attributev2;

/**
 * This is the model class for table "{{%attributev2_value}}".
 *
 * @property string $id
 * @property string $attributev2_id
 * @property string $value
 *
 * @property Attributev2 $attributev2
 */
class Attributev2Value extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%attributev2_value}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['attributev2_id'], 'integer'],
            [['value'], 'string'],
            [['attributev2_id'], 'exist', 'skipOnError' => true, 'targetClass' => Attributev2::className(), 'targetAttribute' => ['attributev2_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'attributev2_id' => 'Attributev2 ID',
            'value' => 'Value',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAttributev2()
    {
        return $this->hasOne(Attributev2::className(), ['id' => 'attributev2_id']);
    }

    public function __toString()
    {
        return $this->toString();
    }

    public function toString()
    {
        return (string) $this->value;
    }
}
