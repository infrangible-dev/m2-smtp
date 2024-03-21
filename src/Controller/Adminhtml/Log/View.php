<?php

namespace Infrangible\Smtp\Controller\Adminhtml\Log;

use Infrangible\Smtp\Traits\Log;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class View
    extends \Infrangible\BackendWidget\Controller\Backend\Object\View
{
    use Log;

    /**
     * @return string
     */
    protected function getObjectNotFoundMessage(): string
    {
        return __('Could not find log!');
    }
}
