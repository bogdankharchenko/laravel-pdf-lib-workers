<?php

/**
 * Generated from openapi.json (pdfmill 0.3.0, sha256 793485a9b985).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfMill\Operations;

use BogdanKharchenko\PdfMill\Contracts\Operation;
use BogdanKharchenko\PdfMill\Data\Permissions;
use BogdanKharchenko\PdfMill\Enums\EncryptAlgorithm;
use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

/**
 * Password-protects the PDF. Applied when the file is saved.
 */
final class Encrypt extends Data implements Operation
{
    /** Names this step in the operations list: always "encrypt". */
    #[Computed]
    public readonly string $op;

    /**
     * @param  string  $ownerPassword  Gives full access.
     * @param  string|Optional  $userPassword  Needed to open the file. Empty or omitted: opens without a password, permissions still apply.
     * @param  EncryptAlgorithm|Optional  $algorithm  Default: "AES-256".
     * @param  bool|Optional  $allowWeakCryptography  Required for RC4, which is broken; only for viewers older than 2005.
     * @param  Permissions|Optional  $permissions  What user-password holders may do. Everything is allowed unless set to false.
     */
    public function __construct(
        public readonly string $ownerPassword,
        public readonly string|Optional $userPassword = new Optional(),
        public readonly EncryptAlgorithm|Optional $algorithm = new Optional(),
        public readonly bool|Optional $allowWeakCryptography = new Optional(),
        public readonly Permissions|Optional $permissions = new Optional(),
    ) {
        $this->op = 'encrypt';
    }
}
