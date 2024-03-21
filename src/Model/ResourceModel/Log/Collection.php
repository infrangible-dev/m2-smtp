<?php

namespace Infrangible\Smtp\Model\ResourceModel\Log;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Infrangible\Smtp\Model\Log;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Collection
    extends AbstractCollection
{
    /**
     * @return void
     */
    public function _construct()
    {
        $this->_init(Log::class, \Infrangible\Smtp\Model\ResourceModel\Log::class);
    }
}
