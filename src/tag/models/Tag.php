<?php
namespace ant\tag\models;

use Yii;

/**
 * This is the model class for table "{{%tag}}".
 *
 * @property string $id
 */
class Tag extends \yii\db\ActiveRecord
{
    public $resume;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tag}}';
    }
	
    public function behaviors(){
        return [
        ];
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['model_class_id', 'frequency'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['model_class_id'], 'exist', 'skipOnError' => true, 'targetClass' => \ant\models\ModelClass::className(), 'targetAttribute' => 'id'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
        ];
    }
}
