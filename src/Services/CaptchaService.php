<?php

namespace Oh86\Captcha\Tencentcloud\Services;

use TencentCloud\Captcha\V20190722\CaptchaClient;
use TencentCloud\Captcha\V20190722\Models\DescribeCaptchaResultRequest;
use TencentCloud\Common\Credential;

class CaptchaService
{
    /**
     * @var array{
     *          secret_id:string, 
     *          secret_key:string, 
     *          region:string, 
     *          captcha_app:array{
     *              app_id:string, 
     *              secret_key:string,
     *          }
     *      }
     */
    private array $config;

    /**
     * @param array{
     *          secret_id:string, 
     *          secret_key:string, 
     *          region:string, 
     *          captcha_app:array{
     *              app_id:string, 
     *              secret_key:string,
     *          }
     *      } $config
     */
    public function __construct($config)
    {
        $this->config = $config;
    }

    public function createCredential()
    {
        return new Credential($this->config['secret_id'], $this->config['secret_key']);
    }

    public function getRegion()
    {
        return $this->config['region'] ?? 'ap-guangzhou';
    }

    /**
     * 校验验证码
     * @return bool
     */
    public function describeCaptchaResult(string $ticket, string $randStr, string $userIp)
    {
        $client = new CaptchaClient($this->createCredential(), $this->getRegion());

        $req = new DescribeCaptchaResultRequest();
        $req->CaptchaAppId = $this->config['captcha_app']['app_id'];
        $req->AppSecretKey = $this->config['captcha_app']['secret_key'];
        $req->CaptchaType = 9;
        $req->Ticket = $ticket;
        $req->Randstr = $randStr;
        $req->UserIp = $userIp;

        $resp = $client->DescribeCaptchaResult($req);

        return $resp->getCaptchaCode() === 1;
    }
}