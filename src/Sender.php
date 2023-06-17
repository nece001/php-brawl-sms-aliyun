<?php

namespace Nece\Brawl\Sms\Aliyun;

use Darabonba\OpenApi\Models\Config;
use AlibabaCloud\SDK\Dysmsapi\V20170525\Dysmsapi;
use AlibabaCloud\SDK\Dysmsapi\V20170525\Models\SendSmsRequest;
use AlibabaCloud\Tea\Utils\Utils\RuntimeOptions;
use Nece\Brawl\Sms\SenderAbstract;
use Nece\Brawl\Sms\SendResult;
use Nece\Brawl\Sms\SmsException;
use Throwable;

class Sender extends SenderAbstract
{
    private $client;
    private $send_numbers;

    public function send($template_id, array $params): SendResult
    {
        $result = new SendResult();

        try {
            $runtime = $this->buildRuntime();
            $req = $this->buildRequest($template_id, $params);
            $res = $this->getClient()->sendSmsWithOptions($req, $runtime);

            // print_r($res->body);exit;

            $result->setRaw(json_encode($res->body, JSON_UNESCAPED_UNICODE));
            $result->setRequestId($res->body->requestId);
            foreach ($this->send_numbers as $number) {
                $result->addResult($res->body->bizId, $number, 0, '', $res->body->code, $res->body->message, '', $res->body->code == 'OK');
            }
        } catch (Throwable $e) {
            $this->error_message = $e->getMessage();
            throw new SmsException('短信发送异常');
        }

        return $result;
    }

    private function getClient()
    {
        $accessKeyId = $this->getConfigValue('accessKeyId');
        $accessKeySecret = $this->getConfigValue('accessKeySecret');
        $endpoint = $this->getConfigValue('endpoint', 'dysmsapi.aliyuncs.com');

        if (!$this->client) {
            $conf = array(
                'accessKeyId' => $accessKeyId,
                'accessKeySecret' => $accessKeySecret,
                'endpoint' => $endpoint,
            );

            $config = new Config($conf);
            $this->client = new Dysmsapi($config);
        }

        return $this->client;
    }

    private function buildRuntime()
    {
        $readTimeout = $this->getConfigValue('read_timeout', 60);
        $connectTimeout = $this->getConfigValue('connect_timeout', 60);
        $httpProxy = $this->getConfigValue('http_proxy');
        $httpsProxy = $this->getConfigValue('https_proxy');
        $noProxy = $this->getConfigValue('no_proxy');
        $maxAttempts = $this->getConfigValue('max_attempts', 3);

        $conf = array(
            'ignoreSSL' => true
        );

        if ($readTimeout) {
            $conf['readTimeout'] = $readTimeout * 1000;
        }

        if ($connectTimeout) {
            $conf['connectTimeout'] = $connectTimeout * 1000;
        }

        if ($httpProxy) {
            $conf['httpProxy'] = $httpProxy;
        }

        if ($httpsProxy) {
            $conf['httpsProxy'] = $httpsProxy;
        }

        if ($noProxy) {
            $conf['noProxy'] = $noProxy;
        }

        if ($maxAttempts) {
            $conf['maxAttempts'] = $maxAttempts;
            $conf['autoretry'] = true;
        }

        return new RuntimeOptions($conf);
    }

    private function buildRequest($template_code, array $params, $sms_up_extend_code = '', $out_id = '')
    {
        $signName = $this->getConfigValue('sign_name');

        $conf = array(
            "phoneNumbers" => $this->formatPhoneNumbers(),
            "signName" => $signName,
            "templateCode" => $template_code,
            "templateParam" => json_encode($params, JSON_UNESCAPED_UNICODE)
        );

        if ($sms_up_extend_code) {
            $conf['smsUpExtendCode'] = $sms_up_extend_code;
        }

        if ($out_id) {
            $conf['outId'] = $out_id;
        }

        return new SendSmsRequest($conf);
    }

    private function formatPhoneNumbers()
    {
        $data = array();
        foreach ($this->phone_numbers as $row) {
            $data[] = '+' . $row['code'] . $row['phone_number'];
        }

        $this->phone_numbers = array();
        $this->send_numbers = $data;

        return implode(',', $data);
    }
}
