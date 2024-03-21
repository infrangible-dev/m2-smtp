<?php

namespace Infrangible\Smtp\Block\Adminhtml\Log;

use Exception;
use Infrangible\Smtp\Model\Log;
use Magento\Framework\Data\Collection\AbstractDb;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Grid
    extends \Infrangible\BackendWidget\Block\Grid
{
    /** @var string */
    protected $_defaultSort = 'updated_at';

    /**
     * @param AbstractDb $collection
     *
     * @return void
     */
    protected function prepareCollection(AbstractDb $collection)
    {
    }

    /**
     * @return void
     * @throws Exception
     */
    protected function prepareFields()
    {
        $this->addTextColumn('from', __('From'));
        $this->addTextColumn('sender', __('Sender'));
        $this->addTextColumn('to', __('To'));
        $this->addTextColumn('cc', __('CC'));
        $this->addTextColumn('bcc', __('BCC'));
        $this->addTextColumn('reply_to', __('Reply-To'));
        $this->addTextColumn('subject', __('Subject'));
        $this->addTextColumn('encoding', __('Encoding'));
        $this->addTextColumn('type', __('Type'));
        $this->addOptionsColumn(
            'status',
            __('Status'),
            [Log::STATUS_NEW => __('New'), Log::STATUS_SUCCESS => __('Success'), Log::STATUS_ERROR => __('Error')]
        );
        $this->addDatetimeColumn('created_at', __('Created At'));
        $this->addDatetimeColumn('updated_at', __('Updated At'));
    }

    /**
     * @return string[]
     */
    protected function getHiddenFieldNames(): array
    {
        return ['sender', 'cc', 'bcc', 'encoding', 'type'];
    }
}
