<?php
/**
* Plugin Name: QRwelcome
* Plugin URI: 
* Description: This plugin detects if someone arrives to your site from a QRcode.
* Version: 1.0.0
* Author: Javier Suarez
* Author URI: http://javiersuarezsanchez.com
*/


//Show New Text
$count=0;
add_option('QRvisitcounter');
function QRadd_IP($ip){
    global $wpdb;
    $wpdb->insert($wpdb->prefix."QRwelcome", array('VisitIP' => $ip));}
function QRget_IP(){
    global $wpdb;
    return $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."QRwelcome;" , ARRAY_N);}

////////////////////////////////
//    Lee el parametro de la URL
////////////////////////////////
function QRwelcome(){
    $var = get_query_var('qr','false');
    if($var == 'true'){
        $count = get_option('QRvisitcounter');
        $count++;
        update_option('QRvisitcounter', $count);
        QRadd_IP(QRgetRealIP());}}

function QRgetRealIP() {
    if (!empty($_SERVER[‘HTTP_CLIENT_IP’]))
        return $_SERVER[‘HTTP_CLIENT_IP’];
    if (!empty($_SERVER[‘HTTP_X_FORWARDED_FOR’]))
        return $_SERVER[‘HTTP_X_FORWARDED_FOR’];
    return $_SERVER[‘REMOTE_ADDR’];}

////////////////////////////////
//    Resetear el contador
////////////////////////////////
function QRresetcounter(){
    if(isset($_POST["resetcounter"])){ 
        update_option('QRvisitcounter', 0);}}

/////////////////////////////////////
//    Muestra variable en el backend
/////////////////////////////////////
function QRadminData(){
?>
    <div class="container">
    <form method="post" action="<?php QRresetcounter(); ?>">
    <table>
        <tr>
            <td colspan="3"><p style="font-size:15px;">Han llegado a esta página a través del código QR:</p></td>
        </tr>
        <tr>
            <td>        </td>
            <td style="border-left:solid 2px gray; padding-left:15px;">
                <p style="font-size:45px; margin: 25px;"><?php echo get_option('QRvisitcounter'); ?></p>
            </td>
            <td><p style="font-size:15px;">visitas</p></td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td><input type="submit" name="resetcounter" value="Reset"></td>
        </tr>
    </table>
    </form>
    <table>
        <tr>
            <td>IPvisit</td>
            <td><?php  $ip = QRget_IP(); echo $ip[0]; ?></td>
        </tr>
        <tr></tr>
    </table>
    </div>

<?php
}

/////////////////////////////////////
//    Añade item al menu
/////////////////////////////////////
function QRwelcomeAdminMenu(){
    add_menu_page('QRwelcome admin','QRwelcome','read','','QRadminData');}

    /////////////////////////////////////
//    Añade el menu
/////////////////////////////////////
add_action('admin_menu','QRwelcomeAdminMenu');

/////////////////////////////////////////////////////////////////////
//    Añadir varaible a variables de wordpress para que no la ignore
/////////////////////////////////////////////////////////////////////
function QRadd_my_var($public_query_vars) {
    $public_query_vars[] = 'qr';
    return $public_query_vars; }
add_filter('query_vars', 'QRadd_my_var');

////////////////////////////////////////////////
//    Añade tabla dedicada para QRWelcome
////////////////////////////////////////////////
function newQRwelcomeTable(){
    global $wpdb;
    $table_name = $wpdb->prefix . "QRwelcome";
    $sql = " CREATE TABLE $table_name(
        Visitid int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        VisitIP VARCHAR(1000) NOT NULL
        ) ;";
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);}

// Lanza "nueva tabla" cuando el plugin es activado
register_activation_hook(__FILE__,'newQRwelcomeTable');


?>
