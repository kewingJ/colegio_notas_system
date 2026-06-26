<?php
// ============================================================
// SISTEMA DE NOTAS - INSTITUTO ARCOÍRIS
// Phase 1: Administrator Module
// ============================================================

// --- BASE DE DATOS LOCAL (MySQL XAMPP) ---
define('DB_HOST',     'localhost');
define('DB_PORT',     '3306');
define('DB_NAME',     'sistema_notas_arcoiris');
define('DB_USER',     'root');
define('DB_PASS',     '');
define('DB_CHARSET',  'utf8mb4');

// --- API REST EXTERNA (Sistema Principal del Colegio) ---
// En desarrollo usamos MOCK para no depender de sistemas externos
define('API_BASE_URL',    'http://localhost/sistema-colegio/api');
define('API_TIMEOUT',     10);       // segundos
define('API_CACHE_TTL',   3600);     // 1 hora
define('API_USE_MOCK',    true);     // Cambiar a false para conectar a API real

// Endpoints:
define('API_ENDPOINT_COLEGIO',        '/colegio');
define('API_ENDPOINT_ALUMNOS',        '/alumnos');
define('API_ENDPOINT_NIVELES_GRADOS', '/niveles-grados');

// --- CONFIGURACIÓN DE LA APLICACIÓN ---
define('APP_NAME',     'Sistema de Notas — Arcoíris');
define('APP_URL',      'http://localhost/sistema-notas/public');
define('APP_VERSION',  '1.0.0');
define('APP_ENV',      'development'); // production | development

// --- SEGURIDAD ---
define('SESSION_NAME',       'arcoiris_notas_session');
define('SESSION_LIFETIME',   7200);      // 2 horas
define('CSRF_TOKEN_NAME',    '_csrf_token');
define('BCRYPT_COST',        12);

// --- PAGINACIÓN ---
define('DEFAULT_PAGE_SIZE',  15);

// --- ZONA HORARIA ---
date_default_timezone_set('America/Managua');

/**
 * Escapa valores para salida HTML (XSS Protection)
 */
function h(?string $val): string {
    return htmlspecialchars((string)$val, ENT_QUOTES, 'UTF-8');
}
