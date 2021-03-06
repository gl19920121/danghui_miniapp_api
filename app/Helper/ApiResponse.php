<?php

namespace App\Helper;

class ApiResponse
{
    public const API_OK = 0;
    public const API_FAIL = -1;
    public const API_TOKEN_INVALID = 1;

    public const API_FILE_IS_NOT_VALID = 2;

    public const API_ACCEPTED = 202;
    public const API_FORBIDDEN = 403;
    public const API_NOT_FOUND = 404;

    public const WECHAT_SYSTEM_BUSY = 1000;
    public const WECHAT_CODE_INVALID = 1001;
    public const WECHAT_CODE_BE_USED = 1002;
    public const WECHAT_CODE_MISS = 1003;
    public const WECHAT_FREQUENCY_TOO_HIGH = 1004;

    public const SMS_SEND_FAIL = 2001;
    public const SMS_SEND_EXCEED_PHONE_SEND_LIMIT = 2002;
    public const SMS_CHECK_FAIL = 2011;
    public const SMS_CHECK_OVERDUE = 2012;

    protected $statusCode;

    protected $statusText;

    protected $defaultStatusText;

    public static $statusTexts = [
        202 => '已经接受请求，但未处理完成',
        403 => '拒绝处理此次请求',
        404 => '资源未找到',
        1000 => '系统繁忙，请稍后再试',
        1001 => 'code无效',
        1002 => 'code已被使用',
        1003 => '缺少code',
        1004 => '操作频率过高，请稍后尝试',
        2001 => '发送失败，请稍后重试',
        2002 => '发送频率过高，请稍后再试',
        2011 => '验证码错误，请重新填写',
        2012 => '验证码已过期，请重新发送',
    ];

    public static $defaultStatusTexts = [
        'ok' => '请求成功',
        'fail' => '请求失败',
    ];

    public function __construct(int $status = self::API_OK)
    {
        $this->statusCode = $status;
        $this->statusText = self::$statusTexts[$status] ?? $this->getDefaultStatusText();
    }

    public static function create(int $status = self::API_OK)
    {
        return new static($status);
    }

    public function hasStatusText(): bool
    {
        return isset(self::$statusTexts[$this->statusCode]);
    }

    public function getStatusText(): string
    {
        if ($this->hasStatusText()) {
            return $this->statusText;
        } else {
            return $this->getDefaultStatusText();
        }
    }

    public function getDefaultStatusText(): string
    {
        return $this->statusCode === 0 ? self::$defaultStatusTexts['ok']: self::$defaultStatusTexts['fail'];
    }
}
