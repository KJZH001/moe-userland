@extends('layouts.admin')

@section('title', '管理员登录')

@section('content')
<div class="min-vh-100 d-flex align-items-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow-lg border-0">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <h4 class="mb-1">管理员登录</h4>
                            <p class="text-muted">使用 OIDC 身份验证登录</p>
                        </div>

                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        <div class="text-center">
                            <div class="oidc-login-container mb-4">
                                <div class="oidc-icon mb-3">
                                    <i class="bi bi-shield-lock-fill text-primary" style="font-size: 4rem;"></i>
                                </div>
                                
                                <a href="{{ route('admin.oidc.redirect') }}" class="btn btn-primary btn-lg w-100 mb-3">
                                    <i class="bi bi-box-arrow-in-right me-2"></i>
                                    使用 OIDC 登录
                                </a>
                            </div>
                        </div>

                        <div class="mt-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="flex-shrink-0">
                                    <i class="bi bi-shield-check text-primary fs-4"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1">安全认证</h6>
                                    <p class="text-muted small mb-0">
                                        通过 OIDC 身份提供者进行安全认证
                                    </p>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="bi bi-person-check text-primary fs-4"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1">用户验证</h6>
                                    <p class="text-muted small mb-0">
                                        系统将验证您的身份并检查管理员权限
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="text-center mt-4">
                            <p class="text-muted small mb-0">
                                Powered by <a href="https://www.leaflow.cn" class="text-decoration-none">Leaflow</a> &
                                <a href="https://openid.net" class="text-decoration-none">OpenID Connect</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .oidc-login-container {
        padding: 2rem 0;
    }

    .oidc-icon {
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.05);
        }
        100% {
            transform: scale(1);
        }
    }

    .btn-primary {
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
    }
</style>
@endsection
