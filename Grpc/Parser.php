<?php

namespace Grpc;

use Google\Protobuf\Internal\Message;

/**
 * 数据解析类
 */
class Parser
{

    public static function pack(string $data): string
    {
        return $data = pack('CN', 0, strlen($data)) . $data;
    }

    public static function unpack(string $data): string
    {
        return $data = substr($data, 5);
    }

    public static function serializeMessage($data)
    {
        if (method_exists($data, 'encode')) {
            $data = $data->encode();
        } else if (method_exists($data, 'serializeToString')) {
            $data = $data->serializeToString();
        } else {
            /** @noinspection PhpUndefinedMethodInspection */
            $data = $data->serialize();
        }
        return self::pack($data);
    }

    public static function deserializeMessage($deserialize, string $value)
    {
        if (empty($value)) {
            return null;
        } else {
            $value = self::unpack($value);
        }
        if (is_array($deserialize)) {
            list($className, $deserializeFunc) = $deserialize;
            /** @var $obj Message */
            $obj = new $className();
            if ($deserializeFunc && method_exists($obj, $deserializeFunc)) {
                $obj->$deserializeFunc($value);
            } else {
                $obj->mergeFromString($value);
            }
            return $obj;
        }

        return call_user_func($deserialize, $value);
    }

    public static function parseToResultArray($response, $deserialize): array
    {
        if (!$response) {
            return ['No response', GRPC_ERROR_NO_RESPONSE, $response];
        } else if ($response->statusCode !== 200) {
            return ['Http status Error', $response->errCode ?: $response->statusCode, $response];
        } else {
            $grpc_status = (int)($response->headers['grpc-status'] ?? 0);
            if ($grpc_status !== 0) {
                return [$response->headers['grpc-message'] ?? 'Unknown error', $grpc_status, $response];
            }
            $data = $response->data;
            $reply = self::deserializeMessage($deserialize, $data);
            $status = (int)($response->headers['grpc-status'] ?? 0 ?: 0);
            return [$reply, $status, $response];
        }
    }
}