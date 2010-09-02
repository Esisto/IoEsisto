<?php
ini_set("display_errors", "On");
error_reporting(E_ALL);

require_once("post/PostManager.php");



// PROCESS PERMALINK WP STYLE…
$error = '404';

if ( isset($_SERVER['PATH_INFO']) )
	$pathinfo = $_SERVER['PATH_INFO'];
else
	$pathinfo = '';
$pathinfo_array = explode('?', $pathinfo);
$pathinfo = str_replace("%", "%25", $pathinfo_array[0]);
$req_uri = $_SERVER['REQUEST_URI'];
$req_uri_array = explode('?', $req_uri);
$req_uri = $req_uri_array[0];
$self = $_SERVER['PHP_SELF'];
$home_path = $_SERVER["PHP_SELF"]; //parse_url(home_url());
//if ( isset($home_path['path']) )
//	$home_path = $home_path['path'];
//else
//	$home_path = '';
$home_path = trim($home_path, '/');

// Trim path info from the end and the leading home path from the
// front.  For path info requests, this leaves us with the requesting
// filename, if any.  For 404 requests, this leaves us with the
// requested permalink.
$req_uri = str_replace($pathinfo, '', rawurldecode($req_uri));
$req_uri = trim($req_uri, '/');
$req_uri = preg_replace("|^$home_path|", '', $req_uri);
$req_uri = trim($req_uri, '/');
$pathinfo = trim($pathinfo, '/');
$pathinfo = preg_replace("|^$home_path|", '', $pathinfo);
$pathinfo = trim($pathinfo, '/');
$self = trim($self, '/');
$self = preg_replace("|^$home_path|", '', $self);
$self = trim($self, '/');

// The requested permalink is in $pathinfo for path info requests and
//  $req_uri for other requests.
if ( ! empty($pathinfo) && !preg_match('|^.*' . $wp_rewrite->index . '$|', $pathinfo) ) {
	$request = $pathinfo;
} else {
	// If the request uri is the index, blank it out so that we don't try to match it against a rule.
	if ( $req_uri == "index.php"/*$wp_rewrite->index*/ )
		$req_uri = '';
	$request = $req_uri;
}

echo $req_uri;

//$this->request = $request;
//
//// Look for matches.
//$request_match = $request;
//foreach ( (array) $rewrite as $match => $query) {
//	// Don't try to match against AtomPub calls
//	if ( $req_uri == 'wp-app.php' )
//		break;
//
//	// If the requesting file is the anchor of the match, prepend it
//	// to the path info.
//	if ( (! empty($req_uri)) && (strpos($match, $req_uri) === 0) && ($req_uri != $request) )
//		$request_match = $req_uri . '/' . $request;
//
//	if ( preg_match("#^$match#", $request_match, $matches) ||
//		preg_match("#^$match#", urldecode($request_match), $matches) ) {
//		// Got a match.
//		$this->matched_rule = $match;
//
//		// Trim the query of everything up to the '?'.
//		$query = preg_replace("!^.+\?!", '', $query);
//
//		// Substitute the substring matches into the query.
//		$query = addslashes(WP_MatchesMapRegex::apply($query, $matches));
//
//		$this->matched_query = $query;
//
//		// Parse the query.
//		parse_str($query, $perma_query_vars);
//
//		// If we're processing a 404 request, clear the error var
//		// since we found something.
//		if ( isset($_GET['error']) )
//			unset($_GET['error']);
//
//		if ( isset($error) )
//			unset($error);
//
//		break;
//	}
//}
//
//// If req_uri is empty or if it is a request for ourself, unset error.
//if ( empty($request) || $req_uri == $self || strpos($_SERVER['PHP_SELF'], 'wp-admin/') !== false ) {
//	if ( isset($_GET['error']) )
//		unset($_GET['error']);
//
//	if ( isset($error) )
//		unset($error);
//
//	if ( isset($perma_query_vars) && strpos($_SERVER['PHP_SELF'], 'wp-admin/') !== false )
//		unset($perma_query_vars);
//
//	$this->did_permalink = false;
//}
//





?>