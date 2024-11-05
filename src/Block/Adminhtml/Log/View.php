<?php

declare(strict_types=1);

namespace Infrangible\Smtp\Block\Adminhtml\Log;

use Infrangible\Smtp\Model\Log;
use Magento\Framework\Data\Form;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class View extends \Infrangible\BackendWidget\Block\View
{
    protected function prepareFields(Form $form)
    {
        $fieldSet = $form->addFieldset(
            'general',
            ['legend' => __('General')]
        );

        $this->addTextField(
            $fieldSet,
            'from',
            __('From')->render(),
            true
        );
        $this->addTextField(
            $fieldSet,
            'sender',
            __('Sender')->render(),
            true
        );
        $this->addTextField(
            $fieldSet,
            'to',
            __('To')->render(),
            true
        );
        $this->addTextField(
            $fieldSet,
            'cc',
            __('CC')->render(),
            true
        );
        $this->addTextField(
            $fieldSet,
            'bcc',
            __('BCC')->render(),
            true
        );
        $this->addTextField(
            $fieldSet,
            'reply_to',
            __('Reply-To')->render(),
            true
        );
        $this->addTextField(
            $fieldSet,
            'subject',
            __('Subject')->render(),
            true
        );
        $this->addTextField(
            $fieldSet,
            'encoding',
            __('Encoding')->render(),
            true
        );
        $this->addTextField(
            $fieldSet,
            'type',
            __('Type')->render(),
            true
        );
        $this->addIframeButtonField(
            $fieldSet,
            'body',
            __('Content')->render(),
            __('View')->render(),
            'infrangible_smtp/log/content'
        );
        $this->addOptionsField(
            $fieldSet,
            'status',
            __('Status')->render(),
            [
                ['value' => Log::STATUS_NEW, 'label' => 'New'],
                ['value' => Log::STATUS_SUCCESS, 'label' => 'Success'],
                ['value' => Log::STATUS_ERROR, 'label' => 'Error']
            ],
            Log::STATUS_NEW,
            true
        );
        $this->addTextareaField(
            $fieldSet,
            'message',
            __('Message')->render(),
            true
        );
        $this->addDateIsoField(
            $fieldSet,
            'created_at',
            __('Created At')->render(),
            true,
            true
        );
        $this->addDateIsoField(
            $fieldSet,
            'updated_at',
            __('Updated At')->render(),
            true,
            true
        );
    }
}
