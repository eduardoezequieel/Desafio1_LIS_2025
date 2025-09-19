<?php
/**
 * Vista específica de Salidas (gastos).
 * Nota: Sistema ahora cuenta con vista unificada (transactions.php).
 * Esta se mantiene por compatibilidad / navegación previa.
 */
use App\Helpers\DashboardPage;

require_once __DIR__ . '/../../app/helpers/DashboardPage.php';

DashboardPage::getSidebarTemplate('Salidas', [
    '/Desafio1_LIS_2025/resources/css/incomes.css'
]);
DashboardPage::getHeaderTemplate('Salidas', [
    [
        'class' => 'btn-success',
        'text' => 'Registrar salida',
        'id' => 'new-expense-btn',
    ]
]);
?>   
    <main class="content">
        <div class="h-100 d-flex align-items-center justify-content-center">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Monto</th>
                    <th scope="col">Categoría</th>
                    <th scope="col">Fecha</th>
                    <th scope="col">Acciones</th>
                </tr>
            </thead>
            <tbody id="expenses-table-body">
                
            </tbody>
        </table>
        </div>
    </main>
    <div class="modal fade" id="expenseModal" tabindex="-1" aria-labelledby="expense-modal-label" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="expense-modal-label">Registrar salida</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="expense-form" method="POST" class="d-flex flex-column gap-2">
                        <input type="hidden" name="id" id="expense-id">
                        <div class="form-group">
                            <label for="expense-type">Categoría</label>
                            <select name="category_id" id="expense-type" class="form-select" required>
                                
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="amount">Monto</label>
                            <input type="number" class="form-control" id="amount" name="amount" required>
                        </div>
                        <div class="form-group">
                            <label for="date">Fecha</label>
                            <input type="date" class="form-control" id="date" name="date" required>
                        </div>
                        <div class="form-group">
                            <label for="description">Descripción (opcional)</label>
                            <textarea class="form-control" id="description" name="description" rows="2"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="invoiceImage">Factura (foto)</label>
                            <div id="invoice-preview-file-container" class="invoice-preview">
                                <span>Haz clic para subir o arrastra para subir una imagen</span>
                            </div>
                            <input type="file" class="form-control d-none" id="invoiceImage" name="invoiceImage" accept="image/*">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button id="save-expense-btn" type="button" class="btn btn-primary">Guardar cambios</button>
                </div>
            </div>
        </div>
    </div>
    <?php
    DashboardPage::getFooterTemplate([
        '/Desafio1_LIS_2025/resources/js/dashboard/expenses.js'
    ]);
?>   
</body>
</html>