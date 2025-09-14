<?php

/**
 * OIDC 配置测试脚本
 * 运行方式: php test_oidc.php
 */

/*

// 出于安全考虑，如果测试完毕后请删除或注释本文件

require_once 'vendor/autoload.php';

// 加载Laravel环境
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Support\Auth\OIDCProvider;

echo "=== OIDC 配置测试 ===\n\n";

// 检查环境变量
echo "1. 检查环境变量配置:\n";
$requiredEnvVars = ['OIDC_ISSUER', 'OIDC_CLIENT_ID', 'OIDC_CLIENT_SECRET'];
foreach ($requiredEnvVars as $var) {
    $value = env($var);
    if ($value) {
        echo "   ✓ $var: " . (strlen($value) > 20 ? substr($value, 0, 20) . '...' : $value) . "\n";
    } else {
        echo "   ✗ $var: 未设置\n";
    }
}

echo "\n2. 测试OIDC发现文档获取:\n";
try {
    $oidcProvider = new OIDCProvider();
    $discovery = $oidcProvider->getDiscoveryDocument();
    
    echo "   ✓ 发现文档获取成功\n";
    echo "   - 授权端点: " . ($discovery['authorization_endpoint'] ?? 'N/A') . "\n";
    echo "   - 令牌端点: " . ($discovery['token_endpoint'] ?? 'N/A') . "\n";
    echo "   - 用户信息端点: " . ($discovery['userinfo_endpoint'] ?? 'N/A') . "\n";
    
} catch (Exception $e) {
    echo "   ✗ 发现文档获取失败: " . $e->getMessage() . "\n";
}

echo "\n3. 测试授权URL生成:\n";
try {
    $authorizationUrl = $oidcProvider->getAuthorizationUrl();
    echo "   ✓ 授权URL生成成功\n";
    echo "   URL: " . substr($authorizationUrl, 0, 100) . "...\n";
} catch (Exception $e) {
    echo "   ✗ 授权URL生成失败: " . $e->getMessage() . "\n";
}

echo "\n4. 检查管理员表:\n";
try {
    $adminCount = \App\Models\Admin::count();
    echo "   ✓ 管理员表连接正常\n";
    echo "   - 管理员数量: $adminCount\n";
    
    if ($adminCount > 0) {
        $admins = \App\Models\Admin::select('name', 'email')->get();
        echo "   - 现有管理员:\n";
        foreach ($admins as $admin) {
            echo "     * {$admin->name} ({$admin->email})\n";
        }
    }
} catch (Exception $e) {
    echo "   ✗ 管理员表检查失败: " . $e->getMessage() . "\n";
}

echo "\n=== 测试完成 ===\n";
echo "\n注意事项:\n";
echo "- 请确保在 .env 文件中正确配置了 OIDC 参数\n";
echo "- 确保 OIDC 提供者可以访问\n";
echo "- 确保回调 URL 在 OIDC 提供者中正确配置\n";
echo "- 管理员用户名需要与 OIDC 的 preferred_username 匹配\n";

*/