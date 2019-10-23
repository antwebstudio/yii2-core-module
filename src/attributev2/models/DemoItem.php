<?php 
namespace ant\attributev2\models;

use Yii;
use yii\db\ActiveRecord;
use ant\attributev2\traits\DynamicAttributeTrait;
use ant\behaviors\AttachBehaviorBehavior;
use ant\behaviors\DateTimeAttributeBehavior;

class DemoItem extends ActiveRecord
{
	use DynamicAttributeTrait;
	
	protected $_dateFormat = 'Y-m-d';

	public function behaviors()
	{
		return [
			[
                'class' => AttachBehaviorBehavior::className(),
                'config' => '@common/config/behaviors.php',
            ],
			'datetimeAttribute' => [
				'class' => DateTimeAttributeBehavior::className(),
				'attributes' => ['expired_date'],
				'format' => $this->_dateFormat,
			],
		];
	}

	public static function tableName()
	{
		return '{{%attributev2_demoitem}}';
	}

	public function _rules()
	{
		return [
			[['name', 'price'], 'required'],
			[['name'], 'string'],
			[['description'], 'string'],
			[['price'], 'number'],
			[['expired_date'], 'date', 'format' => "php:{$this->_dateFormat}"],
		];
	}
}