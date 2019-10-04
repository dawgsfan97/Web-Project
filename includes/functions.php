<?php
if (isset($_POST["search"])) {
    searchByKeyword($_POST["search"]);
};
/**
 * This Function is used to get search result from a specific URL
 */
function searchByKeyword($keyword)
{
    require('config.php');
    $keyword = trim($keyword); //Strip whitespace (or other characters) from the beginning and end of a string
    $keyword = str_replace(" ", "+", $keyword);
    $url = "http://www.omdbapi.com/?apikey=" . config('api_key') . "&s=" . $keyword;
    $handle = curl_init();
    curl_setopt($handle, CURLOPT_URL, $url);
    curl_setopt_array(
        $handle,
        array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true
        )
    );
    $output = curl_exec($handle);
    $response = json_decode($output, true);
    $result_num = sizeof($response["Search"]);
    for ($x = 0; $x < $result_num; $x++) {
        echo $x % 4 == 0 ? '<div class="col-xs-12 col-sm-12 col-lg-12"><br><hr class="my-4"><br></div>' : ''; // add break lines 
        echo '<div class="col-xs-12 col-sm-6 col-lg-3">';
        echo '<div class="card" style="width: 100%;">';
        getDetailsByID($response["Search"][$x]["imdbID"]);
			
		echo '<a href="#" class="btn btn-primary">Read More</a>';
		echo '</div>';
		echo '</div>';
        echo '</div>';
    }
    echo '<div class="col-xs-12 col-sm-12 col-lg-12"><br><hr class="my-4"><br></div>'; // add break lines 
    curl_close($handle);
    

}
/*
 * This function is used for more detail about the movie by searching its imdbID
*/
function getDetailsByID($id)
{
    $url = "http://www.omdbapi.com/?apikey=" . config('api_key') . "&i=" . $id;
    $handle = curl_init();
    curl_setopt($handle, CURLOPT_URL, $url);
    curl_setopt_array(
        $handle,
        array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true
        )
    );
    $output = curl_exec($handle);
    $response = json_decode($output, true);
    echo '<img class="card-img-top img-fluid rounded" src="' . ($response["Poster"] == 'N/A' ? '../template/assets/images/nopicture.jpg': $response["Poster"]) . '" alt="Poster of Movie">';
	echo '<div class="card-body">';
	echo '<h5 class="card-title">' . $response["Title"]. ' - '. $response["Year"] . '</h5>';
	echo '<p class="card-text">' . $response["Plot"] . '</p>';
}
/**
 * Displays site name.
 */
function site_name()
{
    echo config('name');
}


/**
 * Displays site url provided in conig.
 */
function site_url()
{
    echo config('site_url');
}

/**
 * Displays site version.
 */
function site_version()
{
    echo config('version');
}

/**
 * Website navigation.
 */
function nav_menu()
{
    $nav_menu = '';
    $nav_items = config('nav_menu');
    foreach ($nav_items as $uri => $name) {
        $class = '';
        if (isset($_SERVER['QUERY_STRING'])) {
            $class = str_replace('page=', '', $_SERVER['QUERY_STRING']) == $uri ? 'active' : '';
        } 
        $url = config('site_url') . '/' . (config('pretty_uri') || $uri == '' ? '' : '?page=') . $uri;
        $nav_menu .= '<li class="nav-item ' . $class . '"> <a href="' . $url . '" title="' . $name . '" class="nav-link ' . '">' . $name . '</a>' . '</li>';
    }
    echo trim($nav_menu);
}

/**
 * Displays page title. It takes the data from
 * URL, it replaces the hyphens with spaces and
 * it capitalizes the words.
 */
function page_title()
{
    $page = isset($_GET['page']) ? htmlspecialchars($_GET['page']) : 'Home';
    echo ucwords(str_replace('-', ' ', $page));
}

/**
 * Displays page content. It takes the data from
 * the static pages inside the pages/ directory.
 * When not found, display the 404 error page.
 */
function page_content()
{
    $page = isset($_GET['page']) ? $_GET['page'] : 'home';
    $path = getcwd() . '/' . config('content_path') . '/' . $page . '.php';
    if (!file_exists($path)) {
        $path = getcwd() . '/' . config('content_path') . '/404.php';
    }
    // echo file_get_contents($path);
    require config('content_path') . '/' . $page . '.php';
}

/**
 * Starts everything and displays the template.
 */
function init()
{
    require config('template_path') . '/template.php';
}
