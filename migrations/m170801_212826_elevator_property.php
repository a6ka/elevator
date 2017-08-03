<?php

use yii\db\Migration;

class m170801_212826_elevator_property extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%elevator_property}}', [
            'id' => $this->primaryKey(),
            'elevator_name' => $this->string()->unique(),
            'currentHeight' => $this->float()->notNull(),
            'speed' => $this->float()->notNull(),
            'currentDirection' => $this->integer(),
            'persons_number' => $this->integer()->notNull(),
            'status_id' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createIndex('status_id', 'elevator_property', 'status_id');
        $this->addForeignKey('FK_elevator_status', 'elevator_property', 'status_id', 'elevator_statuses', 'id', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        $this->dropForeignKey('FK_elevator_status', 'elevator_property');
        $this->dropIndex('status_id', 'elevator_property');

        $this->dropTable('{{%elevator_property}}');
    }
}
