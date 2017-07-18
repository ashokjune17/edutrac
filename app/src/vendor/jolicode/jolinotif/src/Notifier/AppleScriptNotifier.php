<?php

/*
 * This file is part of the JoliNotif project.
 *
 * (c) Loïck Piera <pyrech@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Joli\JoliNotif\Notifier;

use Joli\JoliNotif\Notification;
use Joli\JoliNotif\Util\OsHelper;
use Symfony\Component\Process\ProcessBuilder;

/**
 * This notifier can be used on Mac OS X 10.9+.
 */
class AppleScriptNotifier extends CliBasedNotifier
{
    /**
     * {@inheritdoc}
     */
    public function isSupported()
    {
        if (OsHelper::isMacOS() && version_compare(OsHelper::getMacOSVersion(), '10.9.0', '>=')) {
            return parent::isSupported();
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getBinary()
    {
        return 'osascript';
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return static::PRIORITY_LOW;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureProcess(ProcessBuilder $processBuilder, Notification $notification)
    {
        $script = 'display notification "'.str_replace('"', '\\"', $notification->getBody()).'"';

        if ($notification->getTitle()) {
            $script .= ' with title "'.str_replace('"', '\\"', $notification->getTitle()).'"';
        }

        if ($notification->getOption('subtitle')) {
            $script .= ' subtitle "'.str_replace('"', '\\"', $notification->getOption('subtitle')).'"';
        }

        if ($notification->getOption('sound')) {
            $script .= ' sound name "'.str_replace('"', '\\"', $notification->getOption('sound')).'"';
        }

        $processBuilder->add('-e');
        $processBuilder->add($script);
    }
}
