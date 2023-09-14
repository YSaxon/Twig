<?php

namespace Twig\Sandbox;
use Twig\Sandbox\SecurityPolicyInterface;
use Twig\Source;

interface SourcePolicyInterface
{
    /**
     * @param Source $source
     *
     */
    public function getPolicy(Source $source = null) : SecurityPolicyInterface;

}