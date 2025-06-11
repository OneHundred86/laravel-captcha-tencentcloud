<?php

namespace Oh86\Captcha\Tencentcloud\Impls;

use Illuminate\Contracts\Container\Container as Application;
use Oh86\Captcha\CaptchaInterface;
use Oh86\Captcha\Tencentcloud\Services\CaptchaService;
use RuntimeException;

class TencentCloudCaptcha implements CaptchaInterface
{
    private CaptchaService $service;

    public function __construct(Application $app)
    {
        $config = $app->get('config')->get('captcha.tencentCloud');
        $this->service = new CaptchaService($config);
    }

    public function acquire($options = null): array
    {
        throw new RuntimeException('请前端使用腾讯云api获取验证码');
    }

    /**
     * @param array{ticket:string, randStr:string, userIp:string} $captcha
     * @return bool
     */
    public function verify($captcha): bool
    {
        return $this->service->describeCaptchaResult($captcha['ticket'], $captcha['randStr'], $captcha['userIp']);
    }
}