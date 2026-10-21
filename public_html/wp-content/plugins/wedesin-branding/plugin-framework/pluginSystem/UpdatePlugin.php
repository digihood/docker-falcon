<?php
namespace sitemap\framework\pluginSystem;
use sitemap\framework\d1g1Session;
use sitemap\framework\Globals;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if( ! class_exists( 'UpdatePlugin' ) )
{
	class UpdatePlugin
	{   
     

        static function Update_Plugin(){
          
            $license = d1g1license::check_update();
          
            if(!empty($license) && is_licensing()){
                if(isset($license->token) && $license->token){ 
                    if(isset($_SESSION['lisense']) && $_SESSION['lisense'] == true){
                        d1g1Session::remove_session('lisense');
                    }
                    if(Globals::$FWDIGI_URL && D1G1_GITHUBREP_BRAND){
                    
                        $myUpdateChecker = \Puc_v4_Factory::buildUpdateChecker(
                            D1G1_GITHUBURL_BRAND,
                            Globals::$FWDIGI_PATHSLUG.'/plugin.php',
                            D1G1_GITHUBREP_BRAND
                        );
                        $myUpdateChecker->setBranch('master');
                        $myUpdateChecker->setAuthentication($license->token);
                        $myUpdateChecker->getVcsApi()->enableReleaseAssets();


                    }
                    d1g1Session::remove_session(D1G1_SITEMAP);
                }else {
                    if(isset($license['message']) && $license['message']){
                        d1g1Session::add_session(D1G1_SITEMAP,$license);
                    }
                }
            }else{

                if(D1G1_GITHUBURL_BRAND && D1G1_GITHUBREP_BRAND){

                    $myUpdateChecker = \Puc_v4_Factory::buildUpdateChecker(
                        D1G1_GITHUBURL_BRAND,
                        Globals::$FWDIGI_PATHSLUG.'/plugin.php',
                        D1G1_GITHUBREP_BRAND
                    );
                    $myUpdateChecker->setBranch('master');
                    $myUpdateChecker->setAuthentication(GithubToken);
                    $myUpdateChecker->getVcsApi()->enableReleaseAssets();


                }
            }
            add_filter( 'site_transient_update_plugins', [static::class , 'stop_plugin_updates'] );  

        }

        static function stop_plugin_updates( $value ) {
            unset( $value->response[D1G1_SITEMAP.'/setup.php']->package );
            return $value;
        }
    }
    new UpdatePlugin;
}