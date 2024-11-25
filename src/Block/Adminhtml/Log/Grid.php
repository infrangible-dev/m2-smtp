<?php

declare(strict_types=1);

namespace Infrangible\Smtp\Block\Adminhtml\Log;

use Exception;
use Infrangible\Smtp\Block\Adminhtml\Widget\Grid\Column\Renderer\LogBcc;
use Infrangible\Smtp\Block\Adminhtml\Widget\Grid\Column\Renderer\LogCc;
use Infrangible\Smtp\Block\Adminhtml\Widget\Grid\Column\Renderer\LogSubject;
use Infrangible\Smtp\Block\Adminhtml\Widget\Grid\Column\Renderer\LogTo;
use Infrangible\Smtp\Model\Log;
use Magento\Framework\Data\Collection\AbstractDb;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Grid extends \Infrangible\BackendWidget\Block\Grid
{
    /** @var string */
    protected $_defaultSort = 'updated_at';

    protected function prepareCollection(AbstractDb $collection): void
    {
    }

    /**
     * @throws Exception
     */
    protected function prepareFields(): void
    {
        $this->addTextColumn(
            'from',
            __('From')->render()
        );
        $this->addTextColumn(
            'sender',
            __('Sender')->render()
        );
        $this->addTextColumnWithRenderer(
            'to',
            __('To')->render(),
            LogTo::class
        );
        $this->addTextColumnWithRenderer(
            'cc',
            __('CC')->render(),
            LogCc::class
        );
        $this->addTextColumnWithRenderer(
            'bcc',
            __('BCC')->render(),
            LogBcc::class
        );
        $this->addTextColumn(
            'reply_to',
            __('Reply-To')->render()
        );
        $this->addTextColumnWithRenderer(
            'subject',
            __('Subject')->render(),
            LogSubject::class
        );
        $this->addTextColumn(
            'encoding',
            __('Encoding')->render()
        );
        $this->addTextColumn(
            'type',
            __('Type')->render()
        );
        $this->addOptionsColumn(
            'status',
            __('Status')->render(),
            [Log::STATUS_NEW => __('New'), Log::STATUS_SUCCESS => __('Success'), Log::STATUS_ERROR => __('Error')]
        );
        $this->addDatetimeColumn(
            'created_at',
            __('Created At')->render()
        );
        $this->addDatetimeColumn(
            'updated_at',
            __('Updated At')->render()
        );
    }

    /**
     * @return string[]
     */
    protected function getHiddenFieldNames(): array
    {
        return ['sender', 'cc', 'bcc', 'reply_to', 'encoding', 'type', 'created_at'];
    }
}
