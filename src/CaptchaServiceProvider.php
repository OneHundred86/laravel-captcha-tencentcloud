<?php

namespace Oh86\Captcha\Tencentcloud;

use Illuminate\Support\ServiceProvider;
use Oh86\Captcha\CaptchaManager;
use Oh86\Captcha\Tencentcloud\Captchas\TencentCloudCaptcha;


class CaptchaServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->app->make(CaptchaManager::class)
            ->extend('tencentCloud', function ($app) {
                return new TencentCloudCaptcha($app);
            });
    }
}