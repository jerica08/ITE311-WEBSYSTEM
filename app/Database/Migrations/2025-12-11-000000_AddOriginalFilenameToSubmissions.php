<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddOriginalFilenameToSubmissions extends Migration
{
    public function up()
    {
        $this->forge->addColumn('submissions', [
            'original_filename' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'attachment'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('submissions', 'original_filename');
    }
}
