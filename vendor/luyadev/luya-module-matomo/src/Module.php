<?php

namespace luya\matomo;

use yii\base\InvalidConfigException;
use luya\helpers\Url;

/**
 * Matomo Admin Module.
 *
 * @author Basil Suter <basil@nadar.io>
 * @since 1.0.0
 */
class Module extends \luya\admin\base\Module
{
    /**
     * @var integer The siteId which is given from the matomo application. Each site has its own id.
     */
    public $siteId;
    
    /**
     * @var string The url to the server where the matomo instance is running. without piwik.php or trailing slash.
     */
    public $serverUrl;
    
    /**
     * @var string The token in order to make api request, this token is available in the matomo settings.
     */
    public $apiToken;
    
    /**
     * @inheritdoc
     */
    public $apis = [
        'api-matomo-stats' => 'luya\matomo\apis\StatsController',
    ];
    
    /**
     * @inheritdoc
     */
    public $dashboardObjects = [
        [
            'class' => 'luya\admin\dashboard\ChartDashboardObject',
            'dataApiUrl' => 'admin/api-matomo-stats/visits',
            'title' => ['matomo', 'visits_dashboard_title'],
        ],
    ];
    
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        
        if ($this->siteId === null) {
            throw new InvalidConfigException("The siteId property of the matomo module must be configured.");
        }
        
        if ($this->serverUrl === null) {
            throw new InvalidConfigException("The serverUrl property of the matomo module must be configured.");
        }
    }

    /**
     * Ensures a given url.
     *
     * Removes trailing slash, add https schema if not avialable.
     *
     * @param string $url The url to ensure.
     * @return string
     */
    public static function ensureServerUrl($url)
    {
        return Url::ensureHttp(rtrim($url, '/'), true);
    }
    
    /**
     * @inheritdoc
     */
    public static function onLoad()
    {
        self::registerTranslation('matomo', static::staticBasePath() . '/messages', [
            'matomo' => 'matomo.php',
        ]);
    }
    
    /**
     * @inheritdoc
     */
    public static function t($message, array $params = [])
    {
        return parent::baseT('matomo', $message, $params);
    }
}
