<?php declare(strict_types=1);

namespace MateuszMesek\DocumentDataEav\Model\AttributeValidator;

use Magento\Eav\Api\Data\AttributeInterface;
use MateuszMesek\DocumentDataEavApi\Model\AttributeValidatorInterface;

class DefaultValidator implements AttributeValidatorInterface
{
    public function validate(AttributeInterface $attribute): bool
    {
        if ($attribute->getIsVisibleOnFront()) {
            return true;
        }

        if ($attribute->getUsedInProductListing() || $attribute->getIsFilterable()) {
            return true;
        }

        return false;
    }
}
