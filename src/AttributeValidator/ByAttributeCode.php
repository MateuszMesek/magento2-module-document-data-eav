<?php declare(strict_types=1);

namespace MateuszMesek\DocumentDataEav\AttributeValidator;

use Magento\Eav\Api\Data\AttributeInterface;
use Magento\Framework\ObjectManager\TMap;
use Magento\Framework\ObjectManager\TMapFactory;
use MateuszMesek\DocumentDataEavApi\AttributeValidatorInterface;

class ByAttributeCode implements AttributeValidatorInterface
{
    private AttributeValidatorInterface $defaultValidator;
    private TMap $validatorsByAttributeCode;

    public function __construct(
        AttributeValidatorInterface $defaultValidator,
        TMapFactory $TMapFactory,
        array $validatorsByAttributeCode = []
    )
    {
        $this->defaultValidator = $defaultValidator;
        $this->validatorsByAttributeCode = $TMapFactory->createSharedObjectsMap([
            'type' => AttributeValidatorInterface::class,
            'array' => $validatorsByAttributeCode
        ]);
    }

    public function validate(AttributeInterface $attribute): bool
    {
        $attributeCode = $attribute->getAttributeCode();

        $validator = $this->validatorsByAttributeCode[$attributeCode] ?? $this->defaultValidator;

        return $validator->validate($attribute);
    }
}
