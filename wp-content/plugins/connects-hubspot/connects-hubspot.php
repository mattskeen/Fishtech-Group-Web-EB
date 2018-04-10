<?php
/**
* Plugin Name: Connects - Hubspot Addon
* Plugin URI: 
* Description: Use this plugin to integrate Hubspot with Connects.
* Version: 1.1.1
* Author: Brainstorm Force
* Author URI: https://www.brainstormforce.com/
* License: http://themeforest.net/licenses
*/

if(!class_exists('Smile_Mailer_Hubspot')){
	class Smile_Mailer_Hubspot{
	
		private $slug;
		private $setting;
		function __construct(){

			require_once('hubspot/class.lists.php');
			require_once('hubspot/class.contacts.php');
			add_action( 'wp_ajax_get_hubspot_data', array($this,'get_hubspot_data' ));
			add_action( 'wp_ajax_update_hubspot_authentication', array($this,'update_hubspot_authentication' ));
			add_action( 'wp_ajax_disconnect_hubspot', array($this,'disconnect_hubspot' ));
			add_action( 'wp_ajax_hubspot_add_subscriber', array($this,'hubspot_add_subscriber' ));
			add_action( 'wp_ajax_nopriv_hubspot_add_subscriber', array($this,'hubspot_add_subscriber' ));
            add_action( 'admin_init', array( $this, 'enqueue_scripts' ) );
			$this->setting  = array(
				'name' => 'HubSpot',
				'parameters' => array( 'api_key' ),
				'where_to_find_url' => 'http://help.hubspot.com/articles/KCS_Article/Integrations/How-do-I-get-my-HubSpot-API-key',
				'logo_url' => plugins_url('images/logo.png', __FILE__)
			);
			$this->slug = 'hubspot';
		}
		
		 /*
         * Function Name: enqueue_scripts
         * Function Description: Add custon scripts
         */
        
        function enqueue_scripts() {
            if( function_exists( 'cp_register_addon' ) ) {
                cp_register_addon( $this->slug, $this->setting );
            }
            wp_register_script( $this->slug.'-script', plugins_url('js/'.$this->slug.'-script.js', __FILE__), array('jquery'), '1.1', true );
            wp_enqueue_script( $this->slug.'-script' );
            add_action( 'admin_head', array( $this, 'hook_css' ) );
        }

        /*
         * Function Name: hook_css
         * Function Description: Adds background style script for mailer logo.
         */


        function hook_css() {
            if( isset( $this->setting['logo_url'] ) ) {
                if( $this->setting['logo_url'] != '' ) {
                    $style = '<style>table.bsf-connect-optins td.column-provider.'.$this->slug.'::after {background-image: url("'.$this->setting['logo_url'].'");}.bend-heading-section.bsf-connect-list-header .bend-head-logo.'.$this->slug.'::before {background-image: url("'.$this->setting['logo_url'].'");}</style>';
                    echo $style;
                }
            }
            
        }
		// retrieve mailer info data
		function get_hubspot_data(){

			if ( ! current_user_can( 'access_cp' ) ) {
                die(-1);
            }
			$isKeyChanged = false;
			$connected = false;
			ob_start();
			$hubspot_api = get_option($this->slug.'_api');
			if( $hubspot_api != '' ) {
				try{
					$listsObj = new CP_HubSpot_Lists($hubspot_api);
					$lists = $listsObj->get_static_lists(null);
				} catch ( Exception $ex ) {
					$formstyle = '';
					$isKeyChanged = true;
				}
				if( isset( $lists->status ) ){
					if( $lists->status == 'error' ) {
						$formstyle = '';
						$isKeyChanged = true;
					}
				} else {
					$formstyle = 'style="display:none;"';
				}
            	 
			} else {
            	$formstyle = '';
			}
            ?>
			
			<div class="bsf-cnlist-form-row" <?php echo $formstyle; ?>>
				<label for="cp-list-name"><?php _e( $this->setting['name']." API Key", "smile" ); ?></label>
	            <input type="text" autocomplete="off" id="<?php echo $this->slug; ?>_api_key" name="<?php echo $this->slug; ?>-api-key" value="<?php echo esc_attr( $hubspot_api ); ?>"/>
	        </div>

            <div class="bsf-cnlist-form-row <?php echo $this->slug; ?>-list">
	            <?php
	            if($hubspot_api != ''  && !$isKeyChanged) {
	            	$hs_lists = $this->get_hubspot_lists($hubspot_api);
	            	if( !empty( $hs_lists ) ){
						$connected = true;
					?>
						<label for="<?php echo $this->slug; ?>-list"><?php echo __( "Select List", "smile" ); ?></label>
						<select id="<?php echo $this->slug; ?>-list" class="bsf-cnlist-select" name="<?php echo $this->slug; ?>-list">
					<?php
						foreach($hs_lists as $id => $name) {
					?>
							<option value="<?php echo $id; ?>"><?php echo $name; ?></option>
					<?php
						}
						?>
						</select>
					<?php
					} else {
					?>
						<label for="<?php echo $this->slug; ?>-list"><?php echo __( "You need at least one list added in " . $this->setting['name'] . " before proceeding.", "smile" ); ?></label>
					<?php
					}
	            }
	            ?>	
            </div>

            <div class="bsf-cnlist-form-row">
            	<?php if( $hubspot_api == "" ) { ?>
	            	<button id="auth-<?php echo $this->slug; ?>" class="button button-secondary auth-button" disabled><?php _e( "Authenticate ".$this->setting['name'], "smile" ); ?></button><span class="spinner" style="float: none;"></span>
	            <?php } else {
	            		if( $isKeyChanged ) {
	            ?>
	            	<div id="update-<?php echo $this->slug; ?>" class="update-mailer" data-mailerslug="<?php echo $this->setting['name']; ?>" data-mailer="<?php echo $this->slug; ?>"><span><?php _e( "Your credentials seems to be changed.</br>Use different '" . $this->setting['name'] . " credentials?", "smile" ); ?></span></div><span class="spinner" style="float: none;"></span>
	            <?php
	            		} else {
	            ?>
	            	<div id="disconnect-<?php echo $this->slug; ?>" class="button button-secondary" data-mailerslug="<?php echo $this->setting['name']; ?>" data-mailer="<?php echo $this->slug; ?>"><span><?php _e( "Use different '".$this->setting['name']."' account?", "smile" ); ?></span></div><span class="spinner" style="float: none;"></span>
	            <?php
	            		}
	            ?>
	            <?php } ?>

	        </div>

            <?php
            $content = ob_get_clean();
            
            $result['data'] = $content;
            $result['helplink'] = $this->setting['where_to_find_url'];
            $result['isconnected'] = $connected;
            echo json_encode($result);
            exit();

		}
				
		function hubspot_add_subscriber(){
			$ret = true;
			$email_status = false;
			$proceedStatus = true;

	 		$contact = array_map( 'sanitize_text_field', wp_unslash( $_POST['param'] ) );

			$style_id = isset( $_POST['style_id'] ) ? esc_attr( $_POST['style_id'] ) : '';
			
			if( isset( $_POST['style_id'] ) ){
				check_ajax_referer( 'cp-submit-form-'.$style_id );
			}

			$contact['source'] = ( isset( $_POST['source'] ) ) ? esc_attr( $_POST['source'] ) : '';

			$msg = isset( $_POST['message'] ) ? $_POST['message'] : __( 'Thanks for subscribing. Please check your mail and confirm the subscription.', 'smile' );

			$email = sanitize_email( $_POST['param']['email'] );

			if( isset( $email ) ) {
				$email_status = ( !( isset( $_POST['only_conversion'] ) ? true : false ) ) ? apply_filters('cp_valid_mx_email', $email ) : false;
			}

			if ( is_user_logged_in() && current_user_can( 'access_cp' ) ) {
				$default_error_msg = __( 'THERE APPEARS TO BE AN ERROR WITH THE CONFIGURATION.', 'smile' );
			} else {
				$default_error_msg = __( 'THERE WAS AN ISSUE WITH YOUR REQUEST. Administrator has been notified already!', 'smile' );
			}

			$this->api_key = get_option($this->slug.'_api');
		
			if($email_status) {
				if( function_exists( "cp_add_subscriber_contact" ) ){
					$option = isset( $_POST['option'] ) ? esc_attr( $_POST['option'] ) : '';
					$isuserupdated = cp_add_subscriber_contact( $option ,$contact );
				}

				if ( !$isuserupdated ) {  // if user is updated dont count as a conversion
						// update conversions
						smile_update_conversions($style_id);
				}

				if( isset( $email ) ) {
					$status = 'success';
				    try {
				    	$contacts = new CP_HubSpot_Contacts($this->api_key);
					    //Create Contact
					    $params =  array('email' => $email );
					    foreach( $_POST['param'] as $key => $p ) {
	                        if( $key != 'email' && $key != 'user_id' && $key != 'date' && strtolower( $key ) != 'firstname' ){
	                            $params[$key] = $p;
	                        }
	                        if( strtolower( $key ) == 'firstname' ) {
	                        	$params['firstname'] = $p;
	                        }
	                    }

	                    $errorMsg = '';

					    $createdContact = $contacts->create_contact($params); 
					    if(isset($createdContact->{'status'}) && $createdContact->{'status'} == 'error'){
					    	$contactProfile = isset( $createdContact->identityProfile ) ? $createdContact->identityProfile : '';
					    	$contactID = isset( $contactProfile->vid ) ? $contactProfile->vid : '';
					    	if( $contactID != '' ) {
					    		$contacts->update_contact($contactID,$params);	
					    	} else {
					    		if( isset( $_POST['source'] ) ) {
					                return false;
					            } else {
					            	//api change
					            	//var_dump($createdContact->{'message'});
					            	if ( is_user_logged_in() && current_user_can( 'access_cp' ) ) {
						                $detailed_msg = isset($createdContact->{'message'}) ? $createdContact->{'message'} : '';
						            } else {
						                $detailed_msg = '';
						            }
						            if( $detailed_msg !== '' && $detailed_msg!== null ) {
						                $page_url = isset( $_POST['cp-page-url'] ) ? esc_url( $_POST['cp-page-url'] ) : '';

						                // notify error message to admin
						                if( function_exists('cp_notify_error_to_admin') ) {
						                    $result   = cp_notify_error_to_admin($page_url);
						                }
						            }
							   		print_r(json_encode(array(
										'action' => ( isset( $_POST['message'] ) ) ? 'message' : 'redirect',
										'email_status' => $email_status,
										'status' => 'error',
										'message' => $default_error_msg,
										'detailed_msg' => $detailed_msg,
										'url' => ( isset( $_POST['message'] ) ) ? 'none' : esc_url( $_POST['redirect'] ),
									)));
									exit();
								}
					    	}
					    				    	
					    } else {
					    	$contactID = $createdContact->{'vid'};
					    }

					    if( $_POST['list_id'] != -1 ) {

					    	$lists = new CP_HubSpot_Lists($this->api_key);
						   	$contacts_to_add = array($contactID);
						   	 
						   	$add_res = $lists->add_contacts_to_list($contacts_to_add,$_POST['list_id']);
						   	$add_res = json_decode( $add_res );

						   	if( isset( $add_res->status ) ) {
							   	if( $add_res->status == 'error' ) {
							   		$proceedStatus = false;
							   		if ( is_user_logged_in() && current_user_can( 'access_cp' ) ) {
						                $detailed_msg = isset( $add_res->message ) ? $add_res->message : '';
						            } else {
						                $detailed_msg = '';
						            }
							   	}
						   	}

						} else {
					    	$detailed_msg = '';
					    }

						    
					   	$ret = true;
					   	
					   	if( !$proceedStatus ) {
					   		if( isset( $_POST['source'] ) ) {
				                return false;
				            } else {
					            if( $detailed_msg !== '' && $detailed_msg!== null ) {
					                $page_url = isset( $_POST['cp-page-url'] ) ? esc_url( $_POST['cp-page-url'] ) : '';

					                // notify error message to admin
					                if( function_exists('cp_notify_error_to_admin') ) {
					                    $result   = cp_notify_error_to_admin($page_url);
					                }
					            }
						   		print_r(json_encode(array(
									'action' => ( isset( $_POST['message'] ) ) ? 'message' : 'redirect',
									'email_status' => $email_status,
									'status' => 'error',
									'message' => $default_error_msg,
									'detailed_msg' => $detailed_msg,
									'url' => ( isset( $_POST['message'] ) ) ? 'none' : esc_url( $_POST['redirect'] ),
								)));
								exit();
							}
						}
					} catch (Exception $e) {
						if( isset( $_POST['source'] ) ) {
			                return false;
			            } else {
							print_r(json_encode(array(
								'action' => ( isset( $_POST['message'] ) ) ? 'message' : 'redirect',
								'email_status' => $email_status,
								'status' => 'error',
								'message' => __( "Something went wrong. Please try again.", "smile" ),
								'url' => ( isset( $_POST['message'] ) ) ? 'none' : esc_url( $_POST['redirect'] ),
							)));
							exit();
						}
					}
					
				}
			} else {
				if( isset( $_POST['only_conversion'] ) ? true : false ){
					// update conversions 
					$status = 'success';
					smile_update_conversions( $style_id );
					$ret = true;
				} else if( isset( $email ) ) {
                    $msg = ( isset( $_POST['msg_wrong_email']  )  && $_POST['msg_wrong_email'] !== '' ) ? $_POST['msg_wrong_email'] : __( 'Please enter correct email address.', 'smile' );
                    $status = 'error';
                    $ret = false;
                } else if( !isset( $email ) ) {
                    //$msg = __( 'Something went wrong. Please try again.', 'smile' );
                    $msg  = $default_error_msg;
                    $errorMsg = __( 'Email field is mandatory to set in form.', 'smile' );
                    $status = 'error';
                }
			}

			if ( is_user_logged_in() && current_user_can( 'access_cp' ) ) {
                $detailed_msg = $errorMsg;
            } else {
                $detailed_msg = '';
            }

            if( $detailed_msg !== '' && $detailed_msg!== null ) {
                $page_url = isset( $_POST['cp-page-url'] ) ? esc_url( $_POST['cp-page-url'] ) : '';

                // notify error message to admin
                if( function_exists('cp_notify_error_to_admin') ) {
                    $result   = cp_notify_error_to_admin($page_url);
                }
            }

			if( isset( $_POST['source'] ) ) {
                return $ret;
            } else {
            	print_r(json_encode(array(
					'action' => ( isset( $_POST['message'] ) ) ? 'message' : 'redirect',
					'email_status' => $email_status,
					'status' => $status,
					'message' => $msg,
					'detailed_msg' => $detailed_msg,
					'url' => ( isset( $_POST['message'] ) ) ? 'none' : esc_url( $_POST['redirect'] ),
				)));
				exit();
            }
		}

		function update_hubspot_authentication(){

			if ( ! current_user_can( 'access_cp' ) ) {
                die(-1);
            }
			$post = $_POST;

			$data = array();
			$HAPIKey = trim( sanitize_text_field( $post['api_key'] ) );

			if( $post['api_key'] == '' ) {
				print_r(json_encode(array(
					'status' => "error",
					'message' => __( "Please provide valid API Key for your " . $this->setting['name'] . " account.", "smile" )
				)));
				exit();
			}

			try{
				$listsObj = new CP_HubSpot_Lists( $HAPIKey );
				$lists = $listsObj->get_static_lists(null);
			} catch( Exception $ex ) {
				print_r(json_encode(array(
					'status' => "error",
					'message' => __( "Something went wrong. Please try again.", "smile" )
				)));
				exit();
			}
			
			if( isset( $lists->status ) ){
				if( $lists->status == 'error' ) {
					print_r(json_encode(array(
						'status' => "error",
						'message' => __( "Failed to authenticate. Please check API Key", "smile" )
					)));
					exit();
				}
			}
			
			if( is_array( $lists->lists ) && empty( $lists->lists ) ) {
				print_r(json_encode(array(
					'status' => "error",
					'message' => __( "You have zero static lists in your HubSpot account. You must have at least one static list before integration." , "smile" )
				)));
				exit();
			}
        	ob_start();
			$hs_lists = array();
			$html = $query = '';
			?>
			<label for="<?php echo $this->slug; ?>-list"  >Select List</label>
			<select id="<?php echo $this->slug; ?>-list" class="bsf-cnlist-select" name="<?php echo $this->slug; ?>-list">
			<?php
			foreach($lists->lists as $offset => $list) {
			?>
				<option value="<?php echo $list->listId; ?>"><?php echo $list->name; ?></option>
			<?php
				$query .= $list->listId.'|'.$list->name.',';
				$hs_lists[$list->listId] = $list->name;
			}
			?>
			</select>
			<input type="hidden" id="mailer-all-lists" value="<?php echo $query; ?>"/>
			<input type="hidden" id="mailer-list-action" value="update_<?php echo $this->slug; ?>_list"/>
			<input type="hidden" id="mailer-list-api" value="<?php echo $HAPIKey; ?>"/>

			<div class="bsf-cnlist-form-row">
				<div id='disconnect-<?php echo $this->slug; ?>' class='button button-secondary' data-mailerslug='<?php echo $this->setting['name'] ?>' data-mailer='<?php echo $this->slug; ?>'>
					<span>
						<?php echo _e( "Use different '".$this->setting['name']."' account?", "smile" ); ?>
					</span>
				</div>
				<span class='spinner' style='float: none;'></span>
			</div>
			<?php 
			$html .= ob_get_clean();
			update_option($this->slug.'_api',$HAPIKey);
			update_option($this->slug.'_lists',$hs_lists);	

			print_r(json_encode(array(
				'status' => "success",
				'message' => $html
			)));
			
			exit();
		}
		
		
		function disconnect_hubspot(){

			if ( ! current_user_can( 'access_cp' ) ) {
                die(-1);
            }

			delete_option( $this->slug.'_api' );
			delete_option( $this->slug.'_lists' );
			
			$smile_lists = get_option('smile_lists');			
			if( !empty( $smile_lists ) ){ 
				foreach( $smile_lists as $key => $list ) {
					$provider = $list['list-provider'];
					if( strtolower( $provider ) == strtolower( $this->slug ) ){
						$smile_lists[$key]['list-provider'] = "Convert Plug";
						$contacts_option = "cp_" . $this->slug . "_" . preg_replace( '#[ _]+#', '_', strtolower( $list['list-name'] ) );
                        $contact_list = get_option( $contacts_option );
                        $deleted = delete_option( $contacts_option );
                        $status = update_option( "cp_connects_" . preg_replace( '#[ _]+#', '_', strtolower( $list['list-name'] ) ), $contact_list );
					}
				}
				update_option( 'smile_lists', $smile_lists );
			}
			
			print_r(json_encode(array(
                'message' => "disconnected",
			)));
			exit();
		}

		/*
		 * Function Name: get_hubspot_lists
		 * Function Description: Get HubSpot Mailer Campaign list
		 */

		function get_hubspot_lists( $api_key = '' ) {
			if( $api_key != '' ) {
				try{
					$listsObj = new CP_HubSpot_Lists($api_key);
					$lists = $listsObj->get_static_lists(null);
				} catch ( Exception $ex ) {
					return array();
				}
					
				if( isset( $lists->status ) ){
					if( $lists->status == 'error' ) {
						return array();
					}
				} else {
					$hs_lists = array();
					foreach($lists->lists as $offset => $list) {
						$hs_lists[$list->listId] = $list->name;
					}
					$hs_lists[-1] = __( 'Proceed without a list. Just add contacts.', 'smile' );
					return $hs_lists;
				}
			} else {
				return array();
			}
		}
	}
	new Smile_Mailer_Hubspot;	
}