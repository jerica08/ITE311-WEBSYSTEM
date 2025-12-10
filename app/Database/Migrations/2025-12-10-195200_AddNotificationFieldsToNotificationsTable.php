<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddNotificationFieldsToNotificationsTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('notifications', [
            'title' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'user_id',
            ],
            'type' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'after' => 'message',
            ],
            'related_id' => [
                'type' => 'INT',
                'null' => true,
                'after' => 'type',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('notifications', 'title');
        $this->forge->dropColumn('notifications', 'type');
        $this->forge->dropColumn('notifications', 'related_id');
    }
}
