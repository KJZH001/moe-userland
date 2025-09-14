<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Support\Auth\OIDCProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuthController extends Controller
{
    private OIDCProvider $oidcProvider;

    public function __construct()
    {
        $this->oidcProvider = new OIDCProvider();
    }

    public function index(): View|RedirectResponse
    {
        return view('admin.index');
    }

    public function showLoginForm(): View|RedirectResponse
    {
        if (auth('admin')->check()) {
            return redirect()->route('admin.index');
        }

        return view('admin.login');
    }

    /**
     * 重定向到OIDC授权端点
     */
    public function redirectToOIDC(): RedirectResponse
    {
        $authorizationUrl = $this->oidcProvider->getAuthorizationUrl();
        return redirect($authorizationUrl);
    }

    /**
     * 处理OIDC回调
     */
    public function handleOIDCCallback(Request $request): RedirectResponse
    {
        $code = $request->query('code');
        $state = $request->query('state');
        $error = $request->query('error');

        if ($error) {
            return redirect()->route('admin.login')
                ->with('error', 'OIDC认证失败: ' . ($request->query('error_description') ?? $error));
        }

        if (!$code || !$state) {
            return redirect()->route('admin.login')
                ->with('error', 'OIDC认证参数不完整');
        }

        try {
            $admin = $this->oidcProvider->authenticateUser($code, $state);

            if (!$admin) {
                return redirect()->route('admin.login')
                    ->with('error', '用户认证失败或管理员不存在');
            }

            auth('admin')->login($admin, true);

            return redirect()->route('admin.index')
                ->with('success', '登录成功');

        } catch (\Exception $e) {
            \Log::error('OIDC Authentication Error: ' . $e->getMessage());
            
            return redirect()->route('admin.login')
                ->with('error', '认证过程中发生错误，请重试');
        }
    }

    public function logout(): RedirectResponse
    {
        auth('admin')->logout();

        // 可选：重定向到OIDC提供者的登出端点
        // $logoutUrl = $this->oidcProvider->getLogoutUrl();
        // return redirect($logoutUrl);

        return redirect()->route('admin.login');
    }
}
