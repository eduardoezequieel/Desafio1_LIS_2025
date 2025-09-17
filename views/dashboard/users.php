<?php
use App\Helpers\DashboardPage;

require_once __DIR__ . '/../../app/helpers/DashboardPage.php';

DashboardPage::getSidebarTemplate('Usuarios');
DashboardPage::getHeaderTemplate('Usuarios', [
    [
        'class' => 'btn-success',
        'text' => 'Crear usuario',
        'id' => 'new-user-btn',
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
    <div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="user-modal-label" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="user-modal-label">Crear usuario</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="user-form" method="POST" class="d-flex flex-column gap-2">
                        <input type="hidden" name="id" id="user-id">
                        <div class="form-group">
                            <label for="username">Nombre de usuario</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Contraseña</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button id="save-user-btn" type="button" class="btn btn-primary">Guardar cambios</button>
                </div>
            </div>
        </div>
    </div>
    <?php
    DashboardPage::getFooterTemplate([
        '/Desafio1_LIS_2025/resources/js/dashboard/users.js'
    ]);
?>   
</body>
</html>