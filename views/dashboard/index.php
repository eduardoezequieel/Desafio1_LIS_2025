<?php
use App\Helpers\DashboardPage;

require_once __DIR__ . '/../../app/helpers/DashboardPage.php';

DashboardPage::getSidebarTemplate('Dashboard', [
    '/Desafio1_LIS_2025/resources/css/dashboard-index.css'
]);
DashboardPage::getHeaderTemplate('Dashboard', [
    [
        'class' => 'btn-primary',
        'text' => 'Generar Reporte PDF',
        'id' => 'generate-pdf-btn',
    ]
]);
?>   
    <main class="content">
        <div class="dashboard-container">
            <!-- Summary Cards -->
            <div class="summary-cards">
                <div class="card income-card">
                    <div class="card-body">
                        <h5 class="card-title text-success">Total Ingresos</h5>
                        <h2 class="card-value" id="total-income">$0.00</h2>
                    </div>
                </div>
                <div class="card expense-card">
                    <div class="card-body">
                        <h5 class="card-title text-danger">Total Gastos</h5>
                        <h2 class="card-value" id="total-expenses">$0.00</h2>
                    </div>
                </div>
                <div class="card balance-card">
                    <div class="card-body">
                        <h5 class="card-title">Balance</h5>
                        <h2 class="card-value" id="balance">$0.00</h2>
                    </div>
                </div>
            </div>

            <!-- Chart Section -->
            <div class="chart-section">
                <div class="card">
                    <div class="card-header">
                        <h5>Distribución de Transacciones</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="transactionChart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>

            <!-- Tables Section -->
            <div class="tables-section">
                <!-- Income Table -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="text-success">Últimas Entradas</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Categoría</th>
                                        <th>Monto</th>
                                    </tr>
                                </thead>
                                <tbody id="income-table-body">
                                    <tr>
                                        <td colspan="3" class="text-center">Cargando...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Expense Table -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="text-danger">Últimos Gastos</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Categoría</th>
                                        <th>Monto</th>
                                    </tr>
                                </thead>
                                <tbody id="expense-table-body">
                                    <tr>
                                        <td colspan="3" class="text-center">Cargando...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <?php
    DashboardPage::getFooterTemplate([
        'https://cdn.jsdelivr.net/npm/chart.js',
        'https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js',
        '/Desafio1_LIS_2025/resources/js/dashboard/index.js'
    ]);
?>   
</body>
</html>