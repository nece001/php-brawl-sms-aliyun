# php-brawl-sms-aliyun
PHP 阿里云短信基础服务适配项目

# 依赖
composer require alibabacloud/dysmsapi-20170525

# 示例：
```php
    $conf = array(
        'accessKeyId' => 'xxx',
        'accessKeySecret' => 'xxx',
        'endpoint' => 'dysmsapi.aliyuncs.com',
        'sign_name' => '阿里云'
    );
    
    // 创建配置
    $config = Factory::createConfig('Aliyun');
    $config->setConfig($conf);

    // 创建客户端
    $sms = Factory::createClient($config);
    try {
        $template_id = '1529729'; // 模板ID
        $params = array('1234', '3'); // 内容参数

        // 添加号码
        $sms->addPhoneNumber('131xxxxxxxx');
        $sms->addPhoneNumber('132xxxxxxxx');

        // 发送
        $result = $sms->send($template_id, $params);

        echo '发送结果：';
        print_r($result);
    } catch (Throwable $e) {
        echo $e->getMessage();
        echo $sms->getErrorMessage();
    }
```