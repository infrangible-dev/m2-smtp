<?php

declare(strict_types=1);

namespace Infrangible\Smtp\Block\Adminhtml\Widget\Grid\Column\Renderer;

use Infrangible\Smtp\Model\Log;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class LogTo extends LimitString
{
    protected function getValue(Log $row): string
    {
        return $row->getTo() ? $row->getTo() : '';
    }

    protected function getSize(): int
    {
        return 80;
    }
}
