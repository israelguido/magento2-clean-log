<?php
/**
 *  script by israelguido@gmail.com
 */
namespace Israel\CleanLog\Cron;

use Psr\Log\LoggerInterface;

class Cleanlog
{
    protected $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
    
    public function execute()
    {
        $this->logger->info('Db Log cleaning cron started..!');
        $objectManager  = \Magento\Framework\App\ObjectManager::getInstance();
        $resource       = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection     = $resource->getConnection();

        $tablesToTurncate = [
            'report_event',
            'report_viewed_product_index',
            'report_compared_product_index',
            'customer_visitor'
        ];
        foreach ($tablesToTurncate as $_key => $value) {
            $tableName  = $resource->getTableName($value);
            $sql        = "TRUNCATE " . $tableName;

            try {
                $connection->query($sql);
                $this->logger->info($tableName . ' cleaned up.');
            } catch (\Exception $e) {
                $this->logger->critical($e);
            }
        }
        $this->logger->info('Db Log cleaning cron ended..!');

        return $this;
    }
}
