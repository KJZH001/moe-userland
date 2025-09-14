<?php

namespace App\Support\Auth;

use App\Models\Admin;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OIDCProvider
{
    private array $config;
    private ?array $discoveryDocument = null;

    public function __construct()
    {
        $this->config = config('oidc');
    }

    /**
     * 获取OIDC发现文档
     */
    public function getDiscoveryDocument(): array
    {
        if ($this->discoveryDocument === null) {
            try {
                $response = Http::timeout(10)->get($this->config['issuer'] . '/.well-known/openid_configuration');
                
                if (!$response->successful()) {
                    throw new Exception('Failed to fetch OIDC discovery document');
                }

                $this->discoveryDocument = $response->json();
            } catch (Exception $e) {
                Log::error('OIDC Discovery Document Error: ' . $e->getMessage());
                
                // 使用配置中的备用端点
                $this->discoveryDocument = [
                    'authorization_endpoint' => $this->config['authorization_endpoint'],
                    'token_endpoint' => $this->config['token_endpoint'],
                    'userinfo_endpoint' => $this->config['userinfo_endpoint'],
                    'jwks_uri' => $this->config['jwks_uri'],
                    'end_session_endpoint' => $this->config['end_session_endpoint'],
                ];
            }
        }

        return $this->discoveryDocument;
    }

    /**
     * 生成授权URL
     */
    public function getAuthorizationUrl(): string
    {
        $discovery = $this->getDiscoveryDocument();
        $state = Str::random(32);
        $nonce = Str::random(32);
        
        // 存储state和nonce到session中用于验证
        session(['oidc_state' => $state, 'oidc_nonce' => $nonce]);

        $params = [
            'response_type' => $this->config['response_type'],
            'client_id' => $this->config['client_id'],
            'redirect_uri' => $this->config['redirect_uri'],
            'scope' => implode(' ', $this->config['scopes']),
            'state' => $state,
            'nonce' => $nonce,
        ];

        return $discovery['authorization_endpoint'] . '?' . http_build_query($params);
    }

    /**
     * 交换授权码获取访问令牌
     */
    public function exchangeCodeForTokens(string $code, string $state): array
    {
        // 验证state参数
        if (session('oidc_state') !== $state) {
            throw new Exception('Invalid state parameter');
        }

        $discovery = $this->getDiscoveryDocument();
        
        $response = Http::timeout(10)->asForm()->post($discovery['token_endpoint'], [
            'grant_type' => $this->config['grant_type'],
            'code' => $code,
            'redirect_uri' => $this->config['redirect_uri'],
            'client_id' => $this->config['client_id'],
            'client_secret' => $this->config['client_secret'],
        ]);

        if (!$response->successful()) {
            throw new Exception('Failed to exchange code for tokens: ' . $response->body());
        }

        $tokens = $response->json();
        
        if (!isset($tokens['access_token'])) {
            throw new Exception('No access token received');
        }

        return $tokens;
    }

    /**
     * 获取用户信息
     */
    public function getUserInfo(string $accessToken): array
    {
        $discovery = $this->getDiscoveryDocument();
        
        $response = Http::timeout(10)
            ->withHeaders(['Authorization' => 'Bearer ' . $accessToken])
            ->get($discovery['userinfo_endpoint']);

        if (!$response->successful()) {
            throw new Exception('Failed to get user info: ' . $response->body());
        }

        return $response->json();
    }

    /**
     * 验证并获取用户信息
     */
    public function authenticateUser(string $code, string $state): ?Admin
    {
        try {
            // 交换授权码获取令牌
            $tokens = $this->exchangeCodeForTokens($code, $state);
            
            // 获取用户信息
            $userInfo = $this->getUserInfo($tokens['access_token']);
            
            // 根据preferred_username查找管理员
            $preferredUsername = $userInfo['preferred_username'] ?? null;
            
            if (!$preferredUsername) {
                Log::warning('OIDC user info missing preferred_username', $userInfo);
                return null;
            }

            // 在admins表中查找对应的管理员
            $admin = Admin::where('name', $preferredUsername)->first();
            
            if (!$admin) {
                Log::warning('Admin not found for preferred_username: ' . $preferredUsername);
                return null;
            }

            // 清除session中的临时数据
            session()->forget(['oidc_state', 'oidc_nonce']);

            return $admin;

        } catch (Exception $e) {
            Log::error('OIDC Authentication Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * 生成登出URL
     */
    public function getLogoutUrl(): string
    {
        $discovery = $this->getDiscoveryDocument();
        
        $params = [
            'post_logout_redirect_uri' => route('admin.login'),
        ];

        return $discovery['end_session_endpoint'] . '?' . http_build_query($params);
    }
}
