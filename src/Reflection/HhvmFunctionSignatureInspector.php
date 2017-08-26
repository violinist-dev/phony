<?php

declare(strict_types=1);

namespace Eloquent\Phony\Reflection;

use ReflectionFunctionAbstract;

/**
 * Inspects functions to determine their signature under HHVM.
 *
 * @codeCoverageIgnore
 */
class HhvmFunctionSignatureInspector implements FunctionSignatureInspector
{
    /**
     * Construct a new function signature inspector.
     *
     * @param FeatureDetector $featureDetector The feature detector to use.
     */
    public function __construct(FeatureDetector $featureDetector)
    {
        $this->isIterableTypeHintSupported = $featureDetector
            ->isSupported('type.iterable');
        $this->isObjectTypeHintSupported = $featureDetector
            ->isSupported('type.object');
    }

    /**
     * Get the function signature of the supplied function.
     *
     * @param ReflectionFunctionAbstract $function The function.
     *
     * @return array<string,array<string>> The function signature.
     */
    public function signature(ReflectionFunctionAbstract $function): array
    {
        $signature = [];

        foreach ($function->getParameters() as $parameter) {
            $name = $parameter->getName();

            if ($typehint = $parameter->getTypehintText()) {
                switch ($typehint) {
                    case 'array':
                    case 'callable':
                        $typehint .= ' ';

                        break;

                    case 'iterable':
                        if ($this->isIterableTypeHintSupported) {
                            $typehint .= ' ';
                        } else {
                            $typehint = '\\' . $typehint . ' ';
                        }

                        break;

                    case 'object':
                        if ($this->isObjectTypeHintSupported) {
                            $typehint .= ' ';
                        } else {
                            $typehint = '\\' . $typehint . ' ';
                        }

                        break;

                    default:
                        $typehint = '\\' . $typehint . ' ';
                }
            }

            $byReference = $parameter->isPassedByReference() ? '&' : '';

            if ($parameter->isVariadic()) {
                $variadic = '...';
            } else {
                $variadic = '';
            }

            $defaultValue = $parameter->getDefaultValueText();

            if ('' !== $defaultValue) {
                if ('NULL' === $defaultValue) {
                    $defaultValue = ' = null';
                } elseif (
                    'PHP_INT_MAX' === $defaultValue ||
                    'PHP_INT_MIN' === $defaultValue
                ) {
                    $typehint = '';
                    $defaultValue = ' = ' . $defaultValue;
                } else {
                    $defaultValue = eval('return ' . $defaultValue . ';');

                    if ('\HH\float ' === $typehint) {
                        $defaultValue = sprintf(' = %f', $defaultValue);
                    } else {
                        $defaultValue = ' = ' . var_export($defaultValue, true);
                    }
                }
            }

            $signature[$name] =
                [$typehint, $byReference, $variadic, $defaultValue];
        }

        return $signature;
    }

    private $isIterableTypeHintSupported;
    private $isObjectTypeHintSupported;
}
