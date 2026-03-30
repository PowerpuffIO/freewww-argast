<?php

declare(strict_types=1);


/**
 * This website is provided free of charge.
 * Author: Powerpuff — https://powerpuff.pro/
 * Discord: https://discord.gg/QwCsWtP99A
 * GitHub: https://github.com/PowerpuffIO
 */

final class Srp6
{
    private \GMP $g;
    private \GMP $n;

    public function __construct()
    {
        $this->g = gmp_init(7);
        $this->n = gmp_init('894B645E89E1535BBDAD5B8B290650530801B18EBFBF5E8FAB3C82872A3E9BB7', 16);
    }

    public function calculateVerifier(string $username, string $password, string $salt): string
    {
        $h1 = sha1(strtoupper($username . ':' . $password), true);
        $h2 = sha1($salt . $h1, true);
        $h2g = gmp_import($h2, 1, GMP_LSW_FIRST);
        $verifier = gmp_powm($this->g, $h2g, $this->n);
        $raw = gmp_export($verifier, 1, GMP_LSW_FIRST);
        if (strlen($raw) > 32) {
            $raw = substr($raw, 0, 32);
        }

        return str_pad($raw, 32, "\0", STR_PAD_RIGHT);
    }

    public function getRegistrationData(string $username, string $password): array
    {
        $salt = random_bytes(32);
        $verifier = $this->calculateVerifier($username, $password, $salt);

        return [$salt, $verifier];
    }
}
