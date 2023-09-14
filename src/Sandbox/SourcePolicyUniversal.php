<?php

namespace Twig\Sandbox;
use Twig\Sandbox\SecurityPolicyInterface;
class SourcePolicyUniversal implements SourcePolicyInterface {
    private $policy;
    public function __construct(SecurityPolicyInterface $policy) {
        $this->policy = $policy;
    }
    public function getPolicy($source = null) : SecurityPolicyInterface {
        return $this->policy;
    }
}