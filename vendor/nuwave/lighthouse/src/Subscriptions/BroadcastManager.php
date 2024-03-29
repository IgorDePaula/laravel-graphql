<?php

namespace Nuwave\Lighthouse\Subscriptions;

use Illuminate\Support\Facades\Log;
use Nuwave\Lighthouse\Subscriptions\Broadcasters\RedisBroadcaster;
use Pusher\Pusher;
use RuntimeException;
use Illuminate\Support\Arr;
use Nuwave\Lighthouse\Support\DriverManager;
use Nuwave\Lighthouse\Subscriptions\Contracts\Broadcaster;
use Nuwave\Lighthouse\Subscriptions\Broadcasters\LogBroadcaster;
use Nuwave\Lighthouse\Subscriptions\Broadcasters\PusherBroadcaster;

/**
 * @method void broadcast(\Nuwave\Lighthouse\Subscriptions\Subscriber $subscriber, array $data)
 * @method \Symfony\Component\HttpFoundation\Response hook(\Illuminate\Http\Request $request)
 * @method \Symfony\Component\HttpFoundation\Response authorized(\Illuminate\Http\Request $request)
 * @method \Symfony\Component\HttpFoundation\Response unauthorized(\Illuminate\Http\Request $request)
 */
class BroadcastManager extends DriverManager
{
    /**
     * Get configuration key.
     *
     * @return string
     */
    protected function configKey(): string
    {
        return 'lighthouse.subscriptions.broadcasters';
    }

    /**
     * Get configuration driver key.
     *
     * @return string
     */
    protected function driverKey(): string
    {
        return 'lighthouse.subscriptions.broadcaster';
    }

    /**
     * The interface the driver should implement.
     *
     * @return string
     */
    protected function interface(): string
    {
        return Broadcaster::class;
    }

    /**
     * Create instance of pusher driver.
     *
     * @param  mixed[]  $config
     * @return \Nuwave\Lighthouse\Subscriptions\Broadcasters\PusherBroadcaster
     * @throws \Pusher\PusherException
     */
    protected function createPusherDriver(array $config): PusherBroadcaster
    {
        $connection = $config['connection'] ?? 'pusher';
        $driverConfig = config("broadcasting.connections.{$connection}");

        if (empty($driverConfig) || $driverConfig['driver'] !== 'pusher') {
            throw new RuntimeException("Could not initialize Pusher broadcast driver for connection: {$connection}.");
        }

        $appKey = Arr::get($driverConfig, 'key');
        $appSecret = Arr::get($driverConfig, 'secret');
        $appId = Arr::get($driverConfig, 'app_id');
        $options = Arr::get($driverConfig, 'options', []);

        $pusher = new Pusher($appKey, $appSecret, $appId, $options);

        return new PusherBroadcaster($pusher);
    }

    /**
     * Create instance of log driver.
     *
     * @param  mixed[]  $config
     * @return \Nuwave\Lighthouse\Subscriptions\Broadcasters\LogBroadcaster
     */
    protected function createLogDriver(array $config): LogBroadcaster
    {
        return new LogBroadcaster($config);
    }

    protected function createRedisDriver(array $config): RedisBroadcaster
    {
        Log::info('redis config - '.implode('-',$config));
        return new RedisBroadcaster($config);
    }
}
