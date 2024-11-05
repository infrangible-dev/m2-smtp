<?php

declare(strict_types=1);

namespace Infrangible\Smtp\Plugin;

use Closure;
use Exception;
use Infrangible\Core\Helper\Registry;
use Infrangible\Core\Helper\Stores;
use Infrangible\Smtp\Helper\Data;
use Infrangible\Smtp\Model\Transport;
use Magento\Framework\App\RequestInterface;
use Psr\Log\LoggerInterface;
use RuntimeException;

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

    /** @var Data */
    protected $helper;

    public function __construct(
        Stores $storeHelper,
        Registry $registryHelper,
        LoggerInterface $logging,
        Transport $transport,
        RequestInterface $request,
        Data $helper
    ) {
        $this->storeHelper = $storeHelper;
        $this->registryHelper = $registryHelper;
        $this->logging = $logging;
        $this->transport = $transport;
        $this->request = $request;
        $this->helper = $helper;
    }

    /**
     * @throws Exception
     */
    public function aroundSendMessage(
        \Magento\Framework\Mail\TransportInterface $subject,
        Closure $proceed
    ): void {
        $message = $this->transport->prepareMessage($subject);

        $log = $this->helper->createMessageLog($message);

        if ($this->registryHelper->registry('smtp_test')) {
            $server = $this->request->getParam('infrangible_smtp_test_mail_server');

            try {
                $this->sendMessage($server);

                $this->helper->logMessageSuccess($log);
            } catch (Exception $exception) {
                $this->logging->error($exception);

                $this->helper->logMesssageError(
                    $log,
                    $exception->getMessage()
                );

                throw new Exception($exception->getMessage());
            }
        } else {
            try {
                $this->sendMessage('server');

                $this->helper->logMessageSuccess($log);
            } catch (Exception $exception) {
                if (! ($exception instanceof RuntimeException)) {
                    $this->logging->error($exception);
                }

                try {
                    $this->sendMessage('server2');

                    $this->helper->logMessageSuccess($log);
                } catch (Exception $exception) {
                    if (! ($exception instanceof RuntimeException)) {
                        $this->logging->error($exception);
                    }

                    if ($this->storeHelper->getStoreConfigFlag('infrangible_smtp/general/system_fallback')) {
                        try {
                            $proceed();

                            $this->helper->logMessageSuccess($log);
                        } catch (Exception $exception) {
                            $this->logging->error($exception);

                            $this->helper->logMesssageError(
                                $log,
                                $exception->getMessage()
                            );
                        }
                    }
                }
            }
        }
    }

    protected function sendMessage(string $serverName): void
    {
        if ($this->registryHelper->registry('smtp_test')) {
            if (! $this->request->getParam('infrangible_smtp_general_enable')) {
                throw new RuntimeException(__('Module not enabled')->render());
            }

            if (! $this->request->getParam(
                sprintf(
                    'infrangible_smtp_%s_enable',
                    $serverName
                )
            )) {
                throw new RuntimeException(__('Server not enabled')->render());
            }
        } else {
            if (! $this->storeHelper->getStoreConfigFlag('infrangible_smtp/general/enable')) {
                throw new RuntimeException(__('Module not enabled')->render());
            }

            if (! $this->storeHelper->getStoreConfigFlag(
                sprintf(
                    'infrangible_smtp/%s/enable',
                    $serverName
                )
            )) {
                throw new RuntimeException(__('Server not enabled')->render());
            }
        }

        $this->transport->setServerName($serverName);
        $this->transport->send();
    }
}
