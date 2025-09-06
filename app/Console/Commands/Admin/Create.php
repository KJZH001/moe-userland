<?php

namespace App\Console\Commands\Admin;

use App\Models\Admin;
use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as CommandAlias;

class Create extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '创建一个管理员账号。';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // 移除 Yubico OTP 验证部分，直接创建管理员

        // 名称
        $name = $this->ask('请输入名称');
        $email = $this->ask('请输入邮箱');

        // 检查管理员是否已存在相同的邮箱
        $existingAdmin = Admin::where('email', $email)->first();
        if ($existingAdmin) {
            $this->error('该邮箱已经被注册为管理员：' . $existingAdmin->name . '。');

            return CommandAlias::FAILURE;
        }

        // 创建管理员
        $admin = Admin::create([
            'name' => $name,
            'email' => $email,
        ]);

        // 输出信息
        $this->info('管理员创建成功，ID 为: ' . $admin->id . '。');

        return CommandAlias::SUCCESS;
    }
}
