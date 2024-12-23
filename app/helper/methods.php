<?php

$app = new \config\APP;
$app->setLang();

use Shared\simple_html_dom;

/** 
 * Render function
 * @param String twig file path
 * @param [] List of data
 */
function render($template, $data, $responseType = 'html')
{

    $app = new \config\APP;

    /**
     * Check if response is required in JSON
     */  
    if (!empty($app->request()->get('load')) && $app->request()->get('load') == 'json' )
    {
        echo json_encode($data);
        return true;
    }

    
    /**
     * Check if response is required in JSON
     */  
    if (!empty($app->request()->get('load')) && $app->request()->get('load') == 'content' )
    {
        $setting = $app->SystemSetting();
        $template = 'views/front/'.($setting['template'] ?? 'default').'/pages/'.$data['layout'].'.html.twig';
    }

    /**
     * Response will be override only
     * In case the system works In Vue APP
     */ 
    $isFront = strpos($template, 'front/');
    
    $path = isset($data['load_vue']) ? 'views/admin/vue.html.twig' : $template;
    $data = loadConfig($template, $data);
    $output =  $app->template()->render($path, $data);

    $isFront ? file_put_contents($_SERVER['DOCUMENT_ROOT'].'/app/cache/'. (str_replace('/', '_', $app->currentPage)). '.html', $output) : '';
    
    if ($responseType == 'html')
    {
        echo $output;
    } else {
        return $output;
    }
 } 


/** 
 * Render Plugin function
 * @param String twig file path
 * @param [] List of data
 */
function renderPlugin($template, $data)
{

    global $app;

    /**
     * Response will be override only
     * In case the system works In Vue APP
     */ 
    try {

        $setting = $app->SystemSetting();

        $data['app'] = $app;
        $data['lang'] = new \helper\Lang($_SESSION['lang']);
        $data['template'] = $setting['template'] ?? 'default';
        $output =  $app->template()->render($template, $data);

    } catch (\Throwable $th) {
        return $th->getMessage();
    }

    return $output;
 } 


 /**
  * Load the main configuration in Array
  */
function loadConfig($template, $data)
{

    try {
        
        $app = new \config\APP;
            
        $setting = $app->SystemSetting();
        $languages = $app->Languages();  
        
    } catch (\Exception $e) {
        echo ('CHECK DATABASE CONNECTION ');
        die();
    }

    $data['component'] = $template;
    $data['app'] = $app;
    $data['app']->auth = $app->auth();
    $data['app']->customer = $app->customer_auth();
    $data['app']->setting = $setting;
    $data['template'] = $setting['template'] ?? 'default';
    $data['menu'] = $app->menu();
    $data['sidemenu'] = $app->sideMenu();
    $data['langs'] = $languages;
    $data['startdate'] = !empty($app->request()->get('start')) ? $app->request()->get('start') : date('Y-m-d');
    $data['enddate'] = !empty($app->request()->get('end')) ? $app->request()->get('end') : date('Y-m-d');
    $data['lang'] = new helper\Lang($_SESSION['lang']);
    $data['lang_json'] = (new helper\Lang($_SESSION['lang']))->load();
    $data['lang_key'] = substr($_SESSION['lang'],0,2);

    return $data;
}

function processShortcodes($content) {
    // Use preg_replace_callback to find shortcodes and process them
    $checkCode = preg_replace_callback('/\[plugin_shortcode(.*?)\]/', 'replaceShortcode', $content);
    
    
    return  $checkCode ?? $content;
}

function shortcode_parse_atts($text) {
    $atts = array();
    $pattern = '/([\w-]+)\s*=\s*(?:"([^"]*)"|\'([^\']*)\'|([^\s\'"]+))/';
    preg_match_all($pattern, $text, $matches, PREG_SET_ORDER);

    foreach ($matches as $match) {
        if (!empty($match[2])) {
            $atts[$match[1]] = $match[2];
        } elseif (!empty($match[3])) {
            $atts[$match[1]] = $match[3];
        } elseif (!empty($match[4])) {
            $atts[$match[1]] = $match[4];
        }
    }

    return $atts;
}

function replaceShortcode($matches) {
    
    // Extract attributes from the shortcode
    $attributes = shortcode_parse_atts($matches[1]);

    // Generate content based on the attributes
    return $attributes ? generatePluginContent($attributes) : null;
}

function generatePluginContent($attributes) {
    // Example attributes handling
    $id = isset($attributes['id']) ? $attributes['id'] : null;
    $name = isset($attributes['name']) ? $attributes['name'] : 'default';

    return (new \Medians\Hooks\Application\HookController())->view($attributes);
}


/**
 * Page not found 
 * @param Object $twig, Object $app 
 * @return Page not found template 
 */
function Page404()
{
    $app = new \config\APP;
    $settings = $app->SystemSetting();
    echo $app->template()->render('views/front/'.$settings['template'].'/error.html.twig', [
        'title' => 'Page not found',
        'template'  => $settings['template'] ?? 'default',
        'app' => $app
    ]);
}

/**
 * Page not found 
 * @param Object $twig, Object $app 
 * @return Page not found template 
 */
function errorPage($data)
{
    $app = new \config\APP;
    $settings = $app->SystemSetting();
    echo $app->template()->render('views/front/'.$settings['template'].'/error.html.twig', [
        'title' => 'Error',
        'msg' => $data,
        'template'  => $settings['template'] ?? 'default',
        'app' => $app
    ]);
}


/**
 * Page not authorized 
 * @param Object $twig, Object $app 
 * @return Page not found template 
 */
function Page403()
{
    $app = new \config\APP;
    $settings = $app->SystemSetting();
    echo $app->template()->render('views/front/'.$settings['template'].'/error.html.twig', [
        'title' => 'Not authorized to acces this Page.',
        'template'  => $settings['template'] ?? 'default',
        'app' => '',
    ]);
}




// Format File size into human-readable units
function formatSizeUnits($bytes, $format = 'text') {
    $mbSize = 0;
    if ($bytes >= 1073741824) {
        $mbSize = number_format($bytes / 1073741824, 2) * 1000;
        $bytes = number_format($bytes / 1073741824, 2);
        $unit = 'GB';
    } elseif ($bytes >= 1048576) {
        $bytes = number_format($bytes / 1048576, 2);
        $mbSize = $bytes;
        $unit = 'MB';
    } elseif ($bytes >= 1024) {
        $mbSize = 1;
        $bytes = number_format($bytes / 1024, 2);
        $unit = 'KB';
    } elseif ($bytes > 1) {
        $bytes = $bytes ;
        $unit = 'B';
    } elseif ($bytes == 1) {
        $bytes = $bytes ;
        $unit = 'B';
    } else {
        $bytes = 0;
        $unit = 'B';
    }

    if ($format == 'text')
    {
        $bytes = "$bytes $unit";
    }
    
    if ($format == 'number')
    {
        $bytes = $mbSize ?? 0;
    }

    return $bytes;
}



/**
 * Handle routes response 
 * based on session & Permissions
*/
function response($response)
{
    
    $app = new \config\APP;

	echo isset($app->auth()->id) ? (is_array($response) ? json_encode($response) : $response) : Page403();
}


function front_response($response)
{
    
    $app = new \config\APP;

	echo isset($app->customer_auth()->customer_id) ? (is_array($response) ? json_encode($response) : $response) : Page403();
}


/**
 * Handle routes response 
 * based on session & Permissions
*/
function printResponse($response)
{
	echo is_array($response) ? json_encode($response) : $response ;
}

/** 
 * Filter language variable by code
 * 
 * @param String $langkey
 * @return String 
*/ 
function translate($langkey = null)
{
    $translation = (new helper\Lang($_SESSION['lang']))->translate($langkey);
    return $translation ?? ucfirst(str_replace('_', ' ', $langkey));
}



/**
 * Get days between two dates
 * 
 */
function getDaysBetweenDates($to, $from)
{
    $toDate = strtotime($to);
    $fromDate = strtotime($from);
    $datediff = $toDate - $fromDate;

    return round($datediff / (60 * 60 * 24));
}


/**
 * Get days between two dates
 * 
 */
function getPercentageBetweenDates($to, $from)
{
    $toDate = strtotime($to);
    $fromDate = strtotime(date("Y-m-d"));
    $datediff = $toDate - $fromDate;

    $currentDuration = round($datediff / (60 * 60 * 24));

    $totalDuration = getDaysBetweenDates($to, $from);

    return round((($totalDuration - $currentDuration) / $totalDuration) * 100 );
}


/**
 * Extract sections from html page
 */
function scrapeAndExtractSections($url) 
{
    // Initialize cURL session
    $ch = curl_init($url);
    
    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Execute cURL session and get the HTML content
    $htmlContent = curl_exec($ch);

    // Check for cURL errors
    if (curl_errno($ch)) {
        echo 'Curl error: ' . curl_error($ch);
        curl_close($ch);
        return;
    }

    // Close cURL session
    curl_close($ch);

    // Create a SimpleHTMLDom object
    $dom = new simple_html_dom();
    
    // Load HTML content into the DOM parser
    $dom->load($htmlContent);

    // Find and extract section elements
    $sections = array();
    foreach ($dom->find('section') as $section) {
        // Add section content to the array
        $sections[] = $section->outertext;
    }

    // Clean up the DOM parser
    $dom->clear();
    
    // Output the extracted sections
    return $sections;
}

function curLng()
{
    return $_SESSION['lang'] ?? $app->lang;
}

/**
 * Secure the inputs from XSS vulneribility
 * Save from cyber attacks
 */
function sanitizeInput($input, $ignore = null) {
    global $app;

    if (is_object($input)) {
        foreach ($input as $key => $value) {
            $input->$key = $value ? sanitizeInput($value) : '';
        }
        return $input;
    } else if (is_array($input) ) {
        foreach ($input as $key => $value) {
            $input[$key] = $value ? sanitizeInput($value) : '';
        }
        return $input;
    } else if (gettype($input) =='string' && in_array(substr($input,0,1), ['{', '['])   ) {
        return sanitizeInput(json_decode($input));
    } else {
        return str_replace(["&lt;", "&quot", "&gt;", "&#039;", "&amp;apos;", "&apos;", "'", '"'], "`",  htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    }
}
