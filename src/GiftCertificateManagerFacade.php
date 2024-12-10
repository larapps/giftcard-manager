<?php

namespace Larapps\GiftCertificateManager;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Larapps\GiftCertificateManager\Skeleton\SkeletonClass
 */
class GiftCertificateManagerFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'gift-certificate-manager';
    }
}
