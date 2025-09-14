# OIDC 后台登录配置说明

## 概述

本项目已从 Yubico OTP 验证迁移到 OIDC (OpenID Connect) 验证。管理员现在可以通过 OIDC 身份提供者进行登录。

## 环境变量配置

请在您的 `.env` 文件中添加以下配置：

```env
# OIDC 身份提供者配置
OIDC_ISSUER=https://auth.moeworld.tech
OIDC_CLIENT_ID=your_client_id_here
OIDC_CLIENT_SECRET=your_client_secret_here

# OIDC 回调URL (可选，默认使用 APP_URL + /admin/oidc/callback)
OIDC_REDIRECT_URI=https://userland.moeworld.top/admin/oidc/callback

# OIDC 端点配置 (可选，如果自动发现失败则使用这些配置)
OIDC_AUTHORIZATION_ENDPOINT=https://auth.moeworld.tech/login/oauth/authorize
OIDC_TOKEN_ENDPOINT=https://auth.moeworld.tech/api/login/oauth/access_token
OIDC_USERINFO_ENDPOINT=https://auth.moeworld.tech/api/userinfo
OIDC_JWKS_URI=https://auth.moeworld.tech/.well-known/jwks
OIDC_END_SESSION_ENDPOINT=https://auth.moeworld.tech/api/logout
```

## 用户映射

系统会将 OIDC 返回的 `preferred_username` 字段映射到 `admins` 表中的 `name` 字段。

- 如果 `preferred_username` 在 `admins` 表中存在对应的记录，则登录成功
- 如果不存在，则登录失败

## 管理员账户

确保在 `admins` 表中存在对应的管理员记录：

```sql
INSERT INTO admins (name, email, created_at, updated_at) 
VALUES ('your_username', 'your_email@example.com', NOW(), NOW());
```

## 登录流程

1. 访问 `/admin` 页面
2. 点击 "使用 OIDC 登录" 按钮
3. 重定向到 OIDC 身份提供者
4. 在身份提供者页面完成认证
5. 重定向回应用并自动登录

## 安全说明

- 所有 OIDC 配置都存储在 `.env` 文件中，不会提交到代码仓库
- 使用 state 和 nonce 参数防止 CSRF 攻击
- 支持 JWT 令牌验证和签名验证

## 故障排除

如果遇到问题，请检查：

1. OIDC 配置是否正确
2. 回调 URL 是否在 OIDC 提供者中正确配置
3. 管理员账户是否存在于 `admins` 表中
4. 网络连接是否正常（OIDC 发现端点可访问）

## 日志

OIDC 认证过程中的错误会记录在 Laravel 日志中，请查看 `storage/logs/laravel.log` 文件。
