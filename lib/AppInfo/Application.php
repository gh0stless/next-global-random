<?php

declare(strict_types=1);

namespace OCA\GlobalRandom\AppInfo;

use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\AppFramework\Bootstrap\IBootstrap;
use OCP\AppFramework\Bootstrap\IRegistrationContext;

class Application extends App implements IBootstrap {
    public const APP_ID = 'globalrandom';

    public function __construct() {
        parent::__construct(self::APP_ID);
    }

    public function register(IRegistrationContext $context): void {
        // Reine Iframe-Hülle — keine eigenen Services, Events oder Middleware nötig.
    }

    public function boot(IBootContext $context): void {
        // Nichts zu booten.
    }
}
