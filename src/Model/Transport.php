<?php

namespace Infrangible\Smtp\Model;

use Exception;
use FeWeDev\Base\Variables;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Mail\TransportInterface;
use Magento\Framework\Phrase;
use Infrangible\Core\Helper\Registry;
use Infrangible\Core\Helper\Stores;
use Laminas\Mail\Address;
use Laminas\Mail\AddressList;
use Laminas\Mail\Header\ContentType;
use Laminas\Mail\Header\HeaderInterface;
use Laminas\Mail\Message;
use Laminas\Mail\Transport\Smtp;
use Laminas\Mail\Transport\SmtpOptions;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Transport
{
    /** @var Stores */
    protected $storeHelper;

    /** @var Registry */
    protected $registryHelper;

    /** @var RequestInterface */
    protected $request;

    /** @var LogFactory */
    protected $logFactory;

    /** @var \Infrangible\Smtp\Model\ResourceModel\LogFactory */
    protected $logResourceFactory;

    /** @var Variables */
    protected $variables;

    /** @var string */
    private $serverName;

    /**
     * @param Stores                                           $storeHelper
     * @param Registry                                         $registryHelper
     * @param RequestInterface                                 $request
     * @param LogFactory                                       $logFactory
     * @param \Infrangible\Smtp\Model\ResourceModel\LogFactory $logResourceFactory
     * @param Variables                                        $variables
     */
    public function __construct(
        Stores $storeHelper,
        Registry $registryHelper,
        RequestInterface $request,
        LogFactory $logFactory,
        \Infrangible\Smtp\Model\ResourceModel\LogFactory $logResourceFactory,
        Variables $variables
    ) {
        $this->storeHelper = $storeHelper;
        $this->registryHelper = $registryHelper;

        $this->request = $request;
        $this->logFactory = $logFactory;
        $this->logResourceFactory = $logResourceFactory;
        $this->variables = $variables;
    }

    /**
     * @return string
     */
    protected function getServerName(): string
    {
        return $this->serverName;
    }

    /**
     * @param string $serverName
     */
    public function setServerName(string $serverName)
    {
        $this->serverName = $serverName;
    }

    /**
     * @param TransportInterface $subject
     *
     * @throws MailException
     * @throws AlreadyExistsException
     */
    public function send(TransportInterface $subject)
    {
        $zendMessage = Message::fromString($subject->getMessage()->getRawMessage());

        $zendMessage->setEncoding('utf-8');

        $contentDisposition = $zendMessage->getHeaders()->get('Content-Disposition');

        if ($contentDisposition instanceof HeaderInterface) {
            $contentDisposition->setEncoding('ASCII');
        }

        $isSetReturnPath =
            (int) $this->storeHelper->getStoreConfig(\Magento\Email\Model\Transport::XML_PATH_SENDING_SET_RETURN_PATH);

        $returnPathValue =
            $this->storeHelper->getStoreConfig(\Magento\Email\Model\Transport::XML_PATH_SENDING_RETURN_PATH_EMAIL);

        if (2 === $isSetReturnPath && $returnPathValue) {
            $zendMessage->setSender($returnPathValue);
        } elseif (1 === $isSetReturnPath && $zendMessage->getFrom()->count()) {
            $fromAddressList = $zendMessage->getFrom();
            $fromAddressList->rewind();
            /** @noinspection PhpParamsInspection */
            $zendMessage->setSender($fromAddressList->current()->getEmail());
        }

        $smtpOptions = new SmtpOptions();

        if ($this->registryHelper->registry('smtp_test')) {
            $password = $this->request->getParam(sprintf('infrangible_smtp_%s_password', $this->getServerName()));
            if ($password === '******') {
                $password =
                    $this->storeHelper->getStoreConfig(sprintf('infrangible_smtp/%s/password', $this->getServerName()));
            }

            $smtpOptions->setHost(
                $this->request->getParam(sprintf('infrangible_smtp_%s_host', $this->getServerName()))
            );
            $smtpOptions->setPort(
                $this->request->getParam(sprintf('infrangible_smtp_%s_port', $this->getServerName()))
            );
            $smtpOptions->setConnectionClass(
                $this->request->getParam(
                    sprintf(
                        'infrangible_smtp_%s_connection_class',
                        $this->getServerName()
                    )
                )
            );
            $smtpOptions->setConnectionConfig([
                                                  'ssl'      => $this->request->getParam(
                                                      sprintf('infrangible_smtp_%s_ssl', $this->getServerName())
                                                  ),
                                                  'username' => $this->request->getParam(
                                                      sprintf('infrangible_smtp_%s_username', $this->getServerName())
                                                  ),
                                                  'password' => $password
                                              ]);
        } else {
            $smtpOptions->setHost(
                $this->storeHelper->getStoreConfig(
                    sprintf(
                        'infrangible_smtp/%s/host',
                        $this->getServerName()
                    )
                )
            );
            $smtpOptions->setPort(
                $this->storeHelper->getStoreConfig(
                    sprintf(
                        'infrangible_smtp/%s/port',
                        $this->getServerName()
                    )
                )
            );
            $smtpOptions->setConnectionClass(
                $this->storeHelper->getStoreConfig(
                    sprintf(
                        'infrangible_smtp/%s/connection_class',
                        $this->getServerName()
                    )
                )
            );
            $smtpOptions->setConnectionConfig([
                                                  'ssl'      => $this->storeHelper->getStoreConfig(
                                                      sprintf('infrangible_smtp/%s/ssl', $this->getServerName())
                                                  ),
                                                  'username' => $this->storeHelper->getStoreConfig(
                                                      sprintf(
                                                          'infrangible_smtp/%s/username',
                                                          $this->getServerName()
                                                      )
                                                  ),
                                                  'password' => $this->storeHelper->getStoreConfig(
                                                      sprintf(
                                                          'infrangible_smtp/%s/password',
                                                          $this->getServerName()
                                                      )
                                                  )
                                              ]);
        }

        $transport = new Smtp($smtpOptions);

        $log = $this->logFactory->create();

        $fromAddresses = $zendMessage->getFrom();

        $fromList = [];

        if ($fromAddresses instanceof AddressList) {
            /** @var Address $fromAddress */
            foreach ($fromAddresses as $fromAddress) {
                $fromList[] = $fromAddress->toString();
            }
        } else {
            $fromList[] = $this->variables->stringValue($fromAddresses);
        }

        $senderAddresses = $zendMessage->getSender();

        $senderList = [];

        if ($senderAddresses instanceof AddressList) {
            /** @var Address $senderAddress */
            foreach ($senderAddresses as $senderAddress) {
                $senderList[] = $senderAddress->toString();
            }
        } else {
            $senderList[] = $this->variables->stringValue($senderAddresses);
        }

        $toAddresses = $zendMessage->getTo();

        $toList = [];

        if ($toAddresses instanceof AddressList) {
            /** @var Address $toAddress */
            foreach ($toAddresses as $toAddress) {
                $toList[] = $toAddress->toString();
            }
        } else {
            $toList[] = $this->variables->stringValue($toAddresses);
        }

        $ccAddresses = $zendMessage->getCc();

        $ccList = [];

        if ($ccAddresses instanceof AddressList) {
            /** @var Address $ccAddress */
            foreach ($ccAddresses as $ccAddress) {
                $ccList[] = $ccAddress->toString();
            }
        } else {
            $ccList[] = $this->variables->stringValue($ccAddresses);
        }

        $bccAddresses = $zendMessage->getBcc();

        $bccList = [];

        if ($bccAddresses instanceof AddressList) {
            /** @var Address $bccAddress */
            foreach ($bccAddresses as $bccAddress) {
                $bccList[] = $bccAddress->toString();
            }
        } else {
            $bccList[] = $this->variables->stringValue($bccAddresses);
        }

        $replyToAddresses = $zendMessage->getBcc();

        $replyToList = [];

        if ($replyToAddresses instanceof AddressList) {
            /** @var Address $replyToAddress */
            foreach ($replyToAddresses as $replyToAddress) {
                $replyToList[] = $replyToAddress->toString();
            }
        } else {
            $replyToList[] = $this->variables->stringValue($replyToAddresses);
        }

        $body = $zendMessage->getBody();

        if ($body instanceof \Laminas\Mime\Message) {
            $parts = $body->getParts();

            if (count($parts) === 1) {
                $part = reset($parts);
                $type = $part->getType();
            } else {
                $type = 'multi-part';
            }
        } else {
            $type = 'unknown';

            $headers = $zendMessage->getHeaders();

            foreach ($headers as $header) {
                if ($header instanceof ContentType) {
                    $type = $header->getType();
                }
            }
        }

        $log->setFrom(implode('; ', $fromList));
        $log->setSender(implode('; ', $senderList));
        $log->setTo(implode('; ', $toList));
        $log->setCc(implode('; ', $ccList));
        $log->setBcc(implode('; ', $bccList));
        $log->setReplyTo(implode('; ', $replyToList));
        $log->setSubject($zendMessage->getSubject());
        $log->setEncoding($zendMessage->getEncoding());
        $log->setType($type);
        $log->setBody($zendMessage->getBodyText());
        $log->setStatus(Log::STATUS_NEW);

        $logResource = $this->logResourceFactory->create();

        $logResource->save($log);

        try {
            $transport->send($zendMessage);

            $log->setStatus(Log::STATUS_SUCCESS);

            $logResource->save($log);
        } catch (Exception $exception) {
            $log->setStatus(Log::STATUS_ERROR);
            $log->setMessage($exception->getMessage());

            $logResource->save($log);

            throw new MailException(new Phrase($exception->getMessage()), $exception);
        }
    }
}
