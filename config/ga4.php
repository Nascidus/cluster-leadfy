<?php

return [
    /*
    |--------------------------------------------------------------------------
    | GA4 Property ID
    |--------------------------------------------------------------------------
    |
    | ID numérico da propriedade GA4. Pode ser sobrescrito via variável
    | de ambiente GA4_PROPERTY_ID. Ex.: 338556634
    |
    */

    'property_id' => env('GA4_PROPERTY_ID', '338556634'),

    /*
    |--------------------------------------------------------------------------
    | Caminho das credenciais da conta de serviço
    |--------------------------------------------------------------------------
    |
    | Caminho para o arquivo JSON da conta de serviço usada para consultar
    | a Google Analytics Data API (GA4). Por padrão, aponta para o arquivo
    | ga4-reader-key.json na raiz do projeto Laravel.
    |
    */

    'credentials_path' => env('GA4_CREDENTIALS_PATH', base_path('ga4-reader-key.json')),
];






