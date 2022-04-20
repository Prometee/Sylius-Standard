<?php

declare(strict_types=1);

namespace App\Graphviz\Dumper;

/**
 * DumperInterface is the interface implemented by workflow dumper classes.
 */
interface DumperInterface
{
    /**
     * Dumps a workflow definition.
     *
     *
     * @return string The representation of the workflow
     */
    public function dump(array $config, array $marking = null, array $options = []);
}
