<?php

namespace Infrangible\Smtp\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 *
 * @method string getBcc()
 * @method void setBcc(string $bcc)
 * @method string getBody()
 * @method void setBody(string $body)
 * @method string getCc()
 * @method void setCc(string $cc)
 * @method string getCreatedAt()
 * @method void setCreatedAt(string $createdAt)
 * @method string getEncoding()
 * @method void setEncoding(string $encoding)
 * @method string getFrom()
 * @method void setFrom(string $from)
 * @method string getMessage()
 * @method void setMessage(string $message)
 * @method string getReplyTo()
 * @method void setReplyTo(string $replyTo)
 * @method string getSender()
 * @method void setSender(string $sender)
 * @method int getStatus()
 * @method void setStatus(int $status)
 * @method string getSubject()
 * @method void setSubject(string $subject)
 * @method string getTo()
 * @method void setTo(string $to)
 * @method string getType()
 * @method void setType(string $type)
 * @method string getUpdatedAt()
 * @method void setUpdatedAt(string $updatedAt)
 */
class Log
    extends AbstractModel
{
    public const STATUS_NEW = 1;
    public const STATUS_SUCCESS = 2;
    public const STATUS_ERROR = 3;

    /**
     * @return void
     */
    public function _construct()
    {
        $this->_init(ResourceModel\Log::class);
    }
}
