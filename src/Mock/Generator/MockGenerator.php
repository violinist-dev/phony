<?php

/*
 * This file is part of the Phony package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Phony\Mock\Generator;

use Eloquent\Phony\Mock\Builder\Definition\Method\MethodDefinitionInterface;
use Eloquent\Phony\Mock\Builder\MockBuilder;
use Eloquent\Phony\Mock\Builder\MockBuilderInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionFunctionAbstract;
use ReflectionParameter;

/**
 * Generates mock classes.
 *
 * @internal
 */
class MockGenerator implements MockGeneratorInterface
{
    /**
     * Get the static instance of this generator.
     *
     * @return MockGeneratorInterface The static generator.
     */
    public static function instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Construct a new mock generator.
     */
    public function __construct()
    {
        $reflectorReflector = new ReflectionClass('ReflectionParameter');
        $this->isCallableTypeHintSupported =
            $reflectorReflector->hasMethod('isCallable');
        $this->isParameterConstantSupported =
            $reflectorReflector->hasMethod('isDefaultValueConstant');
    }

    /**
     * Generate a mock class and return the source code.
     *
     * @param MockBuilderInterface $builder The builder.
     *
     * @return string The source code.
     */
    public function generate(MockBuilderInterface $builder)
    {
        $builder->finalize();

        return $this->generateHeader($builder) .
            $this->generateConstants($builder) .
            $this->generateMethods(
                $builder->methodDefinitions()->publicStaticMethods()
            ) .
            $this->generateMagicCallStatic($builder) .
            $this->generateConstructors($builder) .
            $this->generateMethods(
                $builder->methodDefinitions()->publicMethods()
            ) .
            $this->generateMagicCall($builder) .
            $this->generateMethods(
                $builder->methodDefinitions()->protectedStaticMethods()
            ) .
            $this->generateMethods(
                $builder->methodDefinitions()->protectedMethods()
            ) .
            $this->generateCallParentMethods($builder) .
            $this->generateProperties($builder) .
            "\n}\n";
    }

    /**
     * Generate the class header.
     *
     * @param MockBuilderInterface $builder The builder.
     *
     * @return string The source code.
     */
    protected function generateHeader(MockBuilderInterface $builder)
    {
        $template = <<<'EOD'
%s/**
 * A mock class generated by Phony.%s
 *
 * This file is part of the Phony package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with the Phony source code.
 *
 * @link https://github.com/eloquent/phony
 */
class %s
EOD;

        if ($types = $builder->types()) {
            $usedTypes = "\n *";

            foreach ($types as $type) {
                $usedTypes .= sprintf("\n * @uses \%s", $type);
            }
        } else {
            $usedTypes = '';
        }

        $className = $builder->className();
        $classNameParts = explode('\\', $className);

        if (count($classNameParts) > 1) {
            $className = array_pop($classNameParts);
            $namespace =
                sprintf("namespace %s;\n\n", implode('\\', $classNameParts));
        } else {
            $namespace = '';
        }

        $source = sprintf($template, $namespace, $usedTypes, $className);

        $parentClassName = $builder->parentClassName();
        $interfaceNames = $builder->interfaceNames();
        $traitNames = $builder->traitNames();

        if (null !== $parentClassName) {
            $source .= sprintf("\nextends \%s", $parentClassName);
        }

        array_unshift($interfaceNames, 'Eloquent\Phony\Mock\MockInterface');
        $source .= sprintf(
            "\nimplements \%s",
            implode(",\n           \\", $interfaceNames)
        );

        $source .= "\n{";

        if ($traitNames) {
            foreach ($traitNames as $traitName) {
                $source .= sprintf("\n    use \%s;", $traitName);
            }

            $source .= "\n";
        }

        return $source;
    }

    /**
     * Generate the class constants.
     *
     * @param MockBuilderInterface $builder The builder.
     *
     * @return string The source code.
     */
    protected function generateConstants(MockBuilderInterface $builder)
    {
        $constants = $builder->constants();
        $source = '';

        if ($constants) {
            foreach ($constants as $name => $value) {
                $source .= sprintf(
                    "\n    const %s = %s;",
                    $name,
                    $this->renderValue($value)
                );
            }

            $source .= "\n";
        }

        return $source;
    }

    /**
     * Generate the __callStatic() method.
     *
     * @param MockBuilderInterface $builder The builder.
     *
     * @return string The source code.
     */
    protected function generateMagicCallStatic(MockBuilderInterface $builder)
    {
        $methods = $builder->methodDefinitions()->publicStaticMethods();

        if (!isset($methods['__callStatic'])) {
            return '';
        }

        $body = <<<'EOD'
        $arguments = new \Eloquent\Phony\Call\Argument\Arguments($a1);

        if (isset(self::$_magicStaticStubs[$a0])) {
            return self::$_magicStaticStubs[$a0]->invokeWith($arguments);
        }

        return self::_callMagicStatic($a0, $arguments);
EOD;

        return $this->generateMethod($methods['__callStatic'], $body);
    }

    /**
     * Generate the constructors.
     *
     * @param MockBuilderInterface $builder The builder.
     *
     * @return string The source code.
     */
    protected function generateConstructors(MockBuilderInterface $builder)
    {
        $className = $builder->parentClassName();

        if (null === $className) {
            $constructor = null;
        } else {
            $reflectors = $builder->reflectors();
            $constructor = $reflectors[$className]->getConstructor();
        }

        if (!$constructor) {
            return '';
        }

        return <<<'EOD'

    /**
     * Construct a mock.
     */
    public function __construct()
    {
    }

EOD;
    }

    /**
     * Generate the supplied methods
     *
     * @param array<string,MethodDefinitionInterface> The methods.
     *
     * @return string The source code.
     */
    protected function generateMethods(array $methods)
    {
        $source = '';

        foreach ($methods as $method) {
            if (
                '__call' === $method->name() ||
                '__callStatic' === $method->name()
            ) {
                continue;
            }

            if ($method->isStatic()) {
                $body = <<<'EOD'
        $argumentCount = func_num_args();
        $arguments = array();%s

        for ($i = %d; $i < $argumentCount; $i++) {
            $arguments[] = func_get_arg($i);
        }

        if (isset(self::$_staticStubs[__FUNCTION__])) {
            return self::$_staticStubs[__FUNCTION__]->invokeWith(
                new \Eloquent\Phony\Call\Argument\Arguments($arguments)
            );
        }
EOD;
            } else {
                $body = <<<'EOD'
        $argumentCount = func_num_args();
        $arguments = array();%s

        for ($i = %d; $i < $argumentCount; $i++) {
            $arguments[] = func_get_arg($i);
        }

        if (isset($this->_stubs[__FUNCTION__])) {
            return $this->_stubs[__FUNCTION__]->invokeWith(
                new \Eloquent\Phony\Call\Argument\Arguments($arguments)
            );
        }
EOD;
            }

            $parameters = $method->method()->getParameters();

            if ($method->isCustom()) {
                array_shift($parameters);
            }

            if ($parameters) {
                $argumentPacking = "\n";

                foreach ($parameters as $index => $parameter) {
                    if ($parameter->isPassedByReference()) {
                        $reference = '&';
                    } else {
                        $reference = '';
                    }

                    $argumentPacking .= sprintf(
                        "\n        if (\$argumentCount > %d) " .
                            "\$arguments[] = %s\$a%d;",
                        $index,
                        $reference,
                        $index
                    );
                }
            } else {
                $argumentPacking = '';
            }

            $source .= $this->generateMethod(
                $method,
                sprintf($body, $argumentPacking, count($parameters))
            );
        }

        return $source;
    }

    /**
     * Generate the supplied method.
     *
     * @param MethodDefinitionInterface $method The method.
     * @param string                    $body   The method body.
     *
     * @return string The source code.
     */
    protected function generateMethod(MethodDefinitionInterface $method, $body)
    {
        $source = '';

        if ($method->isCustom()) {
            $commentTemplate = <<<'EOD'
    /**
     * Custom method '%s'.%s
     */
EOD;
        } else {
            $commentTemplate = <<<'EOD'
    /**
     * Inherited method '%%s'.
     *
     * @uses \%s::%s()%%s
     */
EOD;
            $commentTemplate = sprintf(
                $commentTemplate,
                $method->method()->getDeclaringClass()->getName(),
                $method->method()->getName()
            );
        }

        $comment = sprintf(
            $commentTemplate,
            $method->name(),
            $this->renderParametersDocumentation(
                $method->method(),
                $method->isCustom()
            )
        );

        if ($method->isStatic()) {
            $scope = 'static ';
        } else {
            $scope = '';
        }

        return sprintf(
            "\n%s\n    %s %sfunction %s%s%s\n    }\n",
            $comment,
            $method->accessLevel(),
            $scope,
            $method->name(),
            $this->renderParameters($method->method(), $method->isCustom()),
            $body
        );
    }

    /**
     * Generate the __call() method.
     *
     * @param MockBuilderInterface $builder The builder.
     *
     * @return string The source code.
     */
    protected function generateMagicCall(MockBuilderInterface $builder)
    {
        $methods = $builder->methodDefinitions()->publicMethods();

        if (!isset($methods['__call'])) {
            return '';
        }

        $body = <<<'EOD'
        $arguments = new \Eloquent\Phony\Call\Argument\Arguments($a1);

        if (isset($this->_magicStubs[$a0])) {
            return $this->_magicStubs[$a0]->invokeWith($arguments);
        }

        return self::_callMagic($a0, $arguments);
EOD;

        return $this->generateMethod($methods['__call'], $body);
    }

    /**
     * Generate the call parent methods.
     *
     * @param MockBuilderInterface $builder The builder.
     *
     * @return string The source code.
     */
    protected function generateCallParentMethods(MockBuilderInterface $builder)
    {
        $source = <<<'EOD'

    /**
     * Call a static parent method.
     *
     * @param string                                           $name      The method name.
     * @param \Eloquent\Phony\Call\Argument\ArgumentsInterface $arguments The arguments.
     */
    private static function _callParentStatic(
        $name,
        \Eloquent\Phony\Call\Argument\ArgumentsInterface $arguments
    ) {
        return \call_user_func_array(
            array(__CLASS__, 'parent::' . $name),
            $arguments->all()
        );
    }

EOD;

        $methods = $builder->methodDefinitions()->publicStaticMethods();

        if (isset($methods['__callStatic'])) {
            $source .= <<<'EOD'

    /**
     * Perform a magic call via the __callStatic stub.
     *
     * @param string                                           $name      The method name.
     * @param \Eloquent\Phony\Call\Argument\ArgumentsInterface $arguments The arguments.
     */
    private static function _callMagicStatic(
        $name,
        \Eloquent\Phony\Call\Argument\ArgumentsInterface $arguments
    ) {
        if (isset(self::$_staticStubs['__callStatic'])) {
            return self::$_staticStubs['__callStatic']
                ->invoke($name, $arguments->all());
        }
    }

EOD;
        }

        $source .= <<<'EOD'

    /**
     * Call a parent method.
     *
     * @param string                                           $name      The method name.
     * @param \Eloquent\Phony\Call\Argument\ArgumentsInterface $arguments The arguments.
     */
    private function _callParent(
        $name,
        \Eloquent\Phony\Call\Argument\ArgumentsInterface $arguments
    ) {
        return \call_user_func_array(
            array($this, 'parent::' . $name),
            $arguments->all()
        );
    }

EOD;

        $methods = $builder->methodDefinitions()->publicMethods();

        if (isset($methods['__call'])) {
            $source .= <<<'EOD'

    /**
     * Perform a magic call via the __call stub.
     *
     * @param string                                           $name      The method name.
     * @param \Eloquent\Phony\Call\Argument\ArgumentsInterface $arguments The arguments.
     */
    private function _callMagic(
        $name,
        \Eloquent\Phony\Call\Argument\ArgumentsInterface $arguments
    ) {
        if (isset($this->_stubs['__call'])) {
            return $this->_stubs['__call']->invoke($name, $arguments->all());
        }
    }

EOD;
        }

        return $source;
    }

    /**
     * Generate the properties.
     *
     * @param MockBuilderInterface $builder The builder.
     *
     * @return string The source code.
     */
    protected function generateProperties(MockBuilderInterface $builder)
    {
        $staticProperties = $builder->staticProperties();
        $properties = $builder->properties();
        $source = '';

        foreach ($staticProperties as $name => $value) {
            $source .= sprintf(
                "\n    public static \$%s = %s;",
                $name,
                $this->renderValue($value)
            );
        }

        foreach ($properties as $name => $value) {
            $source .= sprintf(
                "\n    public \$%s = %s;",
                $name,
                $this->renderValue($value)
            );
        }

        $source .= <<<'EOD'

    private static $_staticStubs = array();
    private static $_magicStaticStubs = array();
    private $_stubs = array();
    private $_magicStubs = array();
    private $_mockId;
EOD;

        return $source;
    }

    /**
     * Render a parameter list compatible with the supplied function reflector.
     *
     * @param ReflectionFunctionAbstract $function            The function.
     * @param boolean                    $stripFirstParameter True if the first parameter should not be rendered.
     *
     * @return string The rendered parameter list.
     */
    protected function renderParameters(
        ReflectionFunctionAbstract $function,
        $stripFirstParameter
    ) {
        $parameters = $function->getParameters();

        if ($stripFirstParameter) {
            array_shift($parameters);
        }

        foreach ($parameters as $index => $parameter) {
            $renderedParameters[] =
                $this->renderParameter($index, $parameter);
        }

        if ($parameters) {
            return sprintf(
                "(\n        %s\n    ) {\n",
                implode(",\n        ",
                    $renderedParameters)
            );
        }

        return "()\n    {\n";
    }

    /**
     * Render a parameter compatible with the supplied parameter reflector.
     *
     * @param integer             $index     The index at which the parameter appears.
     * @param ReflectionParameter $parameter The reflector.
     *
     * @return string The rendered parameter.
     */
    protected function renderParameter($index, ReflectionParameter $parameter)
    {
        $typeHint = $this->parameterType($parameter);

        if ('mixed' === $typeHint) {
            $typeHint = '';
        } else {
            $typeHint .= ' ';
        }

        if ($parameter->isPassedByReference()) {
            $reference = '&';
        } else {
            $reference = '';
        }

        if ($parameter->isOptional()) {
            if (!$parameter->isDefaultValueAvailable()) {
                $defaultValue = 'null';
            } elseif (
                $this->isParameterConstantSupported &&
                $parameter->isDefaultValueConstant()
            ) {
                $constantName = $parameter->getDefaultValueConstantName();

                if ('self:' === substr($constantName, 0, 5)) {
                    $constantName = $parameter->getDeclaringClass()->getName() .
                        substr($constantName, 4);
                }

                $defaultValue = '\\' . $constantName;
            } else {
                $defaultValue =
                    $this->renderValue($parameter->getDefaultValue());
            }

            $defaultValue = sprintf(' = %s', $defaultValue);
        } else {
            $defaultValue = '';
        }

        return
            sprintf('%s%s$a%d%s', $typeHint, $reference, $index, $defaultValue);
    }

    /**
     * Render parameter documentation for a function reflector.
     *
     * @param ReflectionFunctionAbstract $function            The function.
     * @param boolean                    $stripFirstParameter True if the first parameter should not be rendered.
     *
     * @return string The rendered documentation.
     */
    protected function renderParametersDocumentation(
        ReflectionFunctionAbstract $function,
        $stripFirstParameter
    ) {
        $parameters = $function->getParameters();

        if ($stripFirstParameter) {
            array_shift($parameters);
        }

        if (!$parameters) {
            return '';
        }

        $renderedParameters = array();
        $columnWidths = array(0, 0, 0);

        foreach ($parameters as $index => $parameter) {
            $renderedParameter =
                $this->renderParameterDocumentation($index, $parameter);

            foreach ($renderedParameter as $columnIndex => $value) {
                $size = strlen($value);

                if ($size > $columnWidths[$columnIndex]) {
                    $columnWidths[$columnIndex] = $size;
                }
            }

            $renderedParameters[] = $renderedParameter;
        }

        $rendered = "\n     *";

        foreach ($renderedParameters as $renderedParameter) {
            $rendered .= sprintf(
                "\n     * @param %s %s %s",
                str_pad($renderedParameter[0], $columnWidths[0]),
                str_pad($renderedParameter[1], $columnWidths[1]),
                $renderedParameter[2]
            );
        }

        return $rendered;
    }

    /**
     * Render documentation for a parameter.
     *
     * @param integer             $index     The index at which the parameter appears.
     * @param ReflectionParameter $parameter The reflector.
     *
     * @return tuple<string,string,string> A 3-tuple of rendered type, name, and description.
     */
    protected function renderParameterDocumentation(
        $index,
        ReflectionParameter $parameter
    ) {
        $typeHint = $this->parameterType($parameter);

        if ('mixed' !== $typeHint && $parameter->allowsNull()) {
            $typeHint .= '|null';
        }

        if ($parameter->isPassedByReference()) {
            $name = '&$a' . $index;
        } else {
            $name = '$a' . $index;
        }

        $description = sprintf(
            'Was %s.',
            var_export($parameter->getName(), true)
        );

        return array($typeHint, $name, $description);
    }

    /**
     * Determine a parameter's type.
     *
     * @param ReflectionParameter $parameter The parameter.
     *
     * @return string The type.
     */
    protected function parameterType(ReflectionParameter $parameter)
    {
        if ($parameter->isArray()) {
            return 'array';
        } elseif (
            $this->isCallableTypeHintSupported && $parameter->isCallable()
        ) {
            return 'callable';
        } else {
            try {
                if ($class = $parameter->getClass()) {
                    return '\\' . $class->getName();
                }
            } catch (ReflectionException $e) {
                if (
                    !$parameter->getDeclaringFunction()->isInternal() &&
                    preg_match(
                        sprintf(
                            '/Class (%s) does not exist/',
                            MockBuilder::SYMBOL_PATTERN
                        ),
                        $e->getMessage(),
                        $matches
                    )
                ) {
                    return '\\' . $matches[1];
                }
            }
        }

        return 'mixed';
    }

    /**
     * Render the supplied value.
     *
     * This method does not support recursive values, which will result in an
     * infinite loop.
     *
     * @param mixed $value The value.
     *
     * @return string The rendered value.
     */
    protected function renderValue($value)
    {
        if (null === $value) {
            return 'null';
        }

        if (is_array($value)) {
            $isSequence = array_keys($value) === range(0, count($value) - 1);

            $values = array();

            if ($isSequence) {
                foreach ($value as $subValue) {
                    $values[] = $this->renderValue($subValue);
                }
            } else {
                foreach ($value as $key => $subValue) {
                    $values[] = sprintf(
                        '%s => %s',
                        $this->renderValue($key),
                        $this->renderValue($subValue)
                    );
                }
            }

            return sprintf('array(%s)', implode(', ', $values));
        }

        return var_export($value, true);
    }

    protected $isCallableTypeHintSupported;
    protected $isParameterConstantSupported;
    private static $instance;
}
