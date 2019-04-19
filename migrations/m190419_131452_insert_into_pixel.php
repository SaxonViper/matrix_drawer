<?php

use yii\db\Migration;

/**
 * Class m190419_131452_insert_into_pixel
 */
class m190419_131452_insert_into_pixel extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->db->createCommand("
            TRUNCATE TABLE pixel;
        ")->execute();

        $values = [];
        for ($row = 1; $row <= 30; $row++) {
            for ($col = 1; $col <= 30; $col++) {
                $values[] = "($row, $col)";
            }
        }
        $values = implode(', ', $values);
        $this->db->createCommand("INSERT INTO pixel (row, col) VALUES {$values}")->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190419_131452_insert_into_pixel cannot be reverted.\n";

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190419_131452_insert_into_pixel cannot be reverted.\n";

        return false;
    }
    */
}
