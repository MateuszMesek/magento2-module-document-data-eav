<?php declare(strict_types=1);

namespace MateuszMesek\DocumentDataEav;

use Generator;
use Magento\Eav\Api\Data\AttributeInterface;
use Magento\Eav\Model\Config as EavConfig;
use MateuszMesek\DocumentDataEavApi\AttributeValidatorInterface;
use MateuszMesek\DocumentDataIndexerMview\NodeSubscription\Attribute\QueryBuilder;
use MateuszMesek\DocumentDataIndexerMviewApi\NodeSubscriptionsResolverInterface;

class DocumentNodeSubscriptionsResolver implements NodeSubscriptionsResolverInterface
{
    private EavConfig $eavConfig;
    private string $entityType;
    private AttributeValidatorInterface $attributeValidator;

    public function __construct(
        EavConfig $eavConfig,
        string $entityType,
        AttributeValidatorInterface $attributeValidator
    )
    {
        $this->eavConfig = $eavConfig;
        $this->entityType = $entityType;
        $this->attributeValidator = $attributeValidator;
    }

    public function resolve(): Generator
    {
        /** @var AttributeInterface[] $attributes */
        $attributes = $this->eavConfig->getEntityAttributes($this->entityType);

        foreach ($attributes as $attribute) {
            if (!$this->attributeValidator->validate($attribute)) {
                continue;
            }

            $id = "attribute_{$this->entityType}_{$attribute->getAttributeCode()}";

            yield $attribute->getAttributeCode() => [
                $id => [
                    'id' => $id,
                    'type' => QueryBuilder::class,
                    'arguments' => [
                        $this->entityType,
                        $attribute->getAttributeCode(),
                    ]
                ]
            ];
        }
    }
}
