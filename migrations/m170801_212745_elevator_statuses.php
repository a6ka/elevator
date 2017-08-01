<?php

use yii\db\Migration;

class m170801_212745_elevator_statuses extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%elevator_statuses}}', [
            'id' => $this->primaryKey(),
            'status' => $this->string(50)->notNull()->unique(),
        ], $tableOptions);

        $this->batchInsert('elevator_statuses', ['status'], [
            ['Waiting'],
            ['Move Up'],
            ['Move Down'],
            ['Loading'],
            ['Stop'],
        ]);
    }

    public function down()
    {
        $this->dropTable('{{%elevator_statuses}}');
    }
}
