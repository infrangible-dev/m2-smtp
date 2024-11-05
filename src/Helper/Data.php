<?php

declare(strict_types=1);

namespace Infrangible\Smtp\Helper;

use FeWeDev\Base\Variables;
use Infrangible\Smtp\Model\Log;
use Infrangible\Smtp\Model\LogFactory;
use Laminas\Mail\Address;
use Laminas\Mail\AddressList;
use Laminas\Mail\Header\ContentType;
use Laminas\Mail\Message;
use Magento\Framework\Exception\AlreadyExistsException;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Data
{
    /** @var LogFactory */
    protected $logFactory;

    /** @var \Infrangible\Smtp\Model\ResourceModel\LogFactory */
    protected $logResourceFactory;

    /** @var Variables */
    protected $variables;

    public function __construct(
        LogFactory $logFactory,
        \Infrangible\Smtp\Model\ResourceModel\LogFactory $logResourceFactory,
        Variables $variables
    ) {
        $this->logFactory = $logFactory;
        $this->logResourceFactory = $logResourceFactory;
        $this->variables = $variables;
    }

    /**
     * @throws AlreadyExistsException
     */
    public function createMessageLog(Message $message): Log
    {
        $fromAddresses = $message->getFrom();

        $fromList = [];

        if ($fromAddresses instanceof AddressList) {
            /** @var Address $fromAddress */
            foreach ($fromAddresses as $fromAddress) {
                $fromList[] = $fromAddress->toString();
            }
        } else {
            $fromList[] = $this->variables->stringValue($fromAddresses);
        }

        $senderAddresses = $message->getSender();

        $senderList = [];

        if ($senderAddresses instanceof AddressList) {
            /** @var Address $senderAddress */
            foreach ($senderAddresses as $senderAddress) {
                $senderList[] = $senderAddress->toString();
            }
        } else {
            $senderList[] = $this->variables->stringValue($senderAddresses);
        }

        $toAddresses = $message->getTo();

        $toList = [];

        if ($toAddresses instanceof AddressList) {
            /** @var Address $toAddress */
            foreach ($toAddresses as $toAddress) {
                $toList[] = $toAddress->toString();
            }
        } else {
            $toList[] = $this->variables->stringValue($toAddresses);
        }

        $ccAddresses = $message->getCc();

        $ccList = [];

        if ($ccAddresses instanceof AddressList) {
            /** @var Address $ccAddress */
            foreach ($ccAddresses as $ccAddress) {
                $ccList[] = $ccAddress->toString();
            }
        } else {
            $ccList[] = $this->variables->stringValue($ccAddresses);
        }

        $bccAddresses = $message->getBcc();

        $bccList = [];

        if ($bccAddresses instanceof AddressList) {
            /** @var Address $bccAddress */
            foreach ($bccAddresses as $bccAddress) {
                $bccList[] = $bccAddress->toString();
            }
        } else {
            $bccList[] = $this->variables->stringValue($bccAddresses);
        }

        $replyToAddresses = $message->getBcc();

        $replyToList = [];

        if ($replyToAddresses instanceof AddressList) {
            /** @var Address $replyToAddress */
            foreach ($replyToAddresses as $replyToAddress) {
                $replyToList[] = $replyToAddress->toString();
            }
        } else {
            $replyToList[] = $this->variables->stringValue($replyToAddresses);
        }

        $body = $message->getBody();

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

            $headers = $message->getHeaders();

            foreach ($headers as $header) {
                if ($header instanceof ContentType) {
                    $type = $header->getType();
                }
            }
        }

        return $this->createLog(
            $fromList,
            $senderList,
            $toList,
            $ccList,
            $bccList,
            $replyToList,
            $message->getSubject(),
            $message->getEncoding(),
            $type,
            $message->getBodyText()
        );
    }

    /**
     * @throws AlreadyExistsException
     */
    public function createLog(
        array $fromList,
        array $senderList,
        array $toList,
        array $ccList,
        array $bccList,
        array $replyToList,
        string $subject,
        string $encoding,
        string $type,
        string $body
    ): Log {
        $log = $this->logFactory->create();

        $log->setFrom(
            implode(
                '; ',
                $fromList
            )
        );
        $log->setSender(
            implode(
                '; ',
                $senderList
            )
        );
        $log->setTo(
            implode(
                '; ',
                $toList
            )
        );
        $log->setCc(
            implode(
                '; ',
                $ccList
            )
        );
        $log->setBcc(
            implode(
                '; ',
                $bccList
            )
        );
        $log->setReplyTo(
            implode(
                '; ',
                $replyToList
            )
        );
        $log->setSubject($subject);
        $log->setEncoding($encoding);
        $log->setType($type);
        $log->setBody($body);
        $log->setStatus(Log::STATUS_NEW);

        $logResource = $this->logResourceFactory->create();

        $logResource->save($log);

        return $log;
    }

    /**
     * @throws AlreadyExistsException
     */
    public function logMessageSuccess(Log $log): void
    {
        $log->setStatus(Log::STATUS_SUCCESS);

        $logResource = $this->logResourceFactory->create();

        $logResource->save($log);
    }

    /**
     * @throws AlreadyExistsException
     */
    public function logMesssageError(Log $log, string $errorMessage): void
    {
        $log->setStatus(Log::STATUS_ERROR);
        $log->setMessage($errorMessage);

        $logResource = $this->logResourceFactory->create();

        $logResource->save($log);
    }
}
