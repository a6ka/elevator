<?php

use yii\db\Migration;

class m170801_212757_task_statuses extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%task_statuses}}', [
            'id' => $this->primaryKey(),
            'status' => $this->string(50)->notNull()->unique(),
        ], $tableOptions);

        $this->batchInsert('task_statuses', ['status'], [
            ['Waiting'],
            ['In queue'],
            ['Current'],
            ['Done'],
        ]);
    }

    public function down()
    {
        $this->dropTable('{{%task_statuses}}');
    }
}
