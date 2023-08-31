<?php

declare(strict_types=1);

namespace Ekyna\Bundle\TableBundle\Model;

/**
 * Class Anchor
 * @package Ekyna\Bundle\TableBundle\Model
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class Anchor
{
    public function __construct(
        public mixed  $value,
        public string $label,
        public array  $attr = [
            'href' => 'javascript:void(0)',
        ],
    ) {
    }
}
