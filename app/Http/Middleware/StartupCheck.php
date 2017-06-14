<?php

namespace App\Http\Middleware;

use App;
use App\Events\UserLoggedIn;
use App\Libraries\CurlUtils;
use App\Models\InvoiceDesign;
use App\Models\Language;
use Auth;
use Cache;
use Closure;
use Event;
use Illuminate\Http\Request;
use Input;
use Redirect;
use Schema;
use Session;
use Utils;

/**
 * Class StartupCheck.
 */
class StartupCheck
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Set up trusted X-Forwarded-Proto proxies
        // TRUSTED_PROXIES accepts a comma delimited list of subnets
        // ie, TRUSTED_PROXIES='10.0.0.0/8,172.16.0.0/12,192.168.0.0/16'
        if (isset($_ENV['TRUSTED_PROXIES'])) {
            $request->setTrustedProxies(array_map('trim', explode(',', env('TRUSTED_PROXIES'))));
        }

        // Ensure all request are over HTTPS in production
        if (Utils::requireHTTPS() && ! $request->secure()) {
            return Redirect::secure($request->path());
        }

        // If the database doens't yet exist we'll skip the rest
        if (! Utils::isNinja() && ! Utils::isDatabaseSetup()) {
            return $next($request);
        }

        // Check if a new version was installed
        if (! Utils::isNinja()) {
            $file = storage_path() . '/version.txt';
            $version = @file_get_contents($file);
            if ($version != NINJA_VERSION) {
                if (version_compare(phpversion(), '5.5.9', '<')) {
                    dd('Please update PHP to >= 5.5.9');
                }
                $handle = fopen($file, 'w');
                fwrite($handle, NINJA_VERSION);
                fclose($handle);

                return Redirect::to('/update');
            }
        }

        // Check the application is up to date and for any news feed messages
        if (Auth::check()) {
            $count = Session::get(SESSION_COUNTER, 0);
            Session::put(SESSION_COUNTER, ++$count);

            if (isset($_SERVER['REQUEST_URI']) && ! Utils::startsWith($_SERVER['REQUEST_URI'], '/news_feed') && ! Session::has('news_feed_id')) {
                $data = false;
                if (Utils::isNinja()) {
                    $data = Utils::getNewsFeedResponse();
                } else {
                    $file = @CurlUtils::get(NINJA_APP_URL.'/news_feed/'.Utils::getUserType().'/'.NINJA_VERSION);
                    $data = @json_decode($file);
                }
                if ($data) {
                    Session::put('news_feed_id', $data->id);
                    if ($data->message && $data->id > Auth::user()->news_feed_id) {
                        Session::flash('news_feed_message', $data->message);
                    }
                } else {
                    Session::put('news_feed_id', true);
                }
            }
        }

        // Check if we're requesting to change the account's language
        if (Input::has('lang')) {
            $locale = Input::get('lang');
            App::setLocale($locale);
            Session::set(SESSION_LOCALE, $locale);

            if (Auth::check()) {
                if ($language = Language::whereLocale($locale)->first()) {
                    $account = Auth::user()->account;
                    $account->language_id = $language->id;
                    $account->save();
                }
            }
        } elseif (Auth::check()) {
            $locale = Auth::user()->account->language ? Auth::user()->account->language->locale : DEFAULT_LOCALE;
            App::setLocale($locale);
        } elseif (session(SESSION_LOCALE)) {
            App::setLocale(session(SESSION_LOCALE));
        }

        // Make sure the account/user localization settings are in the session
        if (Auth::check() && ! Session::has(SESSION_TIMEZONE)) {
            Event::fire(new UserLoggedIn());
        }

        // Check if the user is claiming a license (ie, additional invoices, white label, etc.)
        if (! Utils::isNinjaProd() && isset($_SERVER['REQUEST_URI'])) {
            $claimingLicense = Utils::startsWith($_SERVER['REQUEST_URI'], '/claim_license');
            if (! $claimingLicense && Input::has('license_key') && Input::has('product_id')) {
                $licenseKey = Input::get('license_key');
                $productId = Input::get('product_id');

                $url = (Utils::isNinjaDev() ? SITE_URL : NINJA_APP_URL) . "/claim_license?license_key={$licenseKey}&product_id={$productId}&get_date=true";
                $data = trim(CurlUtils::get($url));

                if ($productId == PRODUCT_INVOICE_DESIGNS) {
                    if ($data = json_decode($data)) {
                        foreach ($data as $item) {
                            $design = new InvoiceDesign();
                            $design->id = $item->id;
                            $design->name = $item->name;
                            $design->pdfmake = $item->pdfmake;
                            $design->save();
                        }

                        Cache::forget('invoiceDesigns');
                        Session::flash('message', trans('texts.bought_designs'));
                    }
                } elseif ($productId == PRODUCT_WHITE_LABEL) {
                    if ($data && $data != RESULT_FAILURE) {
                        $company = Auth::user()->account->company;
                        $company->plan_term = PLAN_TERM_YEARLY;
                        $company->plan_paid = $data;
                        $date = max(date_create($data), date_create($company->plan_expires));
                        $company->plan_expires = $date->modify('+1 year')->format('Y-m-d');
                        $company->plan = PLAN_WHITE_LABEL;
                        $company->save();

                        Session::flash('message', trans('texts.bought_white_label'));
                    } else {
                        Session::flash('error', trans('texts.invalid_white_label_license'));
                    }
                }
            }
        }

        // Check data has been cached
        $cachedTables = unserialize(CACHED_TABLES);
        if (Input::has('clear_cache')) {
            Session::flash('message', 'Cache cleared');
        }
        foreach ($cachedTables as $name => $class) {
            if (Input::has('clear_cache') || ! Cache::has($name)) {
                // check that the table exists in case the migration is pending
                if (! Schema::hasTable((new $class())->getTable())) {
                    continue;
                }
                if ($name == 'paymentTerms') {
                    $orderBy = 'num_days';
                } elseif ($name == 'fonts') {
                    $orderBy = 'sort_order';
                } elseif (in_array($name, ['currencies', 'industries', 'languages', 'countries', 'banks'])) {
                    $orderBy = 'name';
                } else {
                    $orderBy = 'id';
                }
                $tableData = $class::orderBy($orderBy)->get();
                if (count($tableData)) {
                    Cache::forever($name, $tableData);
                }
            }
        }

        // Show message to IE 8 and before users
        if (isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/(?i)msie [2-8]/', $_SERVER['HTTP_USER_AGENT'])) {
            Session::flash('error', trans('texts.old_browser', ['link' => OUTDATE_BROWSER_URL]));
        }

        $response = $next($request);
        //$response->headers->set('X-Frame-Options', 'DENY');

        return $response;
    }
}
