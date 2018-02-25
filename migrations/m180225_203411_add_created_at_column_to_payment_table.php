<?php

use yii\db\Migration;

/**
 * Handles adding created_at to table `payment`.
 */
class m180225_203411_add_created_at_column_to_payment_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('payment', 'created_at', $this->timestamp());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('payment', 'created_at');
    }
}
