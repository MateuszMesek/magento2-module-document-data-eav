<?php declare(strict_types=1);

namespace MateuszMesek\DocumentDataEav\Observer\MessageQueuePoisonPill;

use Magento\Eav\Api\Data\AttributeInterface;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\MessageQueue\PoisonPill\PoisonPillPutInterface;

class OnCommitAfter implements ObserverInterface
{
    public function __construct(
        private readonly PoisonPillPutInterface $poisonPillPut
    )
    {
    }

    public function execute(EventObserver $observer)
    {
        $event = $observer->getEvent();

        $object = $event->getData('object');

        if (!$object instanceof AttributeInterface) {
            return;
        }

        $this->poisonPillPut->put();
    }
}
