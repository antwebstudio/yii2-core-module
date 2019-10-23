<?php

use ant\helpers\ArrayHelper;
use ant\attribute\models\AttributeGroup;
use ant\attribute\models\Attribute;

class AttributeGroupCest
{
    public function _before(UnitTester $I)
    {
    }

    public function _after(UnitTester $I)
    {
    }

    public function testcreate(UnitTester $I)
    {
        $attributeGroup = $this->createGroup();

        $I->assertTrue($attributeGroup instanceof AttributeGroup);
    }

    // tests
    public function testGetDbAttributes(UnitTester $I)
    {
        $group = $this->createGroup();

        $this->createDbAttributes($group, [
            ['name' => 'attributeOne', 'setting' => 'attribute one setting'],
            ['name' => 'attributeTwo', 'setting' => 'attribute two setting'],
        ]);

        $groupId = $group->id;

        $I->assertEquals(
            Attribute::find()->joinWith('group g')->andWhere(['g.id' => $groupId])->all(),
            $group->getDbAttributes()->with('group')->all()
        );
    }

    public function testGetDbAttributesArray(UnitTester $I)
    {
        $group = $this->createGroup();

        $attributesArray = [
            'test' => 'test attribute setting',
            'otherAttribute' => 'other attribute setting',
        ];

        $dbAttributesConfig = [];
        foreach ($attributesArray as $key => $value) 
        {
            $dbAttributesConfig[] = ['name' => $key, 'setting' => $value];
        }

        $this->createDbAttributes($group, $dbAttributesConfig);

        $I->assertEquals($attributesArray, $group->dbAttributesArray);
    }

    public function testDbAttributes(UnitTester $I)
    {
        $group = $this->createGroup();

        $attributesArray = [
            'attribute1' => 'setting',
            'attribute2' => 'setting',
            'attribute3' => 'setting',
        ];

        $dbAttributesConfig = [];
        foreach ($attributesArray as $key => $value) 
        {
            $dbAttributesConfig[] = ['name' => $key, 'setting' => $value];
        }

        $this->createDbAttributes($group, $dbAttributesConfig);

        $I->assertEquals(array_keys($attributesArray), $group->dbAttributes());
    }

    protected function createGroup($attributes = [])
    {
        $model = new AttributeGroup;
        $model->attributes = $attributes;
        
        if (!$model->save()) throw new \Exception(Html::errorSummary($model));
        
        return $model;
    }

    protected function createDbAttributes($group, $dbAttributes)
    {
        foreach ($dbAttributes as $dbAttributeConfig) 
        {
            $dbAttribute = new Attribute;
            $dbAttribute->attributes = ArrayHelper::merge([
                'name' => 'attribute name',
                'setting' => 'attribtue setting',
            ], $dbAttributeConfig);
            
            if (!$dbAttribute->save()) throw new \Exception(Html::errorSummary($dbAttribute));

            $group->link('dbAttributes', $dbAttribute);
        }
    }
}
