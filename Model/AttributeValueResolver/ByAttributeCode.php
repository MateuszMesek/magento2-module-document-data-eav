<?php declare(strict_types=1);

namespace MateuszMesek\DocumentDataEav\Model\AttributeValueResolver;

use Magento\Eav\Api\Data\AttributeInterface;
use Magento\Framework\ObjectManager\TMap;
use Magento\Framework\ObjectManager\TMapFactory;
use MateuszMesek\DocumentDataApi\Model\InputInterface;
use MateuszMesek\DocumentDataEavApi\Model\AttributeValueResolverInterface;

class ByAttributeCode implements AttributeValueResolverInterface
{
    private TMap $resolversByAttributeCode;

    public function __construct(
        private readonly AttributeValueResolverInterface $defaultResolver,
        TMapFactory $TMapFactory,
        array $resolversByAttributeCode = []
    )
    {
        $this->resolversByAttributeCode = $TMapFactory->createSharedObjectsMap([
            'type' => AttributeValueResolverInterface::class,
            'array' => $resolversByAttributeCode
        ]);
    }

    public function resolver(AttributeInterface $attribute, InputInterface $input): mixed
    {
        $attributeCode = $attribute->getAttributeCode();

        $resolver = $this->resolversByAttributeCode[$attributeCode] ?? $this->defaultResolver;

        return $resolver->resolver($attribute, $input);
    }
}
