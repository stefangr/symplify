<?php declare(strict_types=1);

namespace Symplify\TokenRunner\ReflectionDocBlock;

use Nette\Utils\Strings;
use phpDocumentor\Reflection\DocBlock\Tag;
use phpDocumentor\Reflection\DocBlock\Tags\Formatter;
use phpDocumentor\Reflection\DocBlock\Tags\Param;
use phpDocumentor\Reflection\DocBlock\Tags\Return_;
use phpDocumentor\Reflection\Types\Array_;
use Symplify\TokenRunner\DocBlock\ArrayResolver;
use Symplify\TokenRunner\ReflectionDocBlock\Tag\TolerantParam;
use Symplify\TokenRunner\ReflectionDocBlock\Tag\TolerantReturn;

/**
 * Keeps mixed[] as mixed[], not array
 */
final class CleanFormatter implements Formatter
{
    /**
     * @var string
     */
    private $originalContent;

    public function __construct(string $originalContent)
    {
        $this->originalContent = $originalContent;
    }

    public function format(Tag $tag): string
    {
        $tagTypeAndDescription = ltrim((string) $tag, '\\');

        if (($tag instanceof TolerantReturn || $tag instanceof TolerantParam) && $tag->getType() instanceof Array_) {
            $tagTypeAndDescription = $this->resolveAndFixArrayTypeIfNeeded($tag, $tagTypeAndDescription);
        }

        return trim('@' . $tag->getName() . ' ' . $tagTypeAndDescription);
    }

    /**
     * @param Param|Return_ $tag
     */
    private function resolveAndFixArrayTypeIfNeeded(Tag $tag, string $tagTypeAndDescription): string
    {
        $original = 'array';

        if ($tag instanceof TolerantParam) {
            $original = ArrayResolver::resolveArrayType(
                $this->originalContent,
                $tag->getType(),
                'param',
                $tag->getVariableName()
            );
        }

        if ($tag instanceof TolerantReturn) {
            $original = ArrayResolver::resolveArrayType($this->originalContent, $tag->getType(), 'return');
        }

        // possible mixed[] override
        if ($original !== 'array' && $original !== 'array[]' && Strings::contains($tagTypeAndDescription, 'array')) {
            $tagTypeAndDescription = substr_replace($tagTypeAndDescription, 'mixed[]', 0, strlen('array'));
        }

        return $tagTypeAndDescription;
    }
}
