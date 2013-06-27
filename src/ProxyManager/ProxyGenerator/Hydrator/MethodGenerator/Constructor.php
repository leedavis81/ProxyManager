<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

namespace ProxyManager\ProxyGenerator\Hydrator\MethodGenerator;

use ProxyManager\Generator\MethodGenerator;
use ProxyManager\Generator\ParameterGenerator;
use Zend\Code\Generator\PropertyGenerator;

/**
 * Method generator for the constructor of a hydrator proxy
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 * @license MIT
 */
class Constructor extends MethodGenerator
{
    /**
     * @param \ProxyManager\ProxyGenerator\Hydrator\PropertyGenerator\PropertyAccessor[] $propertyAccessors
     */
    public function __construct(array $propertyAccessors)
    {
        parent::__construct('__construct');

        $this->setDocblock("@param \\ReflectionProperty[] \$propertyAccessors to hydrate private properties");
        //$this->setParameter(new ParameterGenerator('propertyAccessors', 'array'));

        $body = empty($propertyAccessors) ? '' : '$this->foobarbaztab = null; $values = & $this->foobarbaztab; ';

        foreach ($propertyAccessors as $propertyAccessor) {
            $originalProperty = $propertyAccessor->getOriginalProperty();

            $body .= '$this->' . $propertyAccessor->getName()
                . ' = \Closure::bind(function ($object) use ($values) { $object->'
                . $originalProperty->getName() . ' = $values['
                . var_export($originalProperty->getName(), true) . ']; }, null, '
                . var_export($originalProperty->getDeclaringClass()->getName(), true)
                . ");\n";
        }

        $this->setBody($body);
    }
}
