<?php

declare(strict_types=1);

namespace BogdanKharchenko\PdfMill\Contracts;

/**
 * One step of an `operations` list. Every class in the Operations namespace
 * implements it; each is a laravel-data object whose "op" field names the step.
 */
interface Operation {}
