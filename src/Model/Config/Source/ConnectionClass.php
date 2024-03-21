<?php

namespace Infrangible\Smtp\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class ConnectionClass
    implements OptionSourceInterface
{
    /**
     * @return array[]
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => 'smtp', 'label' => 'smtp'],
            ['value' => 'plain', 'label' => 'plain'],
            ['value' => 'login', 'label' => 'login'],
            ['value' => 'crammd5', 'label' => 'crammd5']
        ];
    }
}
