<?php

namespace BitTools\SkyHub\Helper\Sales;

use BitTools\SkyHub\Helper\AbstractHelper;
use Magento\Sales\Model\Order as SalesOrder;

class Order extends AbstractHelper
{

    /**
     * @param $skyhubCode
     *
     * @return int
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getOrderId($skyhubCode)
    {
        /** @var \BitTools\SkyHub\Model\ResourceModel\Sales\Order $orderResource */
        $orderResource = $this->objectManager()->create(\BitTools\SkyHub\Model\ResourceModel\Sales\Order::class);
        $orderId       = $orderResource->getEntityIdBySkyhubCode($skyhubCode);

        return $orderId;
    }


    /**
     * @param string $code
     *
     * @return int
     */
    public function getNewOrderIncrementId($code)
    {
        /**
         * @todo Adapt this code to use the current configuration scope.
         */
        /*
        $useDefaultIncrementId = $this->getSkyHubModuleConfig('use_default_increment_id', 'cron_sales_order_import');

        if (!$useDefaultIncrementId) {
            return $code;
        }
        */

        return null;
    }


    /**
     * @param int $orderId (entity_id)
     *
     * @return string
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getOrderIncrementId($orderId)
    {
        /** @var \BitTools\SkyHub\Model\ResourceModel\Sales\Order $orderResource */
        $orderResource = $this->objectManager()->create(\BitTools\SkyHub\Model\ResourceModel\Sales\Order::class);
        $skyhubCode    = $orderResource->getSkyhubCodeByOrderId($orderId);

        return $skyhubCode;
    }
    
    
    /**
     * @return \Magento\Sales\Model\ResourceModel\Order\Collection
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getPendingOrdersFromSkyHub()
    {
        /** @var \BitTools\SkyHub\Model\ResourceModel\Sales\Order $orderResource */
        $orderResource = $this->objectManager()->create(\BitTools\SkyHub\Model\ResourceModel\Order::class);
        
        $deniedStates = [
            SalesOrder::STATE_CANCELED,
            SalesOrder::STATE_CLOSED,
            SalesOrder::STATE_COMPLETE,
        ];

        /** @var \Magento\Sales\Model\ResourceModel\Order\Collection $collection */
        $collection = $this->objectManager()->create(\Magento\Sales\Model\ResourceModel\Order\Collection::class);
        $collection->join(['bso' => $orderResource->getMainTable()], 'bso.order_id = main_table.entity_id');
        $collection->addFieldToFilter('state', ['nin' => $deniedStates]);

        return $collection;
    }
}
