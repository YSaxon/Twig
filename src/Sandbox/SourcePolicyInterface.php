<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Twig\Sandbox;
use Twig\Source;

/**
 * Interface that all source policy classes must implements.
 *
 * @author Yaakov Saxon
 */
interface SourcePolicyInterface
{
    /**
     * @param Source $source
     *
     */
    // public function alwaysSandboxSource(Source $source) : bool;
    // public function neverSandboxSource(Source $source) : bool;
    public function overrideSandboxEnabled(Source $source) : bool|null;

}
