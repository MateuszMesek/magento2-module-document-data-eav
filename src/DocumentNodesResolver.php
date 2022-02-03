<?php declare(strict_types=1);

namespace MateuszMesek\DocumentEav;

use Generator;
use Magento\Eav\Api\Data\AttributeInterface;
use Magento\Eav\Model\Config as EavConfig;
use MateuszMesek\Document\Api\DocumentNodesResolverInterface;
use MateuszMesek\Document\Api\InputInterface;
use MateuszMesek\DocumentEavApi\AttributeReturnTypeResolverInterface;
use MateuszMesek\DocumentEavApi\AttributeValidatorInterface;
use MateuszMesek\DocumentEavApi\AttributeValueResolverInterface;

class DocumentNodesResolver implements DocumentNodesResolverInterface
{
    private EavConfig $eavConfig;
    private string $entityType;
    private AttributeValidatorInterface $attributeValidator;
    private AttributeValueResolverInterface $attributeValueResolver;
    private AttributeReturnTypeResolverInterface $attributeReturnTypeResolver;

    public function __construct(
        EavConfig $eavConfig,
        string $entityType,
        AttributeValidatorInterface $attributeValidator,
        AttributeValueResolverInterface $attributeValueResolver,
        AttributeReturnTypeResolverInterface $attributeReturnTypeResolver
    )
    {
        $this->eavConfig = $eavConfig;
        $this->entityType = $entityType;
        $this->attributeValidator = $attributeValidator;
        $this->attributeValueResolver = $attributeValueResolver;
        $this->attributeReturnTypeResolver = $attributeReturnTypeResolver;
    }

    public function resolve(): Generator
    {
        /** @var AttributeInterface[] $attributes */
        $attributes = $this->eavConfig->getEntityAttributes($this->entityType);

        foreach ($attributes as $attribute) {
            if (!$this->attributeValidator->validate($attribute)) {
                continue;
            }

            yield [
                'path' => $attribute->getAttributeCode(),
                'resolver' => function (InputInterface $input) use ($attribute) {
                    $value = $this->attributeValueResolver->resolver($attribute, $input);

                    if (null === $value) {
                        return null;
                    }

                    return $this->attributeReturnTypeResolver->resolver($attribute, $value);
                },
            ];
        }
    }
}
