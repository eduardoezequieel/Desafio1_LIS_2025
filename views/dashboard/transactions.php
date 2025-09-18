<?php
use App\Helpers\DashboardPage;

require_once __DIR__ . '/../../app/helpers/DashboardPage.php';

DashboardPage::getSidebarTemplate('Transacciones', [
    '/Desafio1_LIS_2025/resources/css/transactions.css'
]);
DashboardPage::getHeaderTemplate('Transacciones', [
    [
        'class' => 'btn-success',
        'text' => 'Nueva Entrada',
        'id' => 'new-income-btn',
    ],
    [
        'class' => 'btn-danger',
        'text' => 'Nuevo Gasto',
        'id' => 'new-expense-btn',
    ]
]);
?>   
    <main class="content">
        <div class="filter-section mb-3">
            <div class="btn-group" role="group" aria-label="Transaction filters">
                <button type="button" class="btn btn-outline-primary active" id="filter-all">Todas</button>
                <button type="button" class="btn btn-outline-success" id="filter-income">Entradas</button>
                <button type="button" class="btn btn-outline-danger" id="filter-expense">Gastos</button>
            </div>
        </div>
        
        <div class="h-100 d-flex align-items-center justify-content-center">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Tipo</th>
                        <th scope="col">Monto</th>
                        <th scope="col">Categoría</th>
                        <th scope="col">Fecha</th>
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody id="transactions-table-body">
                    
                </tbody>
            </table>
        </div>
    </main>

    <!-- Income Modal -->
    <div class="modal fade" id="incomeModal" tabindex="-1" aria-labelledby="income-modal-label" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="income-modal-label">Registrar entrada</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="income-form" method="POST" class="d-flex flex-column gap-2">
                        <input type="hidden" name="id" id="income-id">
                        <div class="form-group">
                            <label for="income-category">Categoría</label>
                            <select name="category_id" id="income-category" class="form-select" required>
                                
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="income-amount">Monto</label>
                            <input type="number" step="0.01" class="form-control" id="income-amount" name="amount" required>
                        </div>
                        <div class="form-group">
                            <label for="income-date">Fecha</label>
                            <input type="date" class="form-control" id="income-date" name="date" required>
                        </div>
                        <div class="form-group">
                            <label for="income-description">Descripción (opcional)</label>
                            <textarea class="form-control" id="income-description" name="description" rows="2"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="income-invoice">Factura (foto)</label>
                            <div id="income-invoice-preview" class="invoice-preview">
                                <span>Haz clic para subir o arrastra para subir una imagen</span>
                            </div>
                            <input type="file" class="form-control d-none" id="income-invoice" name="invoiceImage" accept="image/*">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button id="save-income-btn" type="button" class="btn btn-success">Guardar entrada</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Expense Modal -->
    <div class="modal fade" id="expenseModal" tabindex="-1" aria-labelledby="expense-modal-label" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="expense-modal-label">Registrar gasto</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="expense-form" method="POST" class="d-flex flex-column gap-2">
                        <input type="hidden" name="id" id="expense-id">
                        <div class="form-group">
                            <label for="expense-category">Categoría</label>
                            <select name="category_id" id="expense-category" class="form-select" required>
                                
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="expense-amount">Monto</label>
                            <input type="number" step="0.01" class="form-control" id="expense-amount" name="amount" required>
                        </div>
                        <div class="form-group">
                            <label for="expense-date">Fecha</label>
                            <input type="date" class="form-control" id="expense-date" name="date" required>
                        </div>
                        <div class="form-group">
                            <label for="expense-description">Descripción (opcional)</label>
                            <textarea class="form-control" id="expense-description" name="description" rows="2"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="expense-invoice">Factura (foto)</label>
                            <div id="expense-invoice-preview" class="invoice-preview">
                                <span>Haz clic para subir o arrastra para subir una imagen</span>
                            </div>
                            <input type="file" class="form-control d-none" id="expense-invoice" name="invoiceImage" accept="image/*">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button id="save-expense-btn" type="button" class="btn btn-danger">Guardar gasto</button>
                </div>
            </div>
        </div>
    </div>

    <?php
    DashboardPage::getFooterTemplate([
        '/Desafio1_LIS_2025/resources/js/dashboard/transactions.js'
    ]);
?>   
</body>
</html>
