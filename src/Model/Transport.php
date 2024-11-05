<?php

declare(strict_types=1);

namespace Infrangible\Smtp\Model;

use Infrangible\Core\Helper\Registry;
use Infrangible\Core\Helper\Stores;
use Laminas\Mail\Header\HeaderInterface;
use Laminas\Mail\Message;
use Laminas\Mail\Transport\Smtp;
use Laminas\Mail\Transport\SmtpOptions;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Mail\TransportInterface;

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

    /** @var string */
    private $serverName;

    /** @var Message */
    private $message;

    public function __construct(
        Stores $storeHelper,
        Registry $registryHelper,
        RequestInterface $request
    ) {
        $this->storeHelper = $storeHelper;
        $this->registryHelper = $registryHelper;
        $this->request = $request;
    }

    protected function getServerName(): string
    {
        return $this->serverName;
    }

    public function setServerName(string $serverName): void
    {
        $this->serverName = $serverName;
    }

    public function getMessage(): ?Message
    {
        return $this->message;
    }

    public function prepareMessage(TransportInterface $subject): Message
    {
        $this->message = Message::fromString($subject->getMessage()->getRawMessage());

        $this->message->setEncoding('utf-8');

        $contentDisposition = $this->message->getHeaders()->get('Content-Disposition');

        if ($contentDisposition instanceof HeaderInterface) {
            $contentDisposition->setEncoding('ASCII');
        }

        $isSetReturnPath =
            (int)$this->storeHelper->getStoreConfig(\Magento\Email\Model\Transport::XML_PATH_SENDING_SET_RETURN_PATH);

        $returnPathValue =
            $this->storeHelper->getStoreConfig(\Magento\Email\Model\Transport::XML_PATH_SENDING_RETURN_PATH_EMAIL);

        if (2 === $isSetReturnPath && $returnPathValue) {
            $this->message->setSender($returnPathValue);
        } elseif (1 === $isSetReturnPath && $this->message->getFrom()->count()) {
            $fromAddressList = $this->message->getFrom();
            $fromAddressList->rewind();
            /** @noinspection PhpParamsInspection */
            $this->message->setSender($fromAddressList->current()->getEmail());
        }

        return $this->message;
    }

    public function send(): void
    {
        $smtpOptions = new SmtpOptions();

        if ($this->registryHelper->registry('smtp_test')) {
            $password = $this->request->getParam(
                sprintf(
                    'infrangible_smtp_%s_password',
                    $this->getServerName()
                )
            );
            if ($password === '******') {
                $password = $this->storeHelper->getStoreConfig(
                    sprintf(
                        'infrangible_smtp/%s/password',
                        $this->getServerName()
                    )
                );
            }

            $smtpOptions->setHost(
                $this->request->getParam(
                    sprintf(
                        'infrangible_smtp_%s_host',
                        $this->getServerName()
                    )
                )
            );
            $smtpOptions->setPort(
                $this->request->getParam(
                    sprintf(
                        'infrangible_smtp_%s_port',
                        $this->getServerName()
                    )
                )
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
                    sprintf(
                        'infrangible_smtp_%s_ssl',
                        $this->getServerName()
                    )
                ),
                'username' => $this->request->getParam(
                    sprintf(
                        'infrangible_smtp_%s_username',
                        $this->getServerName()
                    )
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
                    sprintf(
                        'infrangible_smtp/%s/ssl',
                        $this->getServerName()
                    )
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

        $transport->send($this->message);
    }
}
