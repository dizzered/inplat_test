<?php

use yii\db\Migration;

/**
 * Class m180323_122643_create_db
 */
class m180323_122643_create_db extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->createTable('user', [
		    'id' => $this->primaryKey(11),
		    'username' => $this->string(50),
		    'password' => $this->string(100),
		    'role_id' => $this->integer(11)->defaultValue(3)
	    ]);
	    
	    $this->batchInsert('user', ['username', 'password', 'role_id'], [
		    ['guest', Yii::$app->security->generatePasswordHash('guest'), 3],
		    ['moderator', Yii::$app->security->generatePasswordHash('moderator'), 2],
		    ['editor', Yii::$app->security->generatePasswordHash('editor'), 1]
	    ]);
	    
	    $this->createTable('product', [
		    'id' => $this->primaryKey(11),
		    'name' => $this->string(100)->null(),
		    'price' => $this->string(10)->null(),
	    ]);
	    
	    $this->createTable('revision', [
		    'id' => $this->primaryKey(11),
		    'product_id' => $this->integer(11)->notNull(),
		    'attr' => $this->string(50)->notNull(),
		    'new_value' => $this->string(100)->null(),
	    ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('user');

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180323_122643_create_db cannot be reverted.\n";

        return false;
    }
    */
}
