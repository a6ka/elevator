<?php

use yii\db\Migration;

class m170801_212808_tasks extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%tasks}}', [
            'id' => $this->primaryKey(),
            'start_floor' => $this->integer()->notNull(),
            'end_floor' => $this->integer()->notNull(),
            'direction' => $this->smallInteger()->notNull()->defaultValue(0),
            'status_id' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createIndex('status_id', 'tasks', 'status_id');
        $this->addForeignKey('FK_task_status', 'tasks', 'status_id', 'task_statuses', 'id', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        $this->dropForeignKey('FK_task_status', 'tasks');
        $this->dropIndex('status_id', 'tasks');

        $this->dropTable('{{%tasks}}');
    }
}
