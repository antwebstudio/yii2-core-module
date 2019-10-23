<?php
namespace ant\category\migrations\db;

use common\components\Migration;
use ant\category\models\Category;
use ant\category\models\CategoryType;
use Yii;

class m180708080512_alter_category extends Migration
{
	public $tableName = '{{%category}}';
	
    public function safeUp()
    {
        $models = Category::find()->all();
        foreach ($models as $key => $model) {
            
            if (isset($model->icon_path)) {
                $this->insert('{{%file_attachment_group}}', [
                    'model' => get_class($model),
                    'model_id' => $model->id,
                    'type' => 'icon',
                ]);

                $arr = explode('.', $model->icon_path);
                $type = end($arr);
                $this->insert('{{%file_attachment}}', [
                    'group_id' => Yii::app()->db->getLastInsertId(),
                    'path' => $model->icon_path,
                    'base_url' => $model->icon_base_url,
                    'type' => $type,
                    'size' => 1,
                ]);
            }

            if (isset($model->thumbnail_path)) {
                $this->insert('{{%file_attachment_group}}', [
                    'model' => get_class($model),
                    'model_id' => $model->id,
                    'type' => 'category_thumbnail',
                ]);

                $arr = explode('.', $model->thumbnail_path);
                $type = end($arr);
                $this->insert('{{%file_attachment}}', [
                    'group_id' => Yii::app()->db->getLastInsertId(),
                    'path' => $model->thumbnail_path,
                    'base_url' => $model->thumbnail_base_url,
                    'type' => $type,
                    'size' => 1,
                ]);
            }
        }

        $this->dropColumn($this->tableName, 'icon_base_url');
        $this->dropColumn($this->tableName, 'thumbnail_base_url');
        $this->dropColumn($this->tableName, 'thumbnail_path');
        $this->dropColumn($this->tableName, 'icon_path');
    
    }

    public function safeDown()
    {
        $this->addColumn($this->tableName, 'icon_base_url', $this->string(1024));
		$this->addColumn($this->tableName, 'icon_path', $this->string(1024));
		$this->addColumn($this->tableName, 'thumbnail_base_url', $this->string(1024));
		$this->addColumn($this->tableName, 'thumbnail_path', $this->string(1024));
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170731_080512_alter_article_category cannot be reverted.\n";

        return false;
    }
    */
}
