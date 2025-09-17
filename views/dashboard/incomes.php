<?php
use App\Helpers\DashboardPage;

require_once __DIR__ . '/../../app/helpers/DashboardPage.php';

DashboardPage::getSidebarTemplate('Entradas', [
    '/Desafio1_LIS_2025/resources/css/incomes.css'
]);
DashboardPage::getHeaderTemplate('Entradas', [
    [
        'class' => 'btn-success',
        'text' => 'Registrar entrada',
        'id' => 'new-income-btn',
    ]
]);
?>   
    <main class="content">
        <div class="h-100 d-flex align-items-center justify-content-center">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Usuario</th>
                    <th scope="col">Última edición</th>
                    <th scope="col">Acciones</th>
                </tr>
            </thead>
            <tbody id="users-table-body">
                
            </tbody>
        </table>
        </div>
    </main>
    <div class="modal fade" id="incomeModal" tabindex="-1" aria-labelledby="income-modal-label" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="income-modal-label">Registrar entradaaaa</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="income-form" method="POST" class="d-flex flex-column gap-2">
                        <input type="hidden" name="id" id="income-id">
                        <div class="form-group">
                            <label for="income-type">Tipo de entrada</label>
                            <select name="income-type" id="income-type" class="form-select" required>
                                <option value="sale">Venta</option>
                                <option value="service">Servicio</option>
                                <option value="other">Otro</option>
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
                            <label for="invoiceImage">Factura (foto)</label>
                            <div id="invoice-preview-file-container" class="invoice-preview">
                                <span>Haz clic para subir o arrastra para subir una imagen</span>
                                 <!-- <div class="w-100 d-flex justify-content-between align-items-center px-2">
                                    <span>Filename.jpg</span>
                                    <button type="button" class="btn btn-danger btn-sm">X</button>
                                 </div> -->
                            </div>
                            <input type="file" class="form-control d-none" id="invoiceImage" name="invoiceImage" accept="image/*">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button id="save-income-btn" type="button" class="btn btn-primary">Guardar cambios</button>
                </div>
            </div>
        </div>
    </div>
    <?php
    DashboardPage::getFooterTemplate([
        '/Desafio1_LIS_2025/resources/js/dashboard/incomes.js'
    ]);
?>   
</body>
</html>