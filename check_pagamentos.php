<?php
require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

echo "=== VERIFICANDO PAGAMENTOS EXISTENTES ===\n";

$pagamentos = DB::table('pagamentos')->limit(3)->select('agendamento_id', 'valor', 'valor_liquido')->get();

foreach($pagamentos as $p) {
    echo "ID: {$p->agendamento_id} | valor: {$p->valor} | valor_liquido: " . ($p->valor_liquido ?? 'NULL') . "\n";
}
