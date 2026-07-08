<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\VendaController;
use App\Http\Controllers\PagamentoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StockEntryController;
use App\Http\Controllers\StockMovementController;
use App\Livewire\Invoices\CreateInvoice;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\FornecedorController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;

// Rotas para visitantes (Não logados)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Define o Dashboard como a página inicial após o login
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');



Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/dashboard', [DashboardController::class, 'index']);

Route::get('/produtos', [ProdutoController::class, 'index'])->name('products.index');
Route::get('/produtos/criar', [ProdutoController::class, 'create'])->name('products.create');
Route::post('/produtos', [ProdutoController::class, 'store'])->name('products.store');
Route::get('/produtos/{id}/editar', [ProdutoController::class, 'edit'])->name('products.edit');
Route::put('/produtos/{id}', [ProdutoController::class, 'update'])->name('products.update');



Route::get('/clientes', [ClienteController::class, 'index'])->name('customers.index');
Route::get('/clientes/criar', [ClienteController::class, 'create'])->name('customers.create');
Route::post('/clientes', [ClienteController::class, 'store'])->name('customers.store');
Route::get('/clientes/{id}', [ClienteController::class, 'show'])->name('customers.show');
Route::post('/clientes/divida', [ClienteController::class, 'addDebt'])->name('customers.add-debt');
Route::post('/clientes/receber', [ClienteController::class, 'receivePayment'])->name('customers.receive-payment');
Route::delete('/clientes/{cliente}', [ClienteController::class, 'destroy'])->name('customers.destroy');
// Ver extrato na tela
Route::get('/clientes/{id}/extrato', [ClienteController::class, 'extract'])->name('customers.extract');
// Gerar o PDF do extrato
Route::get('/clientes/{id}/extrato/pdf', [ClienteController::class, 'extractPdf'])->name('customers.extractPdf');



Route::get('/faturas', [VendaController::class, 'index'])->name('invoices.index');
Route::get('/faturas/nova', [VendaController::class, 'create'])->name('invoices.create');
Route::post('/faturas/adicionar-item', [VendaController::class, 'addItem'])->name('invoices.addItem');
Route::get('/faturas/remover-item/{index}', [VendaController::class, 'removeItem'])->name('invoices.removeItem');
Route::post('/faturas/finalizar', [VendaController::class, 'store'])->name('invoices.store');
Route::get('/faturas/{id}', [VendaController::class, 'show'])->name('invoices.show');
Route::get('/faturas/{id}/imprimir',[VendaController::class, 'imprimir'])->name('invoices.print');
Route::get('/faturas/{id}/talao',[VendaController::class, 'talao'])->name('invoices.talao');

// Route::get('/vendas/nova', CreateInvoice::class)->name('invoices.create');


Route::get('/recebimentos', [PagamentoController::class, 'index'])->name('receipts.index');
Route::get('/recebimentos/pagar/{invoice_id}', [PagamentoController::class, 'create'])->name('receipts.create');
Route::post('/recebimentos', [PagamentoController::class, 'store'])->name('receipts.store');

Route::delete('/receipts/{id}', [PagamentoController::class, 'destroy'])->name('receipts.destroy');



Route::get('/compras', [StockEntryController::class, 'index'])->name('stock.entries.index');
Route::get('/compras/nova', [StockEntryController::class, 'create'])->name('stock.entries.create');
Route::post('/compras/adicionar', [StockEntryController::class, 'addItem'])->name('stock.entries.addItem');
Route::get('/compras/remover/{index}', [StockEntryController::class, 'removeItem'])->name('stock.entries.removeItem');
Route::post('/compras/finalizar', [StockEntryController::class, 'store'])->name('stock.entries.store');



Route::get('/stock/movimentos', [StockMovementController::class, 'index'])->name('stock.movements.index');
Route::post('/stock/movimentos/ajuste', [StockMovementController::class, 'ajuste'])->name('stock.movements.ajuste');



Route::get('/relatorios/lucro', [ReportController::class, 'profit'])->name('reports.profit');



Route::get('/categorias', [CategoriaController::class, 'index'])->name('categories.index');
Route::post('/categorias', [CategoriaController::class, 'store'])->name('categories.store');
Route::put('/categorias/{id}', [CategoriaController::class, 'update'])->name('categories.update');
Route::delete('/categorias/{id}', [CategoriaController::class, 'destroy'])->name('categories.destroy');


Route::get('/fornecedores', [FornecedorController::class, 'index'])->name('suppliers.index');
Route::post('/fornecedores', [FornecedorController::class, 'store'])->name('suppliers.store');
Route::get('/fornecedores/{id}', [FornecedorController::class, 'show'])->name('suppliers.show');
Route::put('/fornecedores/{id}', [FornecedorController::class, 'update'])->name('suppliers.update');
Route::delete('/fornecedores/{id}', [FornecedorController::class, 'destroy'])->name('suppliers.destroy');
Route::post('/fornecedores/pagar', [FornecedorController::class, 'liquidar'])->name('suppliers.pay');
Route::post('/fornecedores/addDivida', [FornecedorController::class, 'addDivida'])->name('suppliers.add-debt');


// Gestão de Múltiplos Carrinhos
Route::post('/venda/carrinho/novo', [VendaController::class, 'novoCarrinho'])->name('invoices.newCart');
Route::get('/venda/carrinho/trocar/{id}', [VendaController::class, 'trocarCarrinho'])->name('invoices.switchCart');
Route::get('/venda/carrinho/remover/{id}', [VendaController::class, 'removerMesa'])->name('invoices.deleteCart');

Route::patch('/venda/update-item/{index}', [VendaController::class, 'updateItem'])->name('invoices.updateItem');



Route::middleware(['auth'])->group(function () {
    // Apenas admins podem gerir utilizadores
    Route::get('/utilizadores', [UserController::class, 'index'])->name('users.index');
    Route::get('/utilizadores/criar', [UserController::class, 'create'])->name('users.create');
    Route::post('/utilizadores', [UserController::class, 'store'])->name('users.store');
});

// Rota para a Prateleira (Apenas produtos avulsos/filhos)
Route::get('/prateleira', [ProdutoController::class, 'shelf'])->name('products.shelf');
// Rota para abrir uma caixa manualmente
Route::post('/prateleira/abrir-caixa/{id}', [ProdutoController::class, 'openBox'])->name('products.openBox');
// Criar novo produto avulso a partir desta tela
Route::post('/prateleira/novo-avulso', [ProdutoController::class, 'storeShelfProduct'])->name('products.storeShelf');
Route::post('/prateleira/add/{id}', [ProdutoController::class, 'addProductShelf'])->name('products.addShelf');


// Remover produto da prateleira (excluir o vínculo avulso)
Route::delete('/prateleira/{id}', [ProdutoController::class, 'destroyShelfProduct'])->name('products.destroyShelf');
});
