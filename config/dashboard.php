<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Credenciais de acesso ao dashboard GA4
    |--------------------------------------------------------------------------
    |
    | Credenciais simples baseadas em arquivo/env para proteger o dashboard.
    | NÃO use isso para autenticação de usuários finais; é apenas um gate
    | de acesso interno.
    |
    */

    'username' => env('DASHBOARD_USER', 'admin'),
    'password' => env('DASHBOARD_PASS', 'changeme'),
];


