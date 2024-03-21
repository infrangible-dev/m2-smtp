<?php

namespace Infrangible\Smtp\Traits;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
trait Log
{
    /**
     * @return string
     */
    protected function getModuleKey(): string
    {
        return 'Infrangible_Smtp';
    }

    /**
     * @return string
     */
    protected function getResourceKey(): string
    {
        return 'Infrangible_Smtp';
    }

    /**
     * @return string
     */
    protected function getMenuKey(): string
    {
        return 'infrangible_smtp_log';
    }

    /**
     * @return string
     */
    protected function getTitle(): string
    {
        return __('SMTP Log');
    }

    /**
     * @return string
     */
    protected function getObjectName(): string
    {
        return 'Log';
    }

    /**
     * @return string|null
     */
    protected function getObjectField(): ?string
    {
        return 'log_id';
    }

    /**
     * @return bool
     */
    protected function allowAdd(): bool
    {
        return false;
    }

    /**
     * @return bool
     */
    protected function allowEdit(): bool
    {
        return false;
    }

    /**
     * @return bool
     */
    protected function allowView(): bool
    {
        return true;
    }

    /**
     * @return bool
     */
    protected function allowDelete(): bool
    {
        return false;
    }
}
