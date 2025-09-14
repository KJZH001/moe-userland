<?php

return [
    'issuer' => env('OIDC_ISSUER', 'https://auth.moeworld.tech'),
    'client_id' => env('OIDC_CLIENT_ID'),
    'client_secret' => env('OIDC_CLIENT_SECRET'),
    'redirect_uri' => env('OIDC_REDIRECT_URI', env('APP_URL') . '/admin/oidc/callback'),
    'scopes' => ['openid', 'profile', 'email'],
    'response_type' => 'code',
    'response_mode' => 'query',
    'grant_type' => 'authorization_code',
    
    // OIDC Discovery endpoints
    'authorization_endpoint' => env('OIDC_AUTHORIZATION_ENDPOINT', 'https://auth.moeworld.tech/login/oauth/authorize'),
    'token_endpoint' => env('OIDC_TOKEN_ENDPOINT', 'https://auth.moeworld.tech/api/login/oauth/access_token'),
    'userinfo_endpoint' => env('OIDC_USERINFO_ENDPOINT', 'https://auth.moeworld.tech/api/userinfo'),
    'jwks_uri' => env('OIDC_JWKS_URI', 'https://auth.moeworld.tech/.well-known/jwks'),
    'end_session_endpoint' => env('OIDC_END_SESSION_ENDPOINT', 'https://auth.moeworld.tech/api/logout'),
    
    // JWT verification settings
    'jwt_algorithm' => 'RS256',
    'jwt_verify_signature' => true,
    'jwt_verify_issuer' => true,
    'jwt_verify_audience' => true,
    'jwt_verify_expiration' => true,
    'jwt_verify_not_before' => true,
    'jwt_leeway' => 60, // seconds
    
    // User mapping settings
    'user_mapping' => [
        'preferred_username' => 'name', // Map preferred_username to admins.name
        'email' => 'email',
    ],
];
