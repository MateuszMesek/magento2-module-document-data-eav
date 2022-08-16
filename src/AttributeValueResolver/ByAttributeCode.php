<?php declare(strict_types=1);

namespace MateuszMesek\DocumentDataEav\AttributeValueResolver;

use Magento\Eav\Api\Data\AttributeInterface;
use Magento\Framework\ObjectManager\TMap;
use Magento\Framework\ObjectManager\TMapFactory;
use MateuszMesek\DocumentDataEavApi\AttributeValueResolverInterface;

class ByAttributeCode implements AttributeValueResolverInterface
{
    private AttributeValueResolverInterface $defaultResolver;
    private TMap $resolversByAttributeCode;

    public function __construct(
        AttributeValueResolverInterface $defaultResolver,
        TMapFactory $TMapFactory,
        array $resolversByAttributeCode = []
    )
    {
        $this->defaultResolver = $defaultResolver;
        $this->resolversByAttributeCode = $TMapFactory->createSharedObjectsMap([
            'type' => AttributeValueResolverInterface::class,
            'array' => $resolversByAttributeCode
        ]);
    }

    public function resolver(AttributeInterface $attribute, $value)
    {
        $attributeCode = $attribute->getAttributeCode();

        $resolver = $this->resolversByAttributeCode[$attributeCode] ?? $this->defaultResolver;

        return $resolver->resolver($attribute, $value);
    }
}
