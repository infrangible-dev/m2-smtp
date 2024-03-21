<?php

namespace Infrangible\Smtp\Block\Adminhtml\Log;

use Infrangible\Smtp\Model\Log;
use Magento\Framework\Data\Form;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class View
    extends \Infrangible\BackendWidget\Block\View
{
    /**
     * @param Form $form
     */
    protected function prepareFields(Form $form)
    {
        $fieldSet = $form->addFieldset('general', ['legend' => __('General')]);

        $this->addTextField($fieldSet, 'from', __('From'), true);
        $this->addTextField($fieldSet, 'sender', __('Sender'), true);
        $this->addTextField($fieldSet, 'to', __('To'), true);
        $this->addTextField($fieldSet, 'cc', __('CC'), true);
        $this->addTextField($fieldSet, 'bcc', __('BCC'), true);
        $this->addTextField($fieldSet, 'reply_to', __('Reply-To'), true);
        $this->addTextField($fieldSet, 'subject', __('Subject'), true);
        $this->addTextField($fieldSet, 'encoding', __('Encoding'), true);
        $this->addTextField($fieldSet, 'type', __('Type'), true);
        $this->addIframeButtonField($fieldSet, 'body', __('Content'), __('View'), 'infrangible_smtp/log/content');
        $this->addOptionsField($fieldSet, 'status', __('Status'), [
            ['value' => Log::STATUS_NEW, 'label' => 'New'],
            ['value' => Log::STATUS_SUCCESS, 'label' => 'Success'],
            ['value' => Log::STATUS_ERROR, 'label' => 'Error']
        ],                     Log::STATUS_NEW, true);
        $this->addTextareaField($fieldSet, 'message', __('Message'), true);
        $this->addDateIsoField($fieldSet, 'created_at', __('Created At'), true, true);
        $this->addDateIsoField($fieldSet, 'updated_at', __('Updated At'), true, true);
    }
}
