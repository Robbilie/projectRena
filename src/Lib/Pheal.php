<?php
namespace ProjectRena\Lib;

use Monolog\Logger;
use Pheal\Core\Config;
use ProjectRena\RenaApp;

/**
 * Class Pheal
 *
 * @package ProjectRena\Lib
 */
class Pheal
{
    /**
     * @var
     */
    protected $app;

    /**
     * @param RenaApp $app
     */
    function __construct(RenaApp $app)
    {
        $this->app = $app;

        Config::getInstance()->http_method = "curl";
        Config::getInstance()->http_user_agent = $app->baseConfig->getConfig("userAgent", "site", "API DataGetter from projectRena (karbowiak@gmail.com)");
        Config::getInstance()->http_post = false;
        Config::getInstance()->http_keepalive = 10; // 10 seconds keep alive
        Config::getInstance()->http_timeout = 30;
        Config::getInstance()->cache = new \Pheal\Cache\RedisStorage(array(
            "host"       => $app->baseConfig->getConfig("host", "redis", "127.0.0.1"),
            "port"       => $app->baseConfig->getConfig("port", "redis", 6379),
            "persistent" => true,
            "auth"       => null,
            "prefix"     => "Pheal",
        ));

        $psrLogger = new \Monolog\Logger("PhealLogger");
        $psrLogger->pushHandler(new \Monolog\Handler\StreamHandler(__DIR__ . "/../../logs/pheal.log", Logger::INFO));
        Config::getInstance()->log = new \Pheal\Log\PsrLogger($psrLogger);
        Config::getInstance()->api_customkeys = true;
        Config::getInstance()->api_base = $app->baseConfig->getConfig("apiServer", "ccp", "https://api.eveonline.com/");
        Config::getInstance()->rateLimiter = new \Pheal\RateLimiter\FileLockRateLimiter(__DIR__ . "/../../cache/", 30, 20, 10);
    }

    /**
     * @param null $apiKey
     * @param null $vCode
     *
     * @return \Pheal\Pheal
     * @throws \Exception
     */
    public function Pheal($apiKey = null, $vCode = null)
    {
        if($this->app->Storage->get("Api904") >= date("Y-m-d H:i:s"))
        {
            $this->app->Logging->log("ERROR", "904ed till: " . $this->app->Storage->get("Api904"));
            throw new \Exception("Error, CCP has 904ed us till " . $this->app->Storage->get("Api904"));
        }

        return new \Pheal\Pheal($apiKey, $vCode);
    }

    /**
     * @param $keyID
     * @param $characterID
     * @param $exception
     */
    public function handleApiException($keyID, $characterID, $exception)
    {
        $exceptionCode = $exception->getCode();
        $exceptionMessage = $exception->getMessage();

        switch($exceptionCode)
        {
            case 28: // Timeouts
            case 904: // temp ban from CCPs api server
                $this->app->Storage->set("Api904", date("Y-m-d H:i:s", time() + 300));
                break;

            case 403:
            case 502:
            case 503: // Service Unavailable - try again later
                $this->app->ApiKeyCharacters->setCachedUntil($keyID, $characterID, date("Y-m-d H:i:s", time() + 300));
                break;

            case 119: // Kills exhausted: retry after [{0}]
                $this->app->ApiKeyCharacters->setCachedUntil($keyID, $characterID, $exception->cached_until);
                break;

            case 120: // Expected beforeKillID [{0}] but supplied [{1}]: kills previously loaded.
                $this->app->ApiKeyCharacters->setCachedUntil($keyID, $characterID, $exception->cached_until);
                break;

            case 221: // Demote toon, illegal page access
                $this->app->Db->execute("DELETE FROM apiKeyCharacters WHERE keyID = :keyID", array(":keyID" => $keyID));
                $this->app->ApiKeys->setErrorCode($keyID, $exceptionCode);
                break;

            case 220:
            case 200: // Current security level not high enough.
                // Typically happens when a key isn't a full API Key
                $this->app->Db->execute("DELETE FROM apiKeyCharacters WHERE keyID = :keyID", array(":keyID" => $keyID));
                $this->app->ApiKeys->setErrorCode($keyID, $exceptionCode);
                break;

            case 522:
            case 201: // Character does not belong to account.
                // Typically caused by a character transfer

                break;

            case 207: // Not available for NPC corporations.
            case 209:
                $this->app->ApiKeyCharacters->setIsDirector($keyID, $characterID, 0);
                $this->app->Db->execute("DELETE FROM apiKeyCharacters WHERE keyID = :keyID AND characterID = :characterID", array(":keyID"       => $keyID,
                                                                                                                                  ":characterID" => $characterID,
                ));
                break;

            case 222: // account has expired
                $this->app->Db->execute("DELETE FROM apiKeyCharacters WHERE keyID = :keyID", array(":keyID" => $keyID));
                $this->app->ApiKeys->setErrorCode($keyID, $exceptionCode);
                break;

            case 403:
            case 211: // Login denied by account status
                // Remove characters, will revalidate with next doPopulate
                $this->app->Db->execute("DELETE FROM apiKeyCharacters WHERE keyID = :keyID", array(":keyID" => $keyID));
                $this->app->ApiKeys->setErrorCode($keyID, $exceptionCode);
                break;

            case 202: // API key authentication failure.
            case 203: // Authentication failure - API is no good and will never be good again
            case 204: // Authentication failure.
            case 205: // Authentication failure (final pass).
            case 210: // Authentication failure.
            case 521: // Invalid username and/or password passed to UserData.LoginWebUser().
                $this->app->Db->execute("DELETE FROM apiKeyCharacters WHERE keyID = :keyID", array(":keyID" => $keyID));
                $this->app->ApiKeys->setErrorCode($keyID, $exceptionCode);
                break;

            case 500: // Internal Server Error (More CCP Issues)
            case 520: // Unexpected failure accessing database. (More CCP issues)
            case 404: // URL Not Found (CCP having issues...)
            case 902: // Eve backend database temporarily disabled
                $this->app->ApiKeyCharacters->setCachedUntil($keyID, $characterID, time() + 3600);
                break;

            case 0: // API Date could not be read / parsed, original exception (Something is wrong with the XML and it couldn't be parsed)
            default: // try again in 5 minutes
                $this->app->Logging->log("ERROR", "$keyID - Unhandled error - Code $exceptionCode - $exceptionMessage");
                $this->app->ApiKeys->setErrorCode($keyID, $exceptionCode);
                break;
        }
        $this->app->ApiKeyCharacters->setErrorCode($keyID, $characterID, $exceptionCode);
        $this->app->StatsD->increment("CCPApiErrors");
    }
}
