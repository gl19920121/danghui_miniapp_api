<?php

return [
    /** 产品密钥ID，产品标识 */
    'secret_id' => 'b0167df2292ae08198b79da528acafd2',
    /** 产品私有密钥，服务端生成签名信息使用，请严格保管，避免泄露 */
    'secret_key' => '221c5e86942c65d562c5d83f0b3c5adb',
    /** 业务ID，易盾根据产品业务特点分配 */
    'business_id' => 'dc2fdda02761464fa3645556a88af448',
    /** 易盾短信服务发送接口地址 */
    'api_url' => [
        'send' => 'http://sms.dun.163.com/v2/sendsms',
        'check' => 'https://sms.dun.163.com/v2/verifysms'
    ],
    /** api version */
    'version' => 'v2',
    /** API timeout*/
    'api_timeout' => 2,
    /** php内部使用的字符串编码 */
    'internal_string_charset' => 'auto',
];
