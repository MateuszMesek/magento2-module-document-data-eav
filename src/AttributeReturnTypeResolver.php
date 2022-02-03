<?php declare(strict_types=1);

namespace MateuszMesek\DocumentEav;

use Magento\Eav\Api\Data\AttributeInterface;
use MateuszMesek\DocumentEavApi\AttributeReturnTypeResolverInterface;

class AttributeReturnTypeResolver implements AttributeReturnTypeResolverInterface
{
    public function resolver(AttributeInterface $attribute, $value)
    {
        switch ($attribute->getBackendType()) {
            case 'static':
                break;

            case 'decimal':
                $value = (float)$value;
                break;

            case 'int':
                $value = (int)$value;
                break;

            case 'varchar':
            case 'text':
                if (!is_array($value)) {
                    $value = (string)$value;
                }
                break;

            default:
                var_dump($attribute->getBackendType());
                exit;
        }


        switch ($attribute->getFrontendInput()) {
            case 'image':
            case 'price':
            case 'select':
            case 'text':
            case 'textarea':
            case 'weight':
                break;

            case 'boolean':
                $value = (bool)$value;
                break;

            case 'multiselect':
                $value = explode(',', $value);
                $intValues = array_filter(
                    $value,
                    static function ($value) {
                        return (string)$value === (string)(int)$value;
                    }
                );

                if (count($intValues) === count($value)) {
                    $value = array_map(
                        static function ($value) {
                            return (int)$value;
                        },
                        $value
                    );
                }
                break;

            default:
                var_dump($attribute->getFrontendInput());
                exit;
        }

        return $value;
    }
}
