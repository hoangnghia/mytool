<?php

namespace luya\matomo;

use luya\base\Widget;
use luya\web\View;
use luya\matomo\Module;
use yii\base\InvalidConfigException;

/**
 * Integrate Matomo TrackingCode.
 *
 * @property \luya\matomo\admin\Module $module
 *
 * @author Basil Suter <basil@nadar.io>
 * @since 1.0.0
 */
class TrackingCodeWidget extends Widget
{
    /**
     * @var integer The given siteId from the piwik interface.
     */
    public $siteId;
    
    /**
     * @var string The path to the piwik server with schema like `https://piwik.mydomain.com`
     */
    public $serverUrl;
    
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if ($this->siteId === null) {
            $this->siteId = $this->module->siteId;
        }
        
        if ($this->serverUrl === null) {
            $this->serverUrl = $this->module->serverUrl;
        }
    }
    
    private $_module;
    
    /**
     * Get the matomo module.
     *
     * Returns the admin module object instance.
     *
     * @return \luya\matomo\admin\Module
     */
    public function getModule()
    {
        if ($this->_module === null) {
            $this->_module = Module::getInstance();
            
            if ($this->_module === null) {
                throw new InvalidConfigException("You have either provide the sideId and serverUrl as widget config or configure the luya\matomo\Module in your config.");
            }
        }
        
        return $this->_module;
    }
    
    public function getEnsuredHostName()
    {
        return Module::ensureServerUrl($this->serverUrl);
    }
    
    public function run()
    {
        // if not in production, the widget does not render and register anything.
        if (YII_ENV_PROD) {
            $this->view->registerJs("
  var _paq = _paq || [];
  _paq.push(['trackPageView']);
  _paq.push(['enableLinkTracking']);
  (function() {
    var u=\"".$this->getEnsuredHostName()."/\";
    _paq.push(['setTrackerUrl', u+'piwik.php']);
    _paq.push(['setSiteId', '".$this->siteId."']);
    var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
    g.type='text/javascript'; g.async=true; g.defer=true; g.src=u+'piwik.js'; s.parentNode.insertBefore(g,s);
  })();
	", View::POS_HEAD);
        
            return $this->render('trackingcode', [
                'hostName' => $this->getEnsuredHostName(),
                'siteId' => $this->siteId,
            ]);
        }
    }
}
