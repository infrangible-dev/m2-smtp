<?php

declare(strict_types=1);

namespace Infrangible\Smtp\Controller\Adminhtml\Smtp;

use FeWeDev\Base\Arrays;
use FeWeDev\Base\Json;
use Infrangible\Core\Controller\Adminhtml\Ajax;
use Infrangible\Core\Helper\Registry;
use Infrangible\Core\Helper\Stores;
use Infrangible\SimpleMail\Model\MailFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\MailException;
use Psr\Log\LoggerInterface;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Mail extends Ajax
{
    /** @var Stores */
    protected $storeHelper;

    /** @var Registry */
    protected $registryHelper;

    /** @var MailFactory */
    protected $mailFactory;

    public function __construct(
        Arrays $arrays,
        Json $json,
        Stores $storeHelper,
        Registry $registryHelper,
        Context $context,
        LoggerInterface $logging,
        MailFactory $mailFactory
    ) {
        parent::__construct(
            $arrays,
            $json,
            $context,
            $logging
        );

        $this->storeHelper = $storeHelper;
        $this->registryHelper = $registryHelper;

        $this->mailFactory = $mailFactory;
    }

    /**
     * @return void
     */
    public function execute()
    {
        $senderIdentity = $this->_request->getParam('infrangible_smtp_test_mail_sender');
        $senderEMail = $this->storeHelper->getStoreConfig(
            sprintf(
                'trans_email/ident_%s/email',
                $senderIdentity
            )
        );
        $senderName = $this->storeHelper->getStoreConfig(
            sprintf(
                'trans_email/ident_%s/name',
                $senderIdentity
            )
        );
        $receiver = $this->_request->getParam('infrangible_smtp_test_mail_receiver');

        $this->addResponseValue(
            'content',
            'Whoa!'
        );

        $mail = $this->mailFactory->create();

        $mail->addSender(
            $senderEMail,
            $senderName
        );
        if (strpos(
                $receiver,
                ';'
            ) !== false) {
            foreach (explode(
                ';',
                $receiver
            ) as $email) {
                $mail->addReceiver($email);
            }
        } else {
            $mail->addReceiver($receiver);
        }
        $mail->setType('text/plain');
        $mail->setSubject('SMTP Mail Test');
        $mail->setBody(__('This is a test message.')->render());

        try {
            $this->registryHelper->register(
                'smtp_test',
                true
            );

            $mail->send();

            $this->setSuccessResponse(__('The message was successfully sent.')->render());
        } catch (MailException $exception) {
            $this->setErrorResponse($exception->getMessage());
        }
    }
}
