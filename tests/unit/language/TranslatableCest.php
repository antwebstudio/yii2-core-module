<?php

class TranslatableCest
{
    public function _before(UnitTester $I)
    {
    }

    // tests
    public function test(UnitTester $I)
    {
		$name = 'Test Name';
		
		$model = new TranslatableCestTestModel;
		$model->name = $name;
		if (!$model->save()) throw new \Exception(print_r($model->errors, 1));
		
		$model->refresh();
		
		$I->assertEquals($name, $model->name);
    }
}

class TranslatableCestTestModel extends \yii\db\ActiveRecord {
	public static function tableName() {
		return '{{%test}}';
	}
	
	public function behaviors() {
		return [
			[
				'class' => \ant\language\behaviors\Translatable::class,
				'translateAttributes' => [
					'name'
				],
				'currentLanguage' => 'zh-MY',
			],
		];
	}
}