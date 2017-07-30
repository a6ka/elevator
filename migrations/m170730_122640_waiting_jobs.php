<?php

use yii\db\Migration;

class m170730_122640_waiting_jobs extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%waiting_jobs}}', [
            'id' => $this->primaryKey(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'floor' => $this->integer()->notNull(),
            'direction' => $this->integer(),

        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%waiting_jobs}}');
    }
}
