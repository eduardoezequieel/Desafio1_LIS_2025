<?php
use App\Helpers\DashboardPage;

require_once __DIR__ . '/../../app/helpers/DashboardPage.php';

DashboardPage::getSidebarTemplate('Usuarios');
DashboardPage::getHeaderTemplate('Usuarios', [
    [
        'class' => 'btn-success',
        'text' => 'Nuevo Usuario',
        'id' => 'new-user-btn',
        'onClick' => 'alert("Funcionalidad en desarrollo")'
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
    <?php
    DashboardPage::getFooterTemplate([
        '/Desafio1_LIS_2025/resources/js/dashboard/users.js'
    ]);
?>   
</body>
</html>