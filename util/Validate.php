<?php

/**
 * 通用数据校验工具
 */
class Validate
{
    /**
     * 校验赛事计时系统回调签名（示例实现）
     */
    public static function checkSign(array $payload): bool
    {
        $secret = 'CMX_SECRET_KEY'; // TODO: 从安全配置加载
        if (empty($payload['timestamp']) || empty($payload['sign'])) {
            return false;
        }

        if (abs(time() - (int)$payload['timestamp']) > 300) {
            // 超过 5 分钟视为无效
            return false;
        }

        $dataForSign = $payload;
        unset($dataForSign['sign']);

        ksort($dataForSign);
        $str = json_encode($dataForSign, JSON_UNESCAPED_UNICODE);
        $calcSign = md5($str . $secret);

        return hash_equals($calcSign, $payload['sign']);
    }

    /**
     * 校验单条计时数据
     */
    public static function checkTimingItem(array $item): void
    {
        $required = ['car_number', 'current_rank'];
        foreach ($required as $field) {
            if (!isset($item[$field]) || $item[$field] === '') {
                throw new InvalidArgumentException('missing_field: ' . $field);
            }
        }

        if (!ctype_digit((string)$item['current_rank'])) {
            throw new InvalidArgumentException('invalid_current_rank');
        }
    }
}


