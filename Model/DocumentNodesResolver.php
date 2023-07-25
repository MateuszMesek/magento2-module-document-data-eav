<?php declare(strict_types=1);

namespace MateuszMesek\DocumentDataEav\Model;

use Generator;
use Magento\Eav\Api\Data\AttributeInterface;
use Magento\Eav\Model\Config as EavConfig;
use MateuszMesek\DocumentData\Model\Command\GetDocumentNodeValue\ResolverFactory;
use MateuszMesek\DocumentData\Model\Data\DocumentNodeFactory;
use MateuszMesek\DocumentDataApi\Model\DocumentNodesResolverInterface;
use MateuszMesek\DocumentDataApi\Model\InputInterface;
use MateuszMesek\DocumentDataEavApi\Model\AttributeReturnTypeResolverInterface;
use MateuszMesek\DocumentDataEavApi\Model\AttributeValidatorInterface;
use MateuszMesek\DocumentDataEavApi\Model\AttributeValueResolverInterface;

class DocumentNodesResolver implements DocumentNodesResolverInterface
{
    public function __construct(
        private readonly DocumentNodeFactory                  $documentNodeFactory,
        private readonly string                               $documentName,
        private readonly EavConfig                            $eavConfig,
        private readonly string                               $entityType,
        private readonly AttributeValidatorInterface          $attributeValidator,
        private readonly ResolverFactory                      $resolverFactory,
        private readonly AttributeValueResolverInterface      $attributeValueResolver,
        private readonly AttributeReturnTypeResolverInterface $attributeReturnTypeResolver
    )
    {
    }

    public function resolve(): Generator
    {
        /** @var AttributeInterface[] $attributes */
        $attributes = $this->eavConfig->getEntityAttributes($this->entityType);

        foreach ($attributes as $attribute) {
            if (!$this->attributeValidator->validate($attribute)) {
                continue;
            }

            $callback = function (InputInterface $input) use ($attribute) {
                $value = $this->attributeValueResolver->resolver($attribute, $input);

                if (null === $value) {
                    return null;
                }

                return $this->attributeReturnTypeResolver->resolver($attribute, $value);
            };

            yield $this->documentNodeFactory->create([
                'documentName' => $this->documentName,
                'path' => $attribute->getAttributeCode(),
                'valueResolver' => $this->resolverFactory->create($callback)
            ]);
        }
    }
}
