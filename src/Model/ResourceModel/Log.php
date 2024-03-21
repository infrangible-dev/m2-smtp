<?php

namespace Infrangible\Smtp\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Log
    extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('smtp_log', 'log_id');
    }

    /**
     * @param AbstractModel $object
     *
     * @return Log
     * @throws \Exception
     */
    protected function _beforeSave(AbstractModel $object): Log
    {
        /** @var \Infrangible\Smtp\Model\Log $object */
        if ($object->getId()) {
            $object->setUpdatedAt(gmdate('Y-m-d H:i:s'));
        } else {
            $object->setCreatedAt(gmdate('Y-m-d H:i:s'));
        }

        return parent::_beforeSave($object);
    }
}
