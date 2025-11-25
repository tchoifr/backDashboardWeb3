<?php

namespace App\Security;

use InvalidArgumentException;
use kornrunner\Keccak;

class WalletSignatureValidator
{
    private const SECP256K1_P = '0xFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFEFFFFFC2F';
    private const SECP256K1_N = '0xFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFEBAAEDCE6AF48A03BBFD25E8CD0364141';
    private const SECP256K1_GX = '0x79BE667EF9DCBBAC55A06295CE870B07029BFCDB2DCE28D959F2815B16F81798';
    private const SECP256K1_GY = '0x483ADA7726A3C4655DA4FBFC0E1108A8FD17B448A68554199C47D08FFB10D4B8';

    public function isValidSignature(string $address, string $message, string $signature): bool
    {
        try {
            $recovered = $this->recoverAddress($message, $signature);
        } catch (InvalidArgumentException $exception) {
            return false;
        }

        return $recovered !== null && strtolower($recovered) === strtolower($address);
    }

    private function recoverAddress(string $message, string $signature): ?string
    {
        $signature = $this->normalizeHex($signature);
        if (strlen($signature) !== 130) {
            throw new InvalidArgumentException('Signature must be 65 bytes long');
        }

        $messageHash = $this->hashMessage($message);

        $r = substr($signature, 0, 64);
        $s = substr($signature, 64, 64);
        $v = substr($signature, 128, 2);

        $recId = hexdec($v);
        if ($recId >= 35) {
            $recId -= 35;
        }
        if ($recId >= 27) {
            $recId -= 27;
        }

        $recId = $recId % 4;
        $publicKey = $this->publicKeyFromSignature($messageHash, $r, $s, $recId);

        return $this->publicKeyToAddress($publicKey);
    }

    private function publicKeyFromSignature(string $messageHash, string $rHex, string $sHex, int $recId): ?array
    {
        $n = gmp_init(self::SECP256K1_N, 16);
        $p = gmp_init(self::SECP256K1_P, 16);
        $r = gmp_init($rHex, 16);
        $s = gmp_init($sHex, 16);
        $e = gmp_init($messageHash, 16);

        if (gmp_compare($r, 1) < 0 || gmp_compare($r, gmp_sub($n, 1)) > 0) {
            return null;
        }
        if (gmp_compare($s, 1) < 0 || gmp_compare($s, gmp_sub($n, 1)) > 0) {
            return null;
        }

        $x = gmp_add($r, gmp_mul(gmp_init((string) intdiv($recId, 2), 10), $n));
        if (gmp_cmp($x, $p) >= 0) {
            return null;
        }

        $alpha = $this->mod(gmp_add(gmp_powm($x, 3, $p), gmp_init('7', 10)), $p);
        $beta = gmp_powm($alpha, gmp_div_q(gmp_add($p, 1), 4), $p);
        $isEven = gmp_cmp(gmp_mod($beta, 2), 0) === 0;
        $y = $isEven === (($recId & 1) === 0) ? $beta : gmp_sub($p, $beta);

        $rInv = gmp_invert($r, $n);
        if ($rInv === false) {
            return null;
        }

        $minusE = $this->mod(gmp_neg($e), $n);
        $srInv = $this->mod(gmp_mul($s, $rInv), $n);
        $erInv = $this->mod(gmp_mul($minusE, $rInv), $n);

        $gPoint = [
            'x' => gmp_init(self::SECP256K1_GX, 16),
            'y' => gmp_init(self::SECP256K1_GY, 16),
        ];

        $rPoint = ['x' => $x, 'y' => $y];
        $term1 = $this->scalarMultiply($srInv, $rPoint, $p);
        $term2 = $this->scalarMultiply($erInv, $gPoint, $p);

        if ($term1 === null || $term2 === null) {
            return null;
        }

        return $this->pointAdd($term1, $term2, $p);
    }

    private function publicKeyToAddress(?array $publicKey): ?string
    {
        if ($publicKey === null) {
            return null;
        }

        $x = str_pad(gmp_strval($publicKey['x'], 16), 64, '0', STR_PAD_LEFT);
        $y = str_pad(gmp_strval($publicKey['y'], 16), 64, '0', STR_PAD_LEFT);
        $publicKeyHex = $x . $y;
        $hash = Keccak::hash(hex2bin($publicKeyHex), 256);

        return '0x' . substr($hash, 24);
    }

    private function hashMessage(string $message): string
    {
        $length = strlen($message);
        $prefix = "\x19Ethereum Signed Message:\n{$length}";

        return Keccak::hash($prefix . $message, 256);
    }

    private function normalizeHex(string $value): string
    {
        $value = trim($value);
        if (str_starts_with($value, '0x')) {
            $value = substr($value, 2);
        }

        return strtolower($value);
    }

    private function pointAdd(?array $p, ?array $q, \GMP $mod): ?array
    {
        if ($p === null) {
            return $q;
        }
        if ($q === null) {
            return $p;
        }
        if (gmp_cmp($p['x'], $q['x']) === 0) {
            if (gmp_cmp($this->mod(gmp_add($p['y'], $q['y']), $mod), 0) === 0) {
                return null;
            }

            return $this->pointDouble($p, $mod);
        }

        $denominator = $this->mod(gmp_sub($q['x'], $p['x']), $mod);
        $inverse = gmp_invert($denominator, $mod);
        if ($inverse === false) {
            return null;
        }

        $lambda = gmp_mul(gmp_sub($q['y'], $p['y']), $inverse);
        $lambda = $this->mod($lambda, $mod);

        $x = $this->mod(gmp_sub(gmp_sub(gmp_powm($lambda, 2, $mod), $p['x']), $q['x']), $mod);
        $y = $this->mod(gmp_sub(gmp_mul($lambda, gmp_sub($p['x'], $x)), $p['y']), $mod);

        return ['x' => $x, 'y' => $y];
    }

    private function pointDouble(?array $p, \GMP $mod): ?array
    {
        if ($p === null) {
            return null;
        }

        if (gmp_cmp($p['y'], 0) === 0) {
            return null;
        }

        $denominator = gmp_mul(2, $p['y']);
        $inverse = gmp_invert($denominator, $mod);
        if ($inverse === false) {
            return null;
        }

        $lambda = gmp_mul(3, gmp_powm($p['x'], 2, $mod));
        $lambda = gmp_mul($lambda, $inverse);
        $lambda = $this->mod($lambda, $mod);

        $x = $this->mod(gmp_sub(gmp_powm($lambda, 2, $mod), gmp_mul(2, $p['x'])), $mod);
        $y = $this->mod(gmp_sub(gmp_mul($lambda, gmp_sub($p['x'], $x)), $p['y']), $mod);

        return ['x' => $x, 'y' => $y];
    }

    private function scalarMultiply(\GMP $k, array $point, \GMP $mod): ?array
    {
        $result = null;
        $addend = $point;

        while (gmp_cmp($k, 0) > 0) {
            if (gmp_testbit($k, 0)) {
                $result = $this->pointAdd($result, $addend, $mod);
            }

            $addend = $this->pointDouble($addend, $mod);
            $k = gmp_div_q($k, 2);
        }

        return $result;
    }

    private function mod(\GMP $value, \GMP $mod): \GMP
    {
        $result = gmp_mod($value, $mod);
        if ($result === false) {
            throw new InvalidArgumentException('Modulo operation failed');
        }

        if (gmp_cmp($result, 0) < 0) {
            $result = gmp_add($result, $mod);
        }

        return $result;
    }
}
