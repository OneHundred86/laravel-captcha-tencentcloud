# 验证码-腾讯云行为验证码

### 一、安装

```shell
composer require oh86/laravel-captcha
composer require oh86/laravel-captcha-tencentcloud
```

### 二、配置 `config/captcha.php`

```php
return [
    'default' => env('CAPTCHA_DEFAULT_DRIVER', 'tencentCloud'),

    // ...

    // 腾讯云验证码配置
    'tencentCloud' => [
        'secret_id' => env('TENCENT_CLOUD_SECRET_ID'),
        'secret_key' => env('TENCENT_CLOUD_SECRET_KEY'),
        'region' => env('TENCENT_CLOUD_REGION', 'ap-guangzhou'),
        'captcha_app' => [
            'app_id' => (int) env('TENCENT_CLOUD_CAPTCHA_APP_ID'),
            'secret_key' => env('TENCENT_CLOUD_CAPTCHA_SECRET_KEY'),
        ],
    ],
];
```

### 二、使用

#### 1.验证验证码 demo1

```php
use Oh86\Captcha\Facades\Captcha;

// 验证
/** @var bool */
$result = Captcha::driver('tencentCloud')->verify(['ticket' => 'your_ticket_here', 'randStr' => 'your_rand_str_here', 'userIp' => 'your_user_ip_here']);
```

#### 2.验证验证码 demo2

```php
use App\Constants\ErrorCode;
use Illuminate\Http\Request;
use Oh86\Captcha\Facades\Captcha;
use Oh86\Http\Exceptions\ErrorCodeException;

trait CaptchaTrait
{
    /**
     * 校验验证码
     * @param \Illuminate\Http\Request $request
     * @throws \Oh86\Http\Exceptions\ErrorCodeException
     */
    protected function verifyCaptcha(Request $request)
    {
        $request->validate([
            'captcha_type' => 'nullable',
            'captcha' => 'required|array',
        ]);

        // 本地环境不校验
        if (config('app.env') == 'local') {
            return;
        }

        $captchaType = $request->captcha_type ?? Captcha::getDefaultDriver();

        if ($captchaType == 'image'){
            $request->validate([
                'captcha.type' => 'nullable',   // default,math,flat,mini,inverse
                'captcha.key' => 'required',
                'captcha.value' => 'required',
            ]);

            if (!Captcha::driver('image')->driver($request->captcha['type'] ?? null)->verify($request->captcha)) {
                throw new ErrorCodeException(ErrorCode::Error, '验证码错误');
            }
            return;
        } elseif ($captchaType == 'tencentCloud') {
            $request->validate([
                'captcha.ticket' => 'required',
                'captcha.randStr' => 'required',
            ]);

            $captcha = ['userIp' => $request->ip()] + $request->captcha;
        } else {
            throw new ErrorCodeException(ErrorCode::Error, '验证码类型不支持');
        }

        if (!Captcha::driver($request->captcha_type)->verify($captcha)) {
            throw new ErrorCodeException(ErrorCode::Error, '验证码错误');
        }
    }
}
```
