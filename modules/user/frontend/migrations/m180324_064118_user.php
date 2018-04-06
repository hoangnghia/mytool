<?php

use yii\db\Migration;

/**
 * Class m180324_064118_user
 */
class m180324_064118_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180324_064118_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180324_064118_user cannot be reverted.\n";

        return false;
    }
    */
}
