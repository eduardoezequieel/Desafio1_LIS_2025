<?php

namespace App\Helpers;

/**
 * Helper de layout para el dashboard:
 * - Valida sesión antes de renderizar.
 * - Evita duplicación de HTML (DRY).
 * - Inyecta estilos y scripts específicos por vista.
 * Ampliable: soporte para breadcrumbs, roles, multi-idioma.
 */
class DashboardPage
{
    /**
     * Prints initial HTML, validates session and builds sidebar.
     * @param string $title
     * @param array $styles Array of stylesheet URLs.
     */
    public static function getSidebarTemplate(string $title, array $styles): void
    {
        /**
         * Flujo:
         * 1. Inicia sesión (session_start).
         * 2. Redirige a login si usuario no está autenticado.
         * 3. Construye sidebar con link activo basado en REQUEST_URI.
         * 4. Inserta estilos pasados como arreglo.
         */

        $currentUrl = $_SERVER['REQUEST_URI'];

        require_once __DIR__ . '/../models/user.php';
        session_start();

        if (!isset($_SESSION['user'])) {
            header('Location: /Desafio1_LIS_2025/public/login');
            exit();
        }

        $stylesHtml = '';

        foreach ($styles as $style) {
            $stylesHtml .= '<link rel="stylesheet" href="' . $style . '">' . "\n";
        }

        print('
            <!DOCTYPE html>
            <html lang="es" data-bs-theme="dark">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <link rel="stylesheet" href="/Desafio1_LIS_2025/resources/css/globals.css">
                <link rel="stylesheet" href="/Desafio1_LIS_2025/resources/css/dashboard.css">
                ' . $stylesHtml . '
                <link href="/Desafio1_LIS_2025/resources/lib/bootstrap/bootstrap.min.css" rel="stylesheet">
                <title>' . $title . '</title>
            </head>
            <body>
            <aside class="sidebar">
                <h5>Dashboard</h5>
                <span>Bienvenido, ' . htmlspecialchars($_SESSION['user']->getUsername()) . '</span>
                <ul class="nav flex-column mt-4">
                    <li class="nav-item ' . ($currentUrl === '/Desafio1_LIS_2025/public/dashboard' ? 'active' : '') . '">
                        <a href="/Desafio1_LIS_2025/public/dashboard">Inicio</a>
                    </li>
                    <li class="nav-item ' . ($currentUrl === '/Desafio1_LIS_2025/public/dashboard/users' ? 'active' : '') . '">
                        <a href="/Desafio1_LIS_2025/public/dashboard/users">Usuarios</a>
                    </li>
                    <li class="nav-item ' . ($currentUrl === '/Desafio1_LIS_2025/public/dashboard/transactions' ? 'active' : '') . '">
                        <a href="/Desafio1_LIS_2025/public/dashboard/transactions">Transacciones</a>
                    </li>
                    <li class="nav-item" id="logout-link">
                        <a>Cerrar sesión</a>
                    </li>
                </ul>
            </aside>
        ');
    }

    /**
     * Prints top navigation header with dynamic action buttons.
     * @param string $title
     * @param array $buttons Each: ['class','text','id','onClick','type']
     */
    public static function getHeaderTemplate(string $title, array $buttons = []): void
    {
        /**
         * Genera encabezado flexible.
         * Cada botón soporta:
         *  - class: Clase Bootstrap.
         *  - text: Etiqueta visible.
         *  - id: Para JS.
         *  - onClick: Acción inline opcional.
         *  - type: type="button|submit".
         */

        $buttonsHtml = '';

        if (empty($buttons)) {
            $buttonsHtml = '
                
            ';
        } else {
            foreach ($buttons as $button) {
                $class = $button['class'] ?? 'btn-primary';
                $text = $button['text'] ?? 'Action';
                $id = isset($button['id']) ? ' id="' . $button['id'] . '"' : '';
                $onClick = isset($button['onClick']) ? ' onclick="' . $button['onClick'] . '"' : '';
                $type = isset($button['type']) ? ' type="' . $button['type'] . '"' : '';

                $buttonsHtml .= '<button class="btn ' . $class . '"' . $id . $onClick . $type . '>' . $text . '</button>' . "\n";
            }
        }

        print('
            <nav>
                <div class="header-container">
                    <h1 class="header-title">' . $title . '</h1>
                    <div class="header-actions">
                        ' . $buttonsHtml . '
                    </div>
                </div>
            </nav>
        ');
    }

    /**
     * Prints common JS libraries + page specific scripts.
     * @param array $scripts
     */
    public static function getFooterTemplate(array $scripts): void
    {
        /**
         * Inserta librerías base (Bootstrap, SweetAlert2, utilidades)
         * y luego scripts específicos de la vista.
         * Mejora futura: versión hash para cache busting.
         */

        $scriptsHtml = '';

        foreach ($scripts as $script) {
            $scriptsHtml .= '<script src="' . $script . '"></script>' . "\n";
        }
        print('
            <script src="/Desafio1_LIS_2025/resources/lib/bootstrap/bootstrap.bundle.min.js"></script>
            <script src="/Desafio1_LIS_2025/resources/lib/swal/sweetalert2@11.js"></script>
            <script src="/Desafio1_LIS_2025/resources/js/components.js"></script>
            <script src="/Desafio1_LIS_2025/resources/js/dashboard/sidebar.js"></script>
            ' . $scriptsHtml . '
        ');
    }
}
