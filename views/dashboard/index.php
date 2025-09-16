<?php
use App\Helpers\DashboardPage;

require_once __DIR__ . '/../../app/helpers/DashboardPage.php';

DashboardPage::getSidebarTemplate('Dashboard');
DashboardPage::getHeaderTemplate('Welcome');
?>   
    <main class="content">
        <div class="h-100 d-flex align-items-center justify-content-center">
            <h1>Bienvenido al Dashboard</h1>
        </div>
    </main>
    <?php
    DashboardPage::getFooterTemplate([
        '/Desafio1_LIS_2025/resources/js/dashboard/index.js'
    ]);
?>   
</body>
</html>