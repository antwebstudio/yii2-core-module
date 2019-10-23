<?php
namespace ant\support\migrations\db;

use yii\db\Migration;

Class m170730_040654_create_contact_form extends Migration
{
	protected $tableName = '{{%contact_form}}';
    public function safeUp()
    {
		$tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable($this->tableName, [
            'id' => $this->primaryKey()->unsigned(),
            'name' => $this->string(), // setia, ctstabil
            'title' => $this->string()->defaultValue(NULL), // setia
            'age' => $this->integer(3)->defaultValue(NULL), // setia
			'address' => $this->string(200)->defaultValue(NULL),
            'mobile' => $this->string(20)->defaultValue(NULL), // setia, ctstabil
            'email' => $this->string(200), // setia, ctstabil
			'state' => $this->string(100)->defaultValue(NULL), // setia
            'country' => $this->string(3)->defaultValue(NULL), // setia
			'message' => $this->text()->defaultValue(NULL), // ctstabil
			'data' => $this->text()->defaultValue(NULL),
			'created_ip' => $this->string(45)->notNull(),
            'created_at' => $this->timestamp()->defaultValue(NULL),
            'updated_at' => $this->timestamp()->defaultValue(NULL),
        ], $tableOptions);
    }

    public function safeDown()
   {        $this->dropTable($this->tableName);
   }    /*
    // Use up()/down() to run migration code without a transaction.
   public function up()    {
    }

    public function down()
	{       echo "m170730_040654_create_contact_form cannot be reverted.\n";
       return false;    }
    */
}