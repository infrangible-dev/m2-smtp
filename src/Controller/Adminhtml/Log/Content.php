<?php

namespace Infrangible\Smtp\Controller\Adminhtml\Log;

use Infrangible\Smtp\Model\LogFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Forward;
use Magento\Framework\Controller\Result\Raw;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Content
    extends Action
{
    /** @var LogFactory */
    protected $logFactory;

    /** @var \Infrangible\Smtp\Model\ResourceModel\LogFactory */
    protected $logResourceFactory;

    /**
     * @param Context                                          $context
     * @param LogFactory                                       $logFactory
     * @param \Infrangible\Smtp\Model\ResourceModel\LogFactory $logResourceFactory
     */
    public function __construct(
        Context $context,
        LogFactory $logFactory,
        \Infrangible\Smtp\Model\ResourceModel\LogFactory $logResourceFactory
    ) {
        parent::__construct($context);

        $this->logFactory = $logFactory;
        $this->logResourceFactory = $logResourceFactory;
    }

    /**
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        $logId = $this->getRequest()->getParam('log_id');

        if ($logId) {
            $log = $this->logFactory->create();

            $this->logResourceFactory->create()->load($log, $logId);

            if ($log->getId()) {
                /** @var Raw $result */
                $result = $this->resultFactory->create(ResultFactory::TYPE_RAW);

                $result->setContents(quoted_printable_decode(htmlspecialchars_decode($log->getBody())));

                return $result;
            }
        }

        /** @var Forward $resultForward */
        $resultForward = $this->resultFactory->create(ResultFactory::TYPE_FORWARD);

        return $resultForward->forward('noroute');
    }
}
