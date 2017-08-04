<?php

use yii\db\Migration;

class m170804_093210_extra_events extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%extra_events}}', [
            'id' => $this->primaryKey(),
            'event' => $this->string()->unique(),
            'value' => $this->smallInteger()->notNull()->defaultValue(0),

        ], $tableOptions);

        $this->insert('{{%extra_events}}', [
            'event' => 'stop_button',
            'value' => 0
        ]);
    }

    public function down()
    {
        $this->dropTable('{{%extra_events}}');
    }
}
