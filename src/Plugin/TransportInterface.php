<?php

namespace Infrangible\Smtp\Plugin;

use Closure;
use Exception;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Phrase;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Infrangible\Core\Helper\Registry;
use Infrangible\Core\Helper\Stores;
use Infrangible\Smtp\Model\Transport;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class TransportInterface
{
    /** @var Stores */
    protected $storeHelper;

    /** @var Registry */
    protected $registryHelper;

    /** @var LoggerInterface */
    protected $logging;

    /** @var Transport */
    protected $transport;

    /** @var RequestInterface */
    protected $request;

    /**
     * @param Stores           $storeHelper
     * @param Registry         $registryHelper
     * @param LoggerInterface  $logging
     * @param Transport        $transport
     * @param RequestInterface $request
     */
    public function __construct(
        Stores $storeHelper,
        Registry $registryHelper,
        LoggerInterface $logging,
        Transport $transport,
        RequestInterface $request)
    {
        $this->storeHelper = $storeHelper;
        $this->registryHelper = $registryHelper;

        $this->logging = $logging;
        $this->transport = $transport;
        $this->request = $request;
    }

    /**
     * @param \Magento\Framework\Mail\TransportInterface $subject
     * @param Closure                                    $proceed
     *
     * @return void
     * @throws Exception
     */
    public function aroundSendMessage(
        \Magento\Framework\Mail\TransportInterface $subject,
        Closure $proceed)
    {
        if ($this->registryHelper->registry('smtp_test')) {
            $server = $this->request->getParam('infrangible_smtp_test_mail_server');

            $this->sendMessage($subject, $server);
        } else {
            try {
                $this->sendMessage($subject, 'server');
            } catch (Exception $exception) {
                if ( ! ($exception instanceof RuntimeException)) {
                    $this->logging->error($exception);
                }

                try {
                    $this->sendMessage($subject, 'server2');
                } catch (Exception $exception) {
                    if ( ! ($exception instanceof RuntimeException)) {
                        $this->logging->error($exception);
                    }

                    if ($this->storeHelper->getStoreConfigFlag('infrangible_smtp/general/system_fallback')) {
                        return $proceed();
                    }
                }
            }
        }
    }

    /**
     * @param \Magento\Framework\Mail\TransportInterface $subject
     * @param string                                     $serverName
     *
     * @throws MailException
     * @throws AlreadyExistsException
     */
    protected function sendMessage(\Magento\Framework\Mail\TransportInterface $subject, string $serverName)
    {
        if ($this->registryHelper->registry('smtp_test')) {
            if ( ! $this->request->getParam('infrangible_smtp_general_enable')) {
                throw new RuntimeException(new Phrase('Module not enabled'));
            }

            if ( ! $this->request->getParam(sprintf('infrangible_smtp_%s_enable', $serverName))) {
                throw new RuntimeException(new Phrase('Server not enabled'));
            }
        } else {
            if ( ! $this->storeHelper->getStoreConfigFlag('infrangible_smtp/general/enable')) {
                throw new RuntimeException(new Phrase('Module not enabled'));
            }

            if ( ! $this->storeHelper->getStoreConfigFlag(sprintf('infrangible_smtp/%s/enable', $serverName))) {
                throw new RuntimeException(new Phrase('Server not enabled'));
            }
        }

        $this->transport->setServerName($serverName);
        $this->transport->send($subject);
    }
}
