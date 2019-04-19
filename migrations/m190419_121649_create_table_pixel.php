<?php

use yii\db\Migration;

/**
 * Class m190419_121649_create_table_pixel
 */
class m190419_121649_create_table_pixel extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('pixel', [
            'id' => $this->primaryKey()->unsigned(),
            'row' => $this->smallInteger()->notNull(),
            'col' => $this->smallInteger()->notNull(),
            'color_red'   => $this->smallInteger()->notNull()->defaultValue(0),
            'color_green' => $this->smallInteger()->notNull()->defaultValue(0),
            'color_blue' => $this->smallInteger()->notNull()->defaultValue(0),
            'opacity' => $this->decimal(4, 2)->notNull()->defaultValue(1),
            'updated_at' => $this->integer()
        ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('pixel');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190419_121649_create_table_pixels cannot be reverted.\n";

        return false;
    }
    */
}
