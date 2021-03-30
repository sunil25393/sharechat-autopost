<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://sunilprajapati.com/
 * @since      1.0.0
 *
 * @package    Sharechat_Autopost
 * @subpackage Sharechat_Autopost/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Sharechat_Autopost
 * @subpackage Sharechat_Autopost/admin
 * @author     Sunil Prajapati <sdprajapati999@gmail.com>
 */
class Sharechat_Autopost_Admin {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

        add_action('init', array($this, 'check_for_newpost_for_sharechat'));
       // add_action('my_hourly_cronjob_action', array($this, 'check_for_newpost_for_sharechat'));
        add_action('category_add_form_fields', array($this, 'category_add_sharechat_catid'), 10, 2);
        add_action('category_edit_form_fields', array($this, 'category_edit_sharechat_catid'), 10);
        add_action('edited_category', array($this, 'category_save_sharechat_catid'));
        add_action('create_category', array($this, 'category_save_sharechat_catid'));
        add_filter('cron_schedules', array($this, 'hourly_cron_schedules'));

        if (!wp_next_scheduled('my_hourly_cronjob_action')) {
            wp_schedule_event(time(), '5min', 'my_hourly_cronjob_action');
        }
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Sharechat_Autopost_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Sharechat_Autopost_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/sharechat-autopost-admin.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Sharechat_Autopost_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Sharechat_Autopost_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/sharechat-autopost-admin.js', array('jquery'), $this->version, false);
    }

    public function send_post_to_sharechat($postdetails) {

        if (empty($postdetails)) {
            return false;
        }

        /* access details */
        $token = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VybmFtZSI6InRhbWlsbWludXRlcyIsImFjY291bnRJZCI6IjUwNzYiLCJ1c2VySWQiOiIyNDAyMDA1MjkiLCJpYXQiOjE1NzE3MzEzMjd9.dLQrJQ4AnEdSoBB1QIVVv5dOZNNSZJAPYVox-umnXjY';
        $username = 'tamilminutes';
        $time = time();



        $data = array(
            'token' => $token,
            'username' => $username,
            'time' => $time, 
        );
		
		if(!empty($postdetails['tags'])){
			  $data['tags']=$postdetails['tags'];
		}
		if($postdetails['image']!=''){
			 $data['image']=$postdetails['image'];
		}else{
			return false;
		}
		if($postdetails['title']!=''){
			 $data['title']=$postdetails['title'];
		}
		if($postdetails['link']!=''){
			 $data['link']=$postdetails['link'];
		}
		if($postdetails['description']!=''){
			 $data['description']=$postdetails['description'];
		}
		if($postdetails['text']!=''){
			 $data['text']=$postdetails['text'];
		}
		if($postdetails['gif']!=''){
			 $data['gif']=$postdetails['gif'];
		}
		if($postdetails['video']!=''){
			 $data['video']=$postdetails['video'];
		}

        $data_json = json_encode($data);

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "https://yxkvd37cra.execute-api.ap-south-1.amazonaws.com/prod/api-push-post");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);

        $apiResult = curl_exec($ch);

        $responsedata = json_decode($apiResult);
		 
        if ($responsedata->status == 'Success') {
            return true;
        } else {
            return false;
        }
    }

    public function hourly_cron_schedules($schedules) {
        if (!isset($schedules["5min"])) {
            $schedules["5min"] = array(
                'interval' => 30*60,
                'display' => __('Once every 5 minutes'));
        }

        return $schedules;
    }

    public function check_for_newpost_for_sharechat() {

         
        $allposts = get_posts(array(
            'fields' => 'ids',
            'posts_per_page' => -1,
            'orderby' => 'ID',
            'order' => 'ASC',
            'meta_query' => array(
                array(
                    'key' => '_sharechat_posted',
                    'compare' => 'NOT EXISTS'
                )
            )
        ));
 
        if (!empty($allposts)) {
            foreach ($allposts as $post) {

                $postObj = get_post($post);
                $parray = array();
                $parray['title'] = $postObj->post_title;
                $parray['link'] = get_permalink($post);
                $parray['image'] = get_the_post_thumbnail_url($post, 'full');
                $parray['title'] = $postObj->post_title;
                $parray['tags'] = $this->get_sharechat_post_tag_ids($post);
				 
				 if($parray['image']==''){
				 $parray['description'] = $this->sort_my_string($postObj->post_content,100);
				 }
				
               if ($this->send_post_to_sharechat($parray)) {
                    add_post_meta($post, '_sharechat_posted', 'no');
				
                }else{
					add_post_meta($post, '_sharechat_posted', 'no');
				}
				
            }
		 
        }
    }

    public function get_sharechat_post_tag_ids($post) {
        $cats = wp_get_post_categories($post);
        $tags = [];
        if (!empty($cats)) {

            foreach ($cats as $cat) {
                $sharechat_catid = get_term_meta($cat, 'sharechat_catid', true);
                $tags[] = $sharechat_catid;
            }
        }
        return $tags;
    }

    public function category_add_sharechat_catid($term) {
        ?>
        <div class="form-field">
            <label for="sharechat_catid"><?php _e('Sharechat Category Id'); ?></label>
            <input type="text" name="sharechat_catid" id="sharechat_catid" value="">
        </div>
        <?php
    }

    public function category_edit_sharechat_catid($term) {

        // put the term ID into a variable
        $t_id = $term->term_id;

        $sharechat_catid = get_term_meta($t_id, 'sharechat_catid', true);
        ?>
        <tr class="form-field">
            <th><label for="sharechat_catid"><?php _e('Sharechat Category Id', 'yourtextdomain'); ?></label></th>
            <td>	 
                <input type="text" name="sharechat_catid" id="sharechat_catid" value="<?php echo esc_attr($sharechat_catid) ? esc_attr($sharechat_catid) : ''; ?>">
            </td>
        </tr>
        <?php
    }

    public function category_save_sharechat_catid($term_id) {

        if (isset($_POST['sharechat_catid'])) {
            $term_image = $_POST['sharechat_catid'];
            if ($term_image) {
                update_term_meta($term_id, 'sharechat_catid', $term_image);
            }
        }
    }
	
	
	
  public  function sort_my_string($string, $lenght = 50) {
    if (strlen($string) > $lenght) {
        return substr($string, 0, $lenght) . "... ";
    } else {
        return $string;
    }
   }
}