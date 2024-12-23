<?php 

/**
 * Medians PS System
 * 
 * Here you can control all routes 
 * for backend & frontend
 */ 

use \Shared\RouteHandler;

use Medians\APIController;
use Medians\DashboardController;
$app = new \config\APP;




/** @return send_message */
RouteHandler::post('/send_message', Medians\Help\Application\HelpMessageController::class.'@store');



/**
 * Switch the language 
 */ 
RouteHandler::get('/switch-lang/(:all)', \Medians\DashboardController::class.'@switchLang');

/**
 * Authentication
 */
RouteHandler::get('/', \Medians\Pages\Application\PageController::class.'@homepage');
RouteHandler::get('/dashboard/login', \Medians\Auth\Application\AuthService::class.'@loginPage');
RouteHandler::get('/google_login_redirect', \Medians\Auth\Application\CustomerAuthService::class.'@verifyLoginWithGoogle');
RouteHandler::get('/facebook_login_redirect', \Medians\Auth\Application\CustomerAuthService::class.'@verifyLoginWithFacebook');
RouteHandler::get('/activate-account/(:all)', \Medians\Auth\Application\CustomerAuthService::class.'@activate');
RouteHandler::get('/reset-password', \Medians\Auth\Application\AuthService::class.'@resetPasswordPage');
RouteHandler::get('/reset-password-code', \Medians\Auth\Application\AuthService::class.'@resetPasswordCodePage');
RouteHandler::get('/customer/reset-password-code', \Medians\Auth\Application\CustomerAuthService::class.'@resetPasswordCodePage');

// Login as admin
RouteHandler::post('/', \Medians\Auth\Application\AuthService::class.'@userLogin');


// Get requests
RouteHandler::get('/mobile_api/(:all)', \Medians\MobileAPIController::class.'@handle');
/**
 * Cart routes
 */
RouteHandler::get('/search', \Medians\Pages\Application\PageController::class.'@search');
RouteHandler::get('/upload', \Medians\Media\Application\MediaItemController::class.'@upload_page');
RouteHandler::get('/media/edit/(:all)', \Medians\Media\Application\MediaItemController::class.'@edit_media');
RouteHandler::get('/import/audio', \Medians\Media\Application\MediaItemController::class.'@import_page');
RouteHandler::get('/audio/(:all)', \Medians\Media\Application\MediaItemController::class.'@audio_page');

/** Iframes pages */
RouteHandler::get('/upload/iframe', \Medians\Media\Application\IframeController::class.'@iframe_upload_page');
RouteHandler::get('/iframe/edit/(:all)', \Medians\Media\Application\IframeController::class.'@edit_iframe');
RouteHandler::get('/discover/iframe', \Medians\Media\Application\IframeController::class.'@discover');
RouteHandler::get('/iframe/(:all)', \Medians\Media\Application\IframeController::class.'@book_page');
RouteHandler::get('/import/iframe', \Medians\Media\Application\IframeController::class.'@import_page');

/** Audiobooks pages */
RouteHandler::get('/upload/audiobook', \Medians\Media\Application\AudiobookController::class.'@audiobook_upload_page');
RouteHandler::get('/audiobook/edit/(:all)', \Medians\Media\Application\AudiobookController::class.'@edit_audiobook');
RouteHandler::get('/audiobook/edit_chapters/(:all)', \Medians\Media\Application\AudiobookController::class.'@edit_chapters');
RouteHandler::get('/discover/audiobook', \Medians\Media\Application\AudiobookController::class.'@discover');
RouteHandler::get('/audiobook/(:all)', \Medians\Media\Application\AudiobookController::class.'@book_page');
RouteHandler::get('/import/audiobook', \Medians\Media\Application\AudiobookController::class.'@import_page');

/** Videos pages */
RouteHandler::get('/upload/video', \Medians\Media\Application\VideoController::class.'@upload_page');
RouteHandler::get('/import/video', \Medians\Media\Application\VideoController::class.'@import_page');
RouteHandler::get('/video/edit/(:all)', \Medians\Media\Application\VideoController::class.'@edit_video');
RouteHandler::get('/discover/video', \Medians\Media\Application\VideoController::class.'@discover');
RouteHandler::get('/video/(:all)', \Medians\Media\Application\VideoController::class.'@video_page');

/** Short Videos pages */
RouteHandler::get('/shorts/create', \Medians\Media\Application\ShortVideoController::class.'@upload_page');
RouteHandler::get('/generate/short/(:all)', \Medians\Media\Application\ShortVideoController::class.'@generate_page');
RouteHandler::get('/short/edit/(:all)', \Medians\Media\Application\ShortVideoController::class.'@edit_video');
RouteHandler::get('/discover/short', \Medians\Media\Application\ShortVideoController::class.'@discover');
RouteHandler::get('/short_video/(:all)', \Medians\Media\Application\ShortVideoController::class.'@video_page');

/** Search pages */
RouteHandler::get('/search/audio', \Medians\Media\Application\MediaItemController::class.'@search');
RouteHandler::get('/search/audiobook', \Medians\Media\Application\AudiobookController::class.'@search');
RouteHandler::get('/search/video', \Medians\Media\Application\VideoController::class.'@search');
RouteHandler::get('/search/short', \Medians\Media\Application\ShortVideoController::class.'@search');
RouteHandler::get('/search/artist', \Medians\Customers\Application\CustomerController::class.'@search');
RouteHandler::get('/search/playlist', \Medians\Playlists\Application\PlaylistController::class.'@search');
RouteHandler::get('/search/station', \Medians\Stations\Application\StationController::class.'@search');
RouteHandler::get('/search/channel', \Medians\Channels\Application\ChannelController::class.'@search');

/** Studio pages */
RouteHandler::get('/studio', \Medians\Media\Application\MediaItemController::class.'@studio');
RouteHandler::get('/studio/audio', \Medians\Media\Application\MediaItemController::class.'@studio_media');
RouteHandler::get('/studio/videos', \Medians\Media\Application\VideoController::class.'@studio_media');
RouteHandler::get('/studio/shorts', \Medians\Media\Application\ShortVideoController::class.'@studio_media');
RouteHandler::get('/studio/playlists', \Medians\Media\Application\MediaItemController::class.'@studio_playlists');
RouteHandler::get('/studio/audiobooks', \Medians\Media\Application\AudiobookController::class.'@studio_audiobooks');
RouteHandler::get('/studio/stations', \Medians\Stations\Application\StationController::class.'@studio');
RouteHandler::get('/studio/profile', \Medians\Customers\Application\CustomerController::class.'@edit_profile');
RouteHandler::get('/studio/information', \Medians\Customers\Application\CustomerController::class.'@edit_information');
RouteHandler::get('/studio/channels', \Medians\Channels\Application\ChannelController::class.'@studio');
RouteHandler::get('/studio/invoices', \Medians\Invoices\Application\InvoiceController::class.'@studio');
RouteHandler::get('/studio/subscriptions', \Medians\Packages\Application\PackageSubscriptionController::class.'@studio');

/**Subscription pages */
RouteHandler::get('/package_subscription/(:all)', \Medians\Packages\Application\PackageController::class.'@page');
RouteHandler::get('/invoice/(:all)', \Medians\Invoices\Application\InvoiceController::class.'@page');

RouteHandler::get('/discover', \Medians\Media\Application\MediaItemController::class.'@discover');
RouteHandler::get('/genres', \Medians\Media\Application\MediaItemController::class.'@genres');
RouteHandler::get('/genre/(:all)', \Medians\Media\Application\MediaItemController::class.'@genre');
RouteHandler::get('/book_genre/(:all)', \Medians\Media\Application\AudiobookController::class.'@genre');
RouteHandler::get('/video_genre/(:all)', \Medians\Media\Application\VideoController::class.'@genre');
RouteHandler::get('/short_video_genre/(:all)', \Medians\Media\Application\ShortVideoController::class.'@genre');
RouteHandler::get('/artists', \Medians\Customers\Application\CustomerController::class.'@channels');
RouteHandler::get('/artist/(:all)', \Medians\Customers\Application\CustomerController::class.'@artist');
RouteHandler::get('/playlists', \Medians\Playlists\Application\PlaylistController::class.'@playlists');
RouteHandler::get('/playlist/(:all)', \Medians\Playlists\Application\PlaylistController::class.'@playlist');

/** Stations pages */
RouteHandler::get('/stations', \Medians\Stations\Application\StationController::class.'@stations');
RouteHandler::get('/stations/manage/(:all)', \Medians\Stations\Application\StationController::class.'@calendar');
RouteHandler::get('/station_json/(:all)', \Medians\Stations\Application\StationController::class.'@station_json');
RouteHandler::get('/stations/create', \Medians\Stations\Application\StationController::class.'@station_upload_page');
RouteHandler::get('/stations/edit/(:all)', \Medians\Stations\Application\StationController::class.'@station_edit');
RouteHandler::get('/station/(:all)', \Medians\Stations\Application\StationController::class.'@station');

/** Channels pages */
RouteHandler::get('/channels', \Medians\Channels\Application\ChannelController::class.'@channels');
RouteHandler::get('/channels/manage/(:all)', \Medians\Channels\Application\ChannelController::class.'@calendar');
RouteHandler::get('/channel_json/(:all)', \Medians\Channels\Application\ChannelController::class.'@channel_json');
RouteHandler::get('/channels/create', \Medians\Channels\Application\ChannelController::class.'@channel_upload_page');
RouteHandler::get('/channels/edit/(:all)', \Medians\Channels\Application\ChannelController::class.'@channel_edit');
RouteHandler::get('/channel/(:all)', \Medians\Channels\Application\ChannelController::class.'@channel');

/** Blog pages */
RouteHandler::get('/blog', \Medians\Blog\Application\BlogController::class.'@list');

/** Embed pages */
RouteHandler::get('/embed_audio/(:all)', \Medians\Media\Application\MediaItemController::class.'@embed');
RouteHandler::get('/embed_video/(:all)', \Medians\Media\Application\VideoController::class.'@embed');

RouteHandler::get('/customer/login', \Medians\Auth\Application\CustomerAuthService::class.'@loginPage');
RouteHandler::get('/customer/signup', \Medians\Auth\Application\CustomerAuthService::class.'@signupPage');
RouteHandler::get('/customer/confirm_account', \Medians\Auth\Application\CustomerAuthService::class.'@otp');
RouteHandler::get('/customer/reset_password', \Medians\Auth\Application\CustomerAuthService::class.'@resetPasswordPage');
RouteHandler::get('/customer/dashboard', \Medians\Media\Application\MediaItemController::class.'@studio');
RouteHandler::get('/customer/orders', \Medians\Customers\Application\CustomerController::class.'@orders');


// POST Requests
RouteHandler::post('/dashboard/login', \Medians\Auth\Application\AuthService::class.'@userLogin');
RouteHandler::post('/reset-password', \Medians\Auth\Application\AuthService::class.'@userResetPassword');
RouteHandler::post('/reset-password-code', \Medians\Auth\Application\AuthService::class.'@resetChangePassword');
RouteHandler::post('/customer/signup', \Medians\Auth\Application\CustomerAuthService::class.'@userSignup');
RouteHandler::post('/customer/login', \Medians\Auth\Application\CustomerAuthService::class.'@userLogin');
RouteHandler::post('/customer/confirm', \Medians\Auth\Application\CustomerAuthService::class.'@checkOTP');
RouteHandler::post('/customer/reset-password', \Medians\Auth\Application\CustomerAuthService::class.'@userResetPassword');
RouteHandler::post('/customer/reset-password-code', \Medians\Auth\Application\CustomerAuthService::class.'@resetChangePassword');


/**
 * Load sub-pages
 */
RouteHandler::get('/page/(:all)', \Medians\Pages\Application\PageController::class.'@page'); 



/**
 * Mobile API requests authorized & non-authorized  
*/
RouteHandler::post('/mobile_api/login', \Medians\MobileAPIController::class.'@login');
RouteHandler::post('/mobile_api/create', \Medians\MobileAPIController::class.'@create');
RouteHandler::post('/mobile_api/update', \Medians\MobileAPIController::class.'@update');
RouteHandler::post('/mobile_api/delete', \Medians\MobileAPIController::class.'@delete');
RouteHandler::post('/mobile_api', \Medians\MobileAPIController::class.'@handle');

/**
* Restricted access requests 
*/
if(!empty($app->auth()))
{

    // Dashboard controller based on the user Role 
    RouteHandler::get('/dashboard', \Medians\DashboardController::class.'@index'); 
    RouteHandler::get('/admin/dashboard', \Medians\DashboardController::class.'@index'); 


    // API POST requests
    RouteHandler::post('/api/create', \Medians\APIController::class.'@create');
    RouteHandler::post('/api/update', \Medians\APIController::class.'@update');
    RouteHandler::post('/api/delete', \Medians\APIController::class.'@delete');
    RouteHandler::post('/api/updateStatus', \Medians\APIController::class.'@updateStatus');
    RouteHandler::post('/api/checkout', \Medians\Orders\Application\OrderController::class.'@checkout');
    RouteHandler::post('/api/bug_report', \Medians\APIController::class.'@bug_report');
    RouteHandler::post('/api/search', \Medians\APIController::class.'@search');
    RouteHandler::post('/api/(:all)', \Medians\APIController::class.'@handle');
    RouteHandler::post('/api', \Medians\APIController::class.'@handle');

    // API GET requests
    RouteHandler::get('/api/(:all)', \Medians\APIController::class.'@handle');




    /**
    * @return Media Library requests
    */
    RouteHandler::post('/media-library-api/delete', \Medians\Media\Application\MediaController::class.'@delete');
    RouteHandler::post('/media-library-api/(:all)', \Medians\Media\Application\MediaController::class.'@upload');
    RouteHandler::get('/media-library-api/media', \Medians\Media\Application\MediaController::class.'@media');



    /**
    * @return Users
    */
    RouteHandler::get('/admin/users/index', \Medians\Users\Application\UserController::class.'@index');
    RouteHandler::get('/admin/users', \Medians\Users\Application\UserController::class.'@index');

    

    /**
    * @return Branches
    */
    RouteHandler::get('/admin/branches', \Medians\Branches\Application\BranchController::class.'@index');

    

    /**
    * @return  Notifications 
    */
    RouteHandler::get('/admin/notifications', \Medians\Notifications\Application\NotificationController::class.'@index');
    RouteHandler::get('/admin/latest_notifications', \Medians\Notifications\Application\NotificationController::class.'@latest_notifications');
    RouteHandler::get('/admin/latest_notifications/(:all)', \Medians\Notifications\Application\NotificationController::class.'@latest_notifications');
    RouteHandler::post('/admin/read_notification', \Medians\Notifications\Application\NotificationController::class.'@read_notification');
    RouteHandler::post('/admin/check_notification', \Medians\Notifications\Application\NotificationController::class.'@check_notification');

    

    /**
    * @return products
    */
    RouteHandler::get('/admin/products', Medians\Products\Application\ProductController::class.'@index');
    RouteHandler::get('/admin/products/new', Medians\Products\Application\ProductController::class.'@create');
    RouteHandler::get('/admin/csv_import', Medians\Products\Application\ProductController::class.'@csv_import');
    RouteHandler::get('/admin/products/(:all)', Medians\Products\Application\ProductController::class.'@product');
    RouteHandler::get('/admin/stock', Medians\Products\Application\ProductStockController::class.'@index');

    /**
    * @return Genres
    */
    RouteHandler::get('/admin/genres', Medians\Categories\Application\GenreController::class.'@index');
    RouteHandler::get('/admin/genres/(:all)', Medians\Categories\Application\GenreController::class.'@genre');

    /**
    * @return AudioGenres
    */
    RouteHandler::get('/admin/book_genres', Medians\Categories\Application\BookGenreController::class.'@index');
    RouteHandler::get('/admin/book_genres/(:all)', Medians\Categories\Application\BookGenreController::class.'@genre');

    /**
    * @return VideoGenres
    */
    RouteHandler::get('/admin/video_genres', Medians\Categories\Application\VideoGenreController::class.'@index');
    RouteHandler::get('/admin/video_genres/(:all)', Medians\Categories\Application\VideoGenreController::class.'@genre');

    /**
    * @return Moods
    */
    RouteHandler::get('/admin/moods', Medians\Categories\Application\MoodController::class.'@index');
    RouteHandler::get('/admin/moods/(:all)', Medians\Categories\Application\MoodController::class.'@mood');

    /**
    * @return Shipping
    */
    RouteHandler::get('/admin/shipping', Medians\Shipping\Application\ShippingController::class.'@index');

    /**
    * @return Newsletters
    */
    RouteHandler::get('/admin/newsletters', Medians\Newsletters\Application\NewsletterController::class.'@index');

    RouteHandler::get('/admin/newsletter_subscribers', Medians\Newsletters\Application\SubscriberController::class.'@index');

    /** @return Customers */
    RouteHandler::get('/admin/customers', Medians\Customers\Application\CustomerController::class.'@index');

    /** @return help messages */
    RouteHandler::get('/admin/help_messages', Medians\Help\Application\HelpMessageController::class.'@index');

    /** @return Get-started */
    // RouteHandler::get('/admin/get_started', Medians\Users\Application\GetStartedController::class.'@get_started');


    /**
    * @return Business settings
    */
    RouteHandler::get('/admin/settings', \Medians\Settings\Application\SettingsController::class.'@index');

    /** @return Admin profile */
    RouteHandler::get('/admin/profile', Medians\Users\Application\UserController::class.'@profile');

    /** @return Gallery */
    RouteHandler::get('/admin/gallery', Medians\Gallery\Application\GalleryController::class.'@index');

    /** @return reviews */
    RouteHandler::get('/admin/reviews', Medians\Reviews\Application\ReviewController::class.'@index');


    /** @return Hooks */
    RouteHandler::get('/admin/hooks', \Medians\Hooks\Application\HookController::class.'@index');
    RouteHandler::get('/admin/hooks/(:all)', \Medians\Hooks\Application\HookController::class.'@hook');


    /** @return Plugins */
    RouteHandler::get('/admin/plugins', \Medians\Plugins\Application\PluginController::class.'@index');

    /** @return Plugins */
    RouteHandler::get('/admin/transactions', \Medians\Transactions\Application\TransactionController::class.'@index');

    /** @return Blog */
    RouteHandler::get('/admin/blog', \Medians\Blog\Application\BlogController::class.'@index');
    RouteHandler::get('/admin/blog/(:all)', Medians\Blog\Application\BlogController::class.'@article');

    /** @return stations */
    RouteHandler::get('/admin/stations', Medians\Stations\Application\StationController::class.'@index');

    /** @return channels */
    RouteHandler::get('/admin/channels', Medians\Channels\Application\ChannelController::class.'@index');

    /** @return Playlist */
    RouteHandler::get('/admin/playlists', Medians\Playlists\Application\PlaylistController::class.'@index');

    /** @return Audio */
    RouteHandler::get('/admin/audio', Medians\Media\Application\MediaItemController::class.'@index');

    /** @return Audiobooks */
    RouteHandler::get('/admin/audio', Medians\Media\Application\MediaItemController::class.'@index');
    RouteHandler::get('/admin/audiobooks', Medians\Media\Application\AudiobookController::class.'@index');
    RouteHandler::get('/admin/videos', Medians\Media\Application\VideoController::class.'@index');
    RouteHandler::get('/admin/shorts', Medians\Media\Application\ShortVideoController::class.'@index');


    /**
    * @return System settings
    */
    RouteHandler::get('/admin/system_settings', \Medians\Settings\Application\SystemSettingsController::class.'@index');
    RouteHandler::get('/admin/site_settings', \Medians\Settings\Application\SiteSettingsController::class.'@index');
    RouteHandler::get('/admin/storage_settings', \Medians\Settings\Application\StreamingSettingsController::class.'@index');
    RouteHandler::get('/admin/videos_settings', \Medians\Settings\Application\VideosSettingsController::class.'@index');
    RouteHandler::get('/admin/short_videos_settings', \Medians\Settings\Application\ShortVideosSettingsController::class.'@index');
    RouteHandler::get('/admin/audio_settings', \Medians\Settings\Application\AudioSettingsController::class.'@index');
    RouteHandler::get('/admin/audiobooks_settings', \Medians\Settings\Application\AudiobooksSettingsController::class.'@index');
    RouteHandler::get('/admin/stations_settings', \Medians\Settings\Application\StationsSettingsController::class.'@index');
    RouteHandler::get('/admin/channels_settings', \Medians\Settings\Application\ChannelsSettingsController::class.'@index');
    RouteHandler::get('/admin/playlists_settings', \Medians\Settings\Application\PlaylistsSettingsController::class.'@index');
    RouteHandler::get('/admin/payment_settings', \Medians\Settings\Application\PaymentSettingsController::class.'@index');

    /**
    * @return Notifications events 
    */
    RouteHandler::get('/admin/notifications_events', \Medians\Notifications\Application\NotificationEventController::class.'@index');

    /** @return roles */
    RouteHandler::get('/admin/roles', Medians\Roles\Application\RoleController::class.'@index');


    /** @return Languages */
    RouteHandler::get('/admin/languages', Medians\Languages\Application\LanguageController::class.'@index');

    /** @return Packages */
    RouteHandler::get('/admin/packages', Medians\Packages\Application\PackageController::class.'@index');

    /** @return Invoices */
    RouteHandler::get('/admin/invoices', Medians\Invoices\Application\InvoiceController::class.'@index');

    /** @return Transactions */
    RouteHandler::get('/admin/transactions', Medians\Transactions\Application\TransactionController::class.'@index');

    /** @return Package Subscription */
    RouteHandler::get('/admin/package_subscriptions', Medians\Packages\Application\PackageSubscriptionController::class.'@index');

    /** @return Translations */
    RouteHandler::get('/admin/translations', Medians\Languages\Application\TranslationController::class.'@index');

    /** @return Subscriber */
    RouteHandler::get('/admin/newsletter_subscribers', Medians\Newsletters\Application\NewsletterSubscriberController::class.'@index');

    /** @return Contact Forms */
    RouteHandler::get('/admin/contact_forms', Medians\Forms\Application\ContactFormController::class.'@index');

    /** @return menus Forms */
    RouteHandler::get('/admin/menus', \Medians\Menus\Application\MenuController::class.'@index');
    
    /** @return Email templates */
    RouteHandler::get('/admin/email_templates', \Medians\Templates\Application\EmailTemplateController::class.'@index');

    /** @return Comments */
    RouteHandler::get('/admin/comments', Medians\Comments\Application\CommentController::class.'@index');

    /** @return Templates */
    RouteHandler::get('/admin/templates', Medians\Templates\Application\WebTemplateController::class.'@index');



    /**
    * @return Content editor
    */
    RouteHandler::get('/admin/email_builder', \Medians\Builders\Application\EmailBuilderController::class.'@index');
    RouteHandler::get('/admin/pages', \Medians\Pages\Application\PageController::class.'@index');
    RouteHandler::get('/admin/pages/(:all)', \Medians\Pages\Application\PageController::class.'@admin_item');
    RouteHandler::get('/admin/builder', \Medians\Builders\Application\BuilderController::class.'@index');
    RouteHandler::get('/admin/editor', \Medians\Pages\Application\PageController::class.'@editor');
    RouteHandler::get('/admin/builder/load', \Medians\Builders\Application\BuilderController::class.'@load'); 
    RouteHandler::get('/admin/builder/meta', \Medians\Builders\Application\BuilderController::class.'@meta'); 
    RouteHandler::get('/admin/builder/module', \Medians\Builders\Application\BuilderController::class.'@module'); 
    RouteHandler::get('/admin/builder/languages', \Medians\Builders\Application\BuilderController::class.'@languages'); 
    RouteHandler::get('/admin/builder/template_preview', \Medians\Builders\Application\BuilderController::class.'@template_preview'); 
    RouteHandler::get('/admin/builder/new', \Medians\Builders\Application\BuilderController::class.'@new_get'); 
    RouteHandler::get('/admin/builder/scrab', \Medians\Builders\Application\BuilderController::class.'@scrab_get'); 

    RouteHandler::post('/admin/update_section_content', \Medians\Pages\Application\PageController::class.'@updateContent');
    RouteHandler::post('/admin/builder', \Medians\Builders\Application\BuilderController::class.'@submit'); 
    RouteHandler::post('/admin/builder/submit', \Medians\Builders\Application\BuilderController::class.'@submit'); 
    RouteHandler::post('/admin/builder/scrab', \Medians\Builders\Application\BuilderController::class.'@scrab'); 
    RouteHandler::post('/admin/builder/addContent', \Medians\Builders\Application\BuilderController::class.'@store'); 
    
}

RouteHandler::get('/logout', function () 
{
    (new \Medians\Auth\Application\AuthService)->unsetSession();
    (new \Medians\Auth\Application\CustomerAuthService)->unsetSession();
    echo (new \config\APP)->redirect('./');
});

RouteHandler::get('/switch-mode/(:all)', function ($mode) 
{
    $set = setcookie('is_dark', ($mode == 'dark'), time() + (10 * 365 * 24 * 60 * 60), "/");
    $_SESSION['is_dark'] = ($mode == 'dark');
    return $_SESSION['is_dark'];
});

RouteHandler::get('/accept-cookie', function () 
{
    setcookie('cookie_accepted', 1, time() + (10 * 365 * 24 * 60 * 60), "/");
    return;
});

// Front API GET requests
RouteHandler::get('/front_api', \Medians\FrontAPIController::class.'@handle');
RouteHandler::post('/front_api', \Medians\FrontAPIController::class.'@handle');
RouteHandler::post('/front_api/create', \Medians\FrontAPIController::class.'@create');
RouteHandler::post('/front_api/update', \Medians\FrontAPIController::class.'@update');
RouteHandler::post('/front_api/delete', \Medians\FrontAPIController::class.'@delete');

/**
 * Loading assets with handling types
 */
RouteHandler::get('/assets', \Medians\Media\Application\MediaController::class.'@assets'); 

/**
 * Streaming multi-media types
 */
RouteHandler::get('/stream', \Medians\Media\Application\MediaController::class.'@stream'); 
RouteHandler::get('/stream_audio', \Medians\Media\Application\MediaController::class.'@stream_audio'); 
RouteHandler::get('/stream_video', \Medians\Media\Application\MediaController::class.'@stream_video'); 
RouteHandler::get('/stream_short_video', \Medians\Media\Application\MediaController::class.'@stream_short_video'); 
RouteHandler::get('/stream_station', \Medians\Media\Application\MediaController::class.'@stream_station'); 
RouteHandler::get('/stream_channel', \Medians\Media\Application\MediaController::class.'@stream_channel'); 
RouteHandler::get('/stream_external', \Medians\Media\Application\MediaController::class.'@stream_external'); 

/**
 * Load sub-pages
 */
RouteHandler::get('/(:all)', \Medians\Pages\Application\PageController::class.'@page'); 


return $app->run();


    