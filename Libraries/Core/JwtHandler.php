<?php
/**
 * Clase ultra minificada para manejar JWT sin dependencias externas pesadas
 */
class JwtHandler
{
    private $secret;

    public function __construct()
    {
        $this->secret = JWT_KEY;
    }

    public function createToken($data, $duration = 3600)
    {
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        $payload = json_encode([
            'iat' => time(),
            'exp' => time() + $duration,
            'data' => $data
        ]);

        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $this->secret, true);
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

        return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    }

    public function validateToken($token)
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3)
            return false;

        list($header, $payload, $signature) = $parts;

        $validSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode(hash_hmac('sha256', $header . "." . $payload, $this->secret, true)));

        if ($signature !== $validSignature)
            return false;

        $data = json_decode(base64_decode($payload), true);
        if ($data['exp'] < time())
            return false;

        return $data['data'];
    }
}
?>