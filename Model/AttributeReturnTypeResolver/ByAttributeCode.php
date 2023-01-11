<?php declare(strict_types=1);

namespace MateuszMesek\DocumentDataEav\Model\AttributeReturnTypeResolver;

use Magento\Eav\Api\Data\AttributeInterface;
use Magento\Framework\ObjectManager\TMap;
use Magento\Framework\ObjectManager\TMapFactory;
use MateuszMesek\DocumentDataEavApi\Model\AttributeReturnTypeResolverInterface;

class ByAttributeCode implements AttributeReturnTypeResolverInterface
{
    private AttributeReturnTypeResolverInterface $defaultResolver;
    private TMap $resolversByAttributeCode;

    public function __construct(
        AttributeReturnTypeResolverInterface $defaultResolver,
        TMapFactory $TMapFactory,
        array $resolversByAttributeCode = []
    )
    {
        $this->defaultResolver = $defaultResolver;
        $this->resolversByAttributeCode = $TMapFactory->createSharedObjectsMap([
            'type' => AttributeReturnTypeResolverInterface::class,
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
