<?php

namespace Nece\Brawl\Sms\Aliyun;

use Nece\Brawl\ConfigAbstract;

/**
 * 配置类
 *
 * @Author nece001@163.com
 * @DateTime 2023-06-16
 */
class Config extends ConfigAbstract
{
    /**
     * 构建配置模板
     *
     * @Author nece001@163.com
     * @DateTime 2023-06-16
     *
     * @return void
     */
    public function buildTemplate()
    {
        $this->addTemplate(true, 'accessKeyId', 'accessKeyId', 'accessKeyId 从阿里云控制台的访问控制获取');
        $this->addTemplate(true, 'accessKeySecret', 'accessKeySecret', 'accessKeySecret 从阿里云控制台的访问控制获取');
        $this->addTemplate(true, 'endpoint', '接入地域域名', '', 'dysmsapi.aliyuncs.com');
        $this->addTemplate(true, 'sign_name', '短信签名名称', '必须是已添加、并通过审核的短信签名。');

        $this->addTemplate(false, 'read_timeout', '读数据超时时间', '秒');
        $this->addTemplate(false, 'connect_timeout', '连接超时时间', '秒');
        $this->addTemplate(false, 'http_proxy', 'HTTP 代理', '例：http://aaa.com:123');
        $this->addTemplate(false, 'https_proxy', 'HTTPS 代理', '例：https://aaa.com:123');
        $this->addTemplate(false, 'no_proxy', '代理白名单', '');
        $this->addTemplate(false, 'max_attempts', '最大重试次数', '');
    }
}
