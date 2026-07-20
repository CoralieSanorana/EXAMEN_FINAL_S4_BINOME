<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddReferenceGroupeToHistoriqueTransactions extends Migration
{
    public function up()
    {
        $this->forge->addColumn('historique_transactions', [
            'reference_groupe' => [
                'type'       => 'TEXT',
                'null'       => true,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('historique_transactions', 'reference_groupe');
    }
}
