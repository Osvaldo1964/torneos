<?php
class JwtHandler
{
    private $secret;

    public function __construct()
    {
        $this->secret = JWT_SECRET;
    }

    public function createToken($data)
    {
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        $payload = json_encode(array_merge($data, ['exp' => time() + 3600])); // 1 hour

        $base64UrlHeader = $this->base64UrlEncode($header);
        $base64UrlPayload = $this->base64UrlEncode($payload);

        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $this->secret, true);
        $base64UrlSignature = $this->base64UrlEncode($signature);

        return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    }

    public function validateToken($token)
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3)
            return false;

        list($header, $payload, $signature) = $parts;

        $validSignature = hash_hmac('sha256', $header . "." . $payload, $this->secret, true);
        $validSignature = $this->base64UrlEncode($validSignature);

        if ($signature !== $validSignature)
            return false;

        $payloadData = json_decode($this->base64UrlDecode($payload), true);
        if ($payloadData['exp'] < time())
            return false;

        return $payloadData;
    }

    private function base64UrlEncode($data)
    {
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($data));
    }

    private function base64UrlDecode($data)
    {
        $remainder = strlen($data) % 4;
        if ($remainder) {
            $padlen = 4 - $remainder;
            $data .= str_repeat('=', $padlen);
        }
        return base64_decode(str_replace(['-', '_'], ['+', '/'], $data));
    }
}
?>