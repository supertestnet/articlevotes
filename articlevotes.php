<?php

/**
 * Plugin Name: Article Votes
 * Description: Manage a database of articles such that people can vote for them by sending sats
 * Version: 1.0.1
 * Author: Super Testnet
 */

function articlesDB() {
        global $wpdb;
        $articles_db_version = '1.0';
        $table_name = $wpdb->prefix . "articlesDB";
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE $table_name (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                title text(50),
                img text(50),
                sats bigint(16),
                trending tinyint(1),
                description text(350),
                link text(50),
                status tinyint(1),
                PRIMARY KEY  (id)
        ) $charset_collate;";
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
        add_option( 'articles_db_version', $articles_db_version );
}

register_activation_hook( __FILE__, 'articlesDB' );

function addArticle() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'articlesDB';
	$title = $_GET[ "title" ];
        $img = $_GET[ "img" ];
        $sats = $_GET[ "sats" ];
        $trending = $_GET[ "trending" ];
        $description = $_GET[ "description" ];
	$link = $_GET[ "link" ];
	$status = $_GET[ "status" ];
        $wpdb->insert(
                $table_name,
                array(
                        'title' => $title,
                        'img' => $img,
                        'sats' => $sats,
                        'trending' => $trending,
			'description' => $description,
			'link' => $link,
                        'status' => $status,
                )
        );
}

add_action('wp_ajax_addarticle', 'addArticle');

function showArticles( $atts = array() ) {
	global $wpdb;
	$table_name = $wpdb->prefix . 'articlesDB';
	$selection = "SELECT * FROM $table_name";
	if ( $atts[ "status" ] == "true" ) {
		$selection = "SELECT * FROM $table_name WHERE status = 1";
	}
        $articles = $wpdb->get_results( $selection );
	return json_encode( $articles, true );
}

add_shortcode( 'showarticles', 'showArticles' );

function deleteArticle() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'articlesDB';
        $id = $_GET[ "article_id" ];
	$wpdb->update( $table_name, array( 'status' => 0 ), array( 'id' => $id ) );
}

add_action('wp_ajax_deletearticle', 'deleteArticle');

function editArticle() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'articlesDB';
        $id = $_GET[ "article_id" ];
        $title = $_GET[ "title" ];
        $img = $_GET[ "img" ];
        $sats = $_GET[ "sats" ];
        $trending = $_GET[ "trending" ];
        $description = $_GET[ "description" ];
        $link = $_GET[ "link" ];
	$status = 1;
        $wpdb->update( $table_name, array(
		'title' => $title,
                'img' => $img,
                'sats' => $sats,
                'trending' => $trending,
                'description' => $description,
                'link' => $link,
                'status' => $status,
	), array( 'id' => $id ) );
}

add_action('wp_ajax_editarticle', 'editArticle');

function checkTheInvoice() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'invoicesDB';
        include_once( 'LightningController.php' );
        $lnbits_url = get_option( 'lnbits_url' );
        $pmthash = $_GET[ "pmthash" ];
        $title = $_GET[ "title" ];
        $lnbits_apikey = get_option( 'lnbits_api_key' );
        $LightningController = new App\Http\Controllers\LightningController();
        $status = $LightningController->checkInvoice( $lnbits_url, $pmthash, $lnbits_apikey );
	if ( $status == 1 ) {
		$wpdb->update( $table_name, array( 'status' => 0 ), array( 'pmthash' => $pmthash ) );
	}
        $selection = "SELECT amount, article FROM $table_name WHERE pmthash = '" . $pmthash . "'";
        $invoicedata = $wpdb->get_results( $selection );
        $newsats = $invoicedata[0]->amount;
        $article = $invoicedata[0]->article;
        $table_name = $wpdb->prefix . 'articlesDB';
        $selection = "SELECT sats FROM $table_name WHERE title = '" . $article . "'";
        $oldsats = $wpdb->get_results( $selection );
        $oldsats = $oldsats[0]->sats;
        $totalsats = $oldsats + $newsats;
        if ( $status == 1 ) {
                $wpdb->update( $table_name, array( 'sats' => $totalsats ), array( 'title' => $article ) );
        }
        if ( $status == 1 ) {
	        $selection = "SELECT id, trending FROM $table_name";
        	$trending = $wpdb->get_results( $selection );
        	foreach( $trending as $item ) {
        	        $wpdb->query( "UPDATE wp_articlesDB SET trending = 0" );
	        }
                $wpdb->update( $table_name, array( 'trending' => 1 ), array( 'title' => $title ) );
        }
        echo $status;
	die();
}

add_action( 'wp_ajax_checkinvoice', 'checkTheInvoice' );
add_action( 'wp_ajax_nopriv_checkinvoice', 'checkTheInvoice' );

function getInvoice() {
    $amt = 10;
    $memo = "Test Invoice";
    if ( isset( $_GET[ "amount" ] ) ) {
        $amt = $_GET[ "amount" ];
    }
    if ( isset( $_GET[ "memo" ] ) ) {
        $memo = $_GET[ "memo" ];
    }
    include_once('LightningController.php');
    $lnbits_url = get_option( 'lnbits_url' );
    $amount = $amt;
    $lnbits_apikey = get_option( 'lnbits_api_key' );
    $webhook = "https://www.bitcoinaudible.com/";
    $LightningController = new App\Http\Controllers\LightningController();
    $invoice = $LightningController->requestInvoice( $lnbits_url, $amount, $memo, $lnbits_apikey, $webhook );
    $data = json_decode( $invoice, true );
    $pmtrqst = $data[ "invoice" ];
    $pmthash = $data[ "pmthash" ];
    addInvoice( $pmtrqst, $amount, $memo, $pmthash );
    echo $invoice;
    die();
}

add_action( 'wp_ajax_getinvoice', 'getInvoice' );
add_action( 'wp_ajax_nopriv_getinvoice', 'getInvoice' );

function invoicesDB() {
        global $wpdb;
        $invoices_db_version = '1.1';
        $table_name = $wpdb->prefix . "invoicesDB";
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE $table_name (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                invoice text(250),
		amount bigint(16),
                article text(250),
                pmthash text(64),
                status tinyint(1),
                PRIMARY KEY  (id)
        ) $charset_collate;";
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
        add_option( 'invoices_db_version', $invoices_db_version );
}

register_activation_hook( __FILE__, 'invoicesDB' );

function addInvoice( $invoice, $amount, $article, $pmthash ) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'invoicesDB';
        $wpdb->insert(
                $table_name,
                array(
                        'invoice' => $invoice,
			'amount' => $amount,
			'article' => $article,
			'pmthash' => $pmthash,
                        'status' => 1,
                )
        );
}

function articlevotes_register_settings() {
	add_option( 'lnbits_url', '' );
	add_option( 'lnbits_api_key', '' );
	register_setting( 'articlevotes_options_group', 'lnbits_url', 'articlevotes_callback' );
	register_setting( 'articlevotes_options_group', 'lnbits_api_key', 'articlevotes_callback' );
}
add_action( 'admin_init', 'articlevotes_register_settings' );

function articlevotes_register_options_page() {
	add_options_page('Article Votes', 'Article Votes', 'manage_options', 'articlevotes', 'articlevotes_options_page');
}
add_action('admin_menu', 'articlevotes_register_options_page');

function articlevotes_options_page()
{
?>
        <h2 style="text-decoration: underline;">Article Votes</h2>
        <form method="post" action="options.php">
                <?php settings_fields( 'articlevotes_options_group' ); ?>
		<h3>
			LNBits Settings
		</h3>
		<table>
			<tr valign="middle">
				<th scope="row">
					<label for="lnbits url">
						LNBits url
					</label>
				</th>
				<td>
					<input type="text" id="lnbits_url" name="lnbits_url" value="<?php echo get_option('lnbits_url'); ?>" placeholder="http://localhost:5000" />
				</td>
			</tr>
		</table>
		<table>
			<tr valign="middle">
				<th scope="row">
					<label for="lnbits api key">
						LNBits api key
					</label>
				</th>
				<td>
					<input type="text" id="lnbits_api_key" name="lnbits_api_key" value="<?php echo get_option('lnbits_api_key'); ?>" placeholder="57sd8Qi6FYr1eZJMyd43tJmmmj35Lb4h" />
				</td>
			</tr>
		</table>
                <?php  submit_button(); ?>
        </form>
<?php
} ?>
