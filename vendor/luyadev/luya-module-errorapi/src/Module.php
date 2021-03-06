<?php

namespace luya\errorapi;

use luya\base\CoreModuleInterface;

/**
 * Error API Module.
 *
 * @author Basil Suter <basil@nadar.io>
 * @since 1.0.0
 */
final class Module extends \luya\base\Module implements CoreModuleInterface
{
    /**
     * @var array Mail recipients.
     */
    public $recipient = [];

    /**
     * @var string The token which should be used to call the slack api. If not defined slack call is disabled.
     */
    public $slackToken;
    
    /**
     * @var string The channel where the slack message should be pushed to.
     */
    public $slackChannel = '#luya';

    /**
     * @var string The link to the "create issue" button.
     * @since 1.0.1
     */
    public $issueCreateRepo = 'https://github.com/luyadev/luya';
    
    /**
     * @inheritdoc
     */
    public $urlRules = [
        ['pattern' => 'errorapi/create', 'route' => 'errorapi/default/create'],
        ['pattern' => 'errorapi/resolve', 'route' => 'errorapi/default/resolve'],
    ];

    /**
     * @inheritdoc
     */
    public static function onLoad()
    {
        self::registerTranslation('errorapi', '@errorapi/messages', [
            'errorapi' => 'errorapi.php',
        ]);
    }

    /**
     * @inheritdoc
     */
    public static function t($message, array $params = [])
    {
        return parent::baseT('errorapi', $message, $params);
    }
}
