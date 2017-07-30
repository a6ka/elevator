<?php

use yii\db\Migration;

class m170730_111425_buildings extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%buildings}}', [
            'id' => $this->primaryKey(),
            'floors' => $this->integer()->notNull(),
            'floor_height' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->insert('{{%buildings}}', [
            'id' => 1,
            'floors' => 5,
            'floor_height' => 4,
        ]);
    }

    public function down()
    {
        $this->dropTable('{{%buildings}}');
    }
}
