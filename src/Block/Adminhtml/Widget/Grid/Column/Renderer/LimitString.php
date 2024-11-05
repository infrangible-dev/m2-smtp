<?php

declare(strict_types=1);

namespace Infrangible\Smtp\Block\Adminhtml\Widget\Grid\Column\Renderer;

use FeWeDev\Base\Strings;
use Infrangible\Smtp\Model\Log;
use Magento\Backend\Block\Context;
use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\DataObject;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
abstract class LimitString extends AbstractRenderer
{
    /** @var Strings */
    protected $strings;

    public function __construct(Context $context, Strings $strings, array $data = [])
    {
        parent::__construct(
            $context,
            $data
        );

        $this->strings = $strings;
    }

    public function render(DataObject $row): string
    {
        /** @var Log $row */
        return htmlentities($this->strings->cutString(
            $this->getValue($row),
            $this->getSize()
        ));
    }

    abstract protected function getValue(Log $row): string;

    abstract protected function getSize(): int;
}
