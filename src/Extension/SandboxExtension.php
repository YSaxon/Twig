<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Twig\Extension;

use Twig\NodeVisitor\SandboxNodeVisitor;
use Twig\Sandbox\SecurityNotAllowedMethodError;
use Twig\Sandbox\SecurityNotAllowedPropertyError;
use Twig\Sandbox\SecurityPolicyInterface;
use Twig\Sandbox\SourcePolicyInterface;
use Twig\Sandbox\SourcePolicyUniversal;
use Twig\Source;
use Twig\TokenParser\SandboxTokenParser;

final class SandboxExtension extends AbstractExtension
{
    private $sandboxedGlobally;
    private $sandboxed;
    private $sourcePolicy;

    public function __construct($policy, $sandboxed = false)
    {
        $this->setSecurityPolicy($policy);
        $this->sandboxedGlobally = $sandboxed;
    }

    public function getTokenParsers()
    {
        return [new SandboxTokenParser()];
    }

    public function getNodeVisitors()
    {
        return [new SandboxNodeVisitor()];
    }

    public function enableSandbox()
    {
        $this->sandboxed = true;
    }

    public function disableSandbox()
    {
        $this->sandboxed = false;
    }

    public function isSandboxed()
    {
        return $this->sandboxedGlobally || $this->sandboxed;
    }

    public function isSandboxedGlobally()
    {
        return $this->sandboxedGlobally;
    }

    public function setSecurityPolicy($policy)
    {
        if ($policy instanceof SecurityPolicyInterface) {
            $this->sourcePolicy = new SourcePolicyUniversal($policy);
        }
        elseif ($policy instanceof SourcePolicyInterface) {
            $this->sourcePolicy = $policy;
        }
        else {
            throw new \InvalidArgumentException(sprintf('First argument of "%s" must be an instance of "%s" or "%s", "%s" given.', __METHOD__, SecurityPolicyInterface::class, SourcePolicyInterface::class, \get_class($policy)));
        }
    }

    public function getSecurityPolicy()
    {
        if ($this->sourcePolicy instanceof SourcePolicyUniversal) {
            return $this->sourcePolicy->getPolicy(null);
        }
        throw new \LogicException('Security policy varies by source.');
        // return $this->sourcePolicy;
    }

    public function checkSecurity($tags, $filters, $functions, Source $source = null)
    {
        if ($this->isSandboxed()) {
            $this->sourcePolicy->getPolicy($source)->checkSecurity($tags, $filters, $functions);
        }
    }

    public function checkMethodAllowed($obj, $method, int $lineno = -1, Source $source = null)
    {
        if ($this->isSandboxed()) {
            try {
                $this->sourcePolicy->getPolicy($source)->checkMethodAllowed($obj, $method);
            } catch (SecurityNotAllowedMethodError $e) {
                $e->setSourceContext($source);
                $e->setTemplateLine($lineno);

                throw $e;
            }
        }
    }

    public function checkPropertyAllowed($obj, $property, int $lineno = -1, Source $source = null)
    {
        if ($this->isSandboxed()) {
            try {
                $this->sourcePolicy->getPolicy($source)->checkPropertyAllowed($obj, $property);
            } catch (SecurityNotAllowedPropertyError $e) {
                $e->setSourceContext($source);
                $e->setTemplateLine($lineno);

                throw $e;
            }
        }
    }

    public function ensureToStringAllowed($obj, int $lineno = -1, Source $source = null)
    {
        if ($this->isSandboxed() && \is_object($obj) && method_exists($obj, '__toString')) {
            try {
                $this->sourcePolicy->getPolicy($source)->checkMethodAllowed($obj, '__toString');
            } catch (SecurityNotAllowedMethodError $e) {
                $e->setSourceContext($source);
                $e->setTemplateLine($lineno);

                throw $e;
            }
        }

        return $obj;
    }
}

class_alias('Twig\Extension\SandboxExtension', 'Twig_Extension_Sandbox');
