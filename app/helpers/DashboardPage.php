<?php

namespace App\Helpers;

class DashboardPage
{
    public static function getSidebarTemplate(string $title): void
    {
        $currentUrl = $_SERVER['REQUEST_URI'];
        session_start();
        if (!isset($_SESSION['user'])) {
            header('Location: /Desafio1_LIS_2025/public/login');
            exit();
        }

        print('
            <!DOCTYPE html>
            <html lang="es" data-bs-theme="dark">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <link rel="stylesheet" href="/Desafio1_LIS_2025/resources/css/globals.css">
                <link rel="stylesheet" href="/Desafio1_LIS_2025/resources/css/dashboard.css">
                <link href="/Desafio1_LIS_2025/resources/lib/bootstrap/bootstrap.min.css" rel="stylesheet">
                <title>' . $title . '</title>
            </head>
            <body>
            <aside class="sidebar">
                <h5>Dashboard</h5>
                <ul class="nav flex-column">
                    <li class="nav-item ' . ($currentUrl === '/Desafio1_LIS_2025/public/dashboard' ? 'active' : '') . '">
                        <a href="/Desafio1_LIS_2025/public/dashboard">Inicio</a>
                    </li>
                    <li class="nav-item ' . ($currentUrl === '/Desafio1_LIS_2025/public/dashboard/users' ? 'active' : '') . '">
                        <a href="/Desafio1_LIS_2025/public/dashboard/users">Usuarios</a>
                    </li>
                    <li class="nav-item" id="logout-link">
                        <a>Cerrar sesión</a>
                    </li>
                </ul>
            </aside>
        ');
    }

    public static function getHeaderTemplate(string $title, array $buttons = []): void
    {
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

                $buttonsHtml .= '<button class="btn ' . $class . '"' . $id . $onClick . '>' . $text . '</button>' . "\n";
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

    public static function getFooterTemplate(array $scripts): void
    {
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
