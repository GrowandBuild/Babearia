<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfissionalController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ServicoController;
use App\Http\Controllers\AgendamentoController;
use App\Http\Controllers\FinanceiroController;
use App\Http\Controllers\FormaPagamentoController;
use App\Http\Controllers\BackupController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect()->route('login');
});

// Rota para servir avatares
Route::get('/storage/avatars/{filename}', function ($filename) {
    $path = storage_path('app/public/avatars/' . $filename);
    
    if (!file_exists($path)) {
        abort(404);
    }
    
    return response()->file($path);
});

// Rota de teste
Route::get('/teste', function () {
    return 'Laravel funcionando! Usuário admin existe: ' . (App\Models\User::where('email', 'admin@esmalteria.com')->exists() ? 'SIM' : 'NÃO');
});


Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Financeiro (apenas proprietária)
    Route::get('/financeiro', [FinanceiroController::class, 'index'])->name('financeiro')->middleware(['can:isProprietaria', 'financial.security']);

           // Backup (apenas proprietária)
           Route::get('/backup', [BackupController::class, 'index'])->name('backup.index')->middleware(['can:isProprietaria', 'financial.security']);
           Route::post('/backup/create', [BackupController::class, 'create'])->name('backup.create')->middleware(['can:isProprietaria', 'financial.security']);
           Route::get('/backup/download', [BackupController::class, 'download'])->name('backup.download')->middleware(['can:isProprietaria', 'financial.security']);
           Route::get('/backup/status', [BackupController::class, 'status'])->name('backup.status')->middleware(['can:isProprietaria', 'financial.security']);
           Route::post('/backup/reset', [BackupController::class, 'resetDatabase'])->name('backup.reset')->middleware(['can:isProprietaria', 'financial.security']);

    // Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Agendamentos (todos podem acessar)
    Route::get('/agenda', [AgendamentoController::class, 'agenda'])->name('agendamentos.agenda');
    Route::get('/agendar', [AgendamentoController::class, 'autoAgendar'])->name('agendamentos.auto-agendar');
    Route::post('/agendar', [AgendamentoController::class, 'storeAutoAgendamento'])->name('agendamentos.store-auto');
    Route::post('/api/horarios-disponiveis', [AgendamentoController::class, 'horariosDisponiveis'])->name('api.horarios-disponiveis');
    Route::post('/api/horarios-disponiveis-dia', [AgendamentoController::class, 'horariosDisponiveisDia'])->name('api.horarios-disponiveis-dia');
    Route::resource('agendamentos', AgendamentoController::class)->except(['destroy']);
    Route::get('/agendamentos/{agendamento}/concluir', [AgendamentoController::class, 'concluir'])->name('agendamentos.concluir');
    Route::post('/agendamentos/{agendamento}/finalizar', [AgendamentoController::class, 'finalizarPagamento'])->name('agendamentos.finalizar');
    Route::get('/agendamentos/{agendamento}/finalizar', [AgendamentoController::class, 'mostrarFinalizar'])->name('agendamentos.mostrar-finalizar');
    Route::get('/agendamentos/{agendamento}/faturar', [AgendamentoController::class, 'faturamentoRapido'])->name('agendamentos.faturar');
    Route::post('/agendamentos/{agendamento}/finalizar-pagamento', [AgendamentoController::class, 'finalizarPagamentoRapido'])->name('agendamentos.finalizar-pagamento');
    Route::post('/agendamentos/{agendamento}/confirmar', [AgendamentoController::class, 'confirmarConclusao'])->name('agendamentos.confirmar')->middleware(['can:isProprietaria', 'financial.security']);
    Route::post('/agendamentos/{agendamento}/cancelar', [AgendamentoController::class, 'cancelar'])->name('agendamentos.cancelar');
    Route::delete('/agendamentos/{agendamento}/deletar', [AgendamentoController::class, 'deletarCompletamente'])->name('agendamentos.deletar')->middleware(['can:isProprietaria', 'financial.security']);

    // Clientes (todos podem ver e criar - para facilitar cadastro rápido)
    Route::resource('clientes', ClienteController::class)->only(['index', 'create', 'store', 'show']);
    
    // Rotas exclusivas para Proprietária
    Route::middleware('can:isProprietaria')->group(function () {
        Route::resource('profissionais', ProfissionalController::class)->parameters(['profissionais' => 'profissional']);
        Route::post('/profissionais/{profissional}/toggle', [ProfissionalController::class, 'toggleStatus'])->name('profissionais.toggle');
        
        // Clientes - edição e exclusão (apenas proprietária)
        Route::get('/clientes/{cliente}/edit', [ClienteController::class, 'edit'])->name('clientes.edit');
        Route::put('/clientes/{cliente}', [ClienteController::class, 'update'])->name('clientes.update');
        Route::delete('/clientes/{cliente}', [ClienteController::class, 'destroy'])->name('clientes.destroy');
        Route::get('/clientes/{cliente}/create-access', [ClienteController::class, 'createAccess'])->name('clientes.create-access');
        Route::post('/clientes/{cliente}/store-access', [ClienteController::class, 'storeAccess'])->name('clientes.store-access');
        Route::get('/clientes/{cliente}/edit-access', [ClienteController::class, 'editAccess'])->name('clientes.edit-access');
        Route::put('/clientes/{cliente}/update-access', [ClienteController::class, 'updateAccess'])->name('clientes.update-access');
        Route::post('/clientes/{cliente}/update-avatar', [ClienteController::class, 'updateAvatar'])->name('clientes.update-avatar');
        
        Route::resource('servicos', ServicoController::class);
        Route::post('/servicos/{servico}/toggle', [ServicoController::class, 'toggleStatus'])->name('servicos.toggle');

        // Dashboard de Agenda (admin) - visão tipo "colunas por profissional"
        Route::get('/admin/agenda', [App\Http\Controllers\AdminAgendaController::class, 'index'])->name('admin.agenda');
        Route::get('/admin/agenda/events', [App\Http\Controllers\AdminAgendaController::class, 'events'])->name('admin.agenda.events');
        Route::get('/admin/agenda/finalizar/{id}', [App\Http\Controllers\AdminAgendaController::class, 'mostrarFinalizar'])->name('admin.agenda.mostrar-finalizar');
        Route::post('/admin/agenda/finalizar-atendimento', [App\Http\Controllers\AdminAgendaController::class, 'finalizarAtendimento'])->name('admin.agenda.finalizar');
        
        Route::get('/formas-pagamento', [FormaPagamentoController::class, 'index'])->name('formas-pagamento.index');
        Route::get('/formas-pagamento/create', [FormaPagamentoController::class, 'create'])->name('formas-pagamento.create');
        Route::post('/formas-pagamento', [FormaPagamentoController::class, 'store'])->name('formas-pagamento.store');
        Route::get('/formas-pagamento/{formaPagamento}/edit', [FormaPagamentoController::class, 'edit'])->name('formas-pagamento.edit');
        Route::put('/formas-pagamento/{formaPagamento}', [FormaPagamentoController::class, 'update'])->name('formas-pagamento.update');
        Route::delete('/formas-pagamento/{formaPagamento}', [FormaPagamentoController::class, 'destroy'])->name('formas-pagamento.destroy');
        Route::post('/formas-pagamento/{formaPagamento}/toggle', [FormaPagamentoController::class, 'toggleStatus'])->name('formas-pagamento.toggle');
    });
});

require __DIR__.'/auth.php';
