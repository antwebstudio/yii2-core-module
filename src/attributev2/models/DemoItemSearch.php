<?php 
namespace ant\attributev2\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
Use ant\attributev2\models\DemoItem;
use ant\behaviors\AttachBehaviorBehavior;

class DemoItemSearch extends DemoItem
{
	public $pageSize = 20;

    public function behaviors()
    {
        return [
            [
                'class' => AttachBehaviorBehavior::className(),
            ],
        ];
    }

    public function rules()
    {
        return
        [
            [['name'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = DemoItem::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' =>
            [
                'pageSize' => $this->pageSize,
            ],
        ]);

        $this->load($params);

        $query->andFilterWhere($this->attributeFilters());

        return $dataProvider;
    }

	protected function attributeFilters($exclude = [])
    {
        $attributeFilters = $this->attributes;

        foreach ($attributeFilters as $attribute => $value)
        {
            $attributeFilters[$attribute] = $this->$attribute;
        }

        return $attributeFilters;
    }
}