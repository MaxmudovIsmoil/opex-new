<?php

return [

    'host' => env('LDAP_HOST', '192.168.30.2'),
    'port' => env('LDAP_PORT', 389),
    'base_dn' => env('LDAP_BASE_DN', "DC=etc,DC=msft"),
    'user' => env('LDAP_USER', 'devldap'),
    'password' => env('LDAP_PASSWORD', 'rz5zxRprmhUBDWxwM4co'),

];
