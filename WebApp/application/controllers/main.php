<?php defined('SYSPATH') or die('No direct script access.');
/**
 * This is the controller for the main site.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Main Controller  
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */
 
 require(APPPATH.'controllers/admin/messages.php');
 
 //Include the service wrapper
include_once(APPPATH.'controllers/admin/ServiceWrapper.php');

class GenericClass { 
	private $internalData;
	
	public function __construct() {
		$this->internalData = array();
	}

  public function __get($name) { 
    return $this->internalData[$name];
  } 
  public function __set($name, $value) { 
    $this->internalData[$name] = $value; 
  } 
}
 
 
class util{
/** 
#########  Apala required changes ##########
	
		function showtags($id,$tablename) its dbcalls need to come for the API

*/


	/**
			Get the tags for display in the home page.
		*/
		public static function showtags($id,$tablename)
		{
				$db = new Database();
				$sql1 = "SELECT id,  tagged_id,  tablename,  tags   FROM tags WHERE tagged_id = ".$id." AND tablename = '".$tablename."' AND correct_yn = 1 ";
				$tags = $db->query($sql1);
				$tagnew_tags = "";
				foreach($tags as $tag)
				{
					$tagnew_tags .= "<a href='".url::base()."taggedfeeds/index/page/1/tag/".$tag->tags."' class='tagged'  title='View other feeds with tag, ".$tag->tags.".'  >".$tag->tags."</a>&nbsp;<a href=\"javascript:mark_tag_false(".$tag->id.",".$tag->tagged_id.",'".$tablename."')\" title='Mark tag as incorrect' >x</a>&nbsp;&nbsp;" ;			
				}				
				return 	$tagnew_tags;	
		}
		
		
/** 
#########  Apala required changes ##########
	
		function get_category_name($id) this function only returns the title of a category.
		Does it also need to come from the API?

*/
		
		/**
		 Get the categeories for display in the home page.
		*/		
		public static function get_category_name($id)		
		{
		/** #########  Apala required changes ########## DB Call below. ::factory('category')*/
		$category = ORM::factory('category')->where('id',$id)->find_all();

		$category_title = $category[0]->category_title;              
		
		return   $category_title;  
		}

	} 
 
 
class Main_Controller extends Template_Controller {

    public $auto_render = TRUE;
	
    // Main template
    public $template = 'layout';
	
    // Cache instance
    protected $cache;

	// Session instance
	protected $session;
	
	protected $API_URL = "http://local.swiftcore.com/ServiceAPI/ChannelProcessingJobServices/";
	
	protected $API_URL2 = "http://local.swiftcore.com/ServiceAPI/ContentServices/";
	
		/** #########  Apala required changes ##########
		settings should come for api, ALL the lines in the function _contruct()	*/
		
    public function __construct()
    {
        parent::__construct();

        /*
         * APALA - Call the core to run the next due processing job
         */
        $coreFolder = dirname(__FILE__) . "/../Core/";
        $apiFile = $coreFolder."ServiceAPI/ChannelProcessingJobServices/RunNextProcessingJob.php";
        $postData = array("key" => "swiftriver_apala");
        $content = http_build_query($postData, '', '&');
        $context = stream_context_create(
            array(
                'http' => array(
                    'method' => 'POST',
                    'header' => 'Content-type: application/x-www-form-urlencoded;',
                    'content' => $content,
                    'timeout' => null
                ),
            ));
        $uri = url_Core::base();
        $uri = str_replace("WebApp/", "", $uri);
        @fopen($uri."/Core/ServiceAPI/ChannelProcessingJobServices/RunNextProcessingJob.php", 'rb', false, $context);


        // Load cache
        $this->cache = new Cache;

		

		// Load Session
		$this->session = Session::instance();
		
        // Load Header & Footer
        $this->template->header  = new View('header');
        $this->template->footer  = new View('footer');        
		
		//call the feedback form
		//$this->_get_feedback_form();
        
		// Retrieve Default Settings
		$site_name = Kohana::config('settings.site_name');
			// Prevent Site Name From Breaking up if its too long
			// by reducing the size of the font
			if (strlen($site_name) > 20)
			{
				$site_name_style = " style=\"font-size:21px;\"";
			}
			else
			{
				$site_name_style = "";
			}
        $this->template->header->site_name = $site_name;
		$this->template->header->site_name_style = $site_name_style;
		$this->template->header->site_tagline = Kohana::config('settings.site_tagline');
        $this->template->header->api_url = Kohana::config('settings.api_url');

		// Display Contact Tab?
		$this->template->header->site_contact_page = Kohana::config('settings.site_contact_page');
				
		// Display Help Tab?
		$this->template->header->site_help_page = Kohana::config('settings.site_help_page');
		
		// Get Custom Pages
		/** #########  Apala required changes ########## DB Call below. ::factory('page')*/
		$this->template->header->pages = ORM::factory('page')->where('page_active', '1')->find_all();
        
        // Get custom CSS file from settings
        $this->template->header->site_style = Kohana::config('settings.site_style');
		
				// Display News Feed?
				$this->template->header->allow_feed = Kohana::config('settings.allow_feed');
				
				// Javascript Header
				$this->template->header->map_enabled = FALSE;
				$this->template->header->validator_enabled = TRUE;
				$this->template->header->datepicker_enabled = FALSE;
				$this->template->header->photoslider_enabled = FALSE;
				$this->template->header->videoslider_enabled = FALSE;
				$this->template->header->protochart_enabled = FALSE;
				$this->template->header->main_page = FALSE;				
				$this->template->header->this_page = "";
				
				// Google Analytics
				$google_analytics = Kohana::config('settings.google_analytics');
				$this->template->footer->google_analytics = $this->_google_analytics($google_analytics);
				
				// *** Locales/Languages ***
				// First Get Available Locales
				$this->template->header->locales_array = $this->cache->get('locales');
				
				// Locale form submitted?
				if (isset($_GET['l']) && !empty($_GET['l']))
				{
					$this->session->set('locale', $_GET['l']);
				}
				// Has a locale session been set?
				if ($this->session->get('locale',FALSE))
				{
					// Change current locale
					Kohana::config_set('locale.language', $_SESSION['locale']);
				}
				$this->template->header->l = Kohana::config('locale.language');
				
				//Set up tracking gif
				if($_SERVER['SERVER_NAME'] != 'localhost' && $_SERVER['SERVER_NAME'] != '127.0.0.1'){
					$track_url = $_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'];
				}else{
					$track_url = 'null';
				}
				$this->template->footer->tracker_url = 'http://tracker.ushahidi.com/track.php?url='.urlencode($track_url).'&lang='.$this->template->header->l.'&version='.Kohana::config('version.ushahidi_version');
		    
				// Load profiler
        // $profiler = new Profiler;
        
        // Get tracking javascript for stats
        $this->template->footer->ushahidi_stats = $this->_ushahidi_stats();
    }
    
   
/** 
#########  Apala required changes ##########
	
		These operations/functions should go through the API  BEGINING line 186 to 565

*/ 
    /**
    		Mark as irrevant.
    */
    public function mark_irrelevant($feedid,$categoryid)
		{
					$this->auto_render=false;
					$service = new ServiceWrapper($this->API_URL2."MarkContentAsChatter.php");
					$this->change_source_rating($feedid,$service);				
			//	url::redirect("/main/index/category/".$categoryid."/page/1");
		}
		/**
    		change the weight of the feed source.
    */
    
    public function increment_source_rating($feedid,$sourceid)
    {
			$this->auto_render=false;
			$service = new ServiceWrapper($this->API_URL2."MarkContentAsAcurate.php");
			$this->change_source_rating($feedid,$service);
			
    }
    public function decrement_source_rating($feedid,$categoryid)
    {
    		$this->auto_render=false;
    		$service = new ServiceWrapper($this->API_URL2."MarkContentAsInacurate.php");
    		$this->change_source_rating($feedid,$service);
    }
    private function change_source_rating($feedid,$service)
		{
				$json = $service->MakePOSTRequest(array("key" => "test", "data" => '{"id":"'.$feedid.'","markerId":"'.$feedid.'"}'), 10);
				$return = json_decode($json);		
		   	if($return->message == "OK")
				{	
						echo json_encode(array('message' => 'success'));//'<span style=color:green >Success!</span>'));		
				}else
				{
						echo json_encode(array('message' => $return->error));				
				}
	   			
		}
		
    /**
    		This function submits reports to the ushahidi instance API
    */
    public function submit_report_via_API($feedid,$categoryid)
		{
				//if(request::is_ajax())
			//	{
					//get information from the database
						$db = new Database();
					  $this->auto_render=false;
					  $sql1 = "";
					
					  //categories news,blogs,others use the feeds table.  others come from the messages table.
					  if($categoryid == 2 || $categoryid == 10 || $categoryid == 11)
						{
								$sql1 =	" 	SELECT 
											 m.id as id
											,m.message as item_title,
											 m.message as item_description,
											  CASE r.service_id 
			 									WHEN 3 THEN CONCAT('http://twitter.com/',m.message_from,'/statuses/',m.service_messageid)
			 									ELSE '#'
			 									END as item_link,
											 m.message_date as item_date,
											 m.message_from as item_source,
											 l.longitude,	l.latitude,	l.location_name ,
											 r.reporter_first,  r.reporter_last ";  
											 
									if ($categoryid == 2 )//sms
											$sql1 .= ", r.reporter_phone as reporter_email" ;		
									else 
											$sql1 .= ", r.reporter_email" ; 	 
											
									$sql1	.= " FROM message m   
													LEFT OUTER JOIN reporter r ON  r.id = m.reporter_id  
													LEFT OUTER JOIN location l ON l.id = r.location_id
												WHERE  submited_to_ushahidi <> 1 AND m.id = ".$feedid ;
						}
						else
						{
								$sql1 = "SELECT 	f.id as id,	item_title,		item_description,		item_link, 
											item_date, 	a.feed_name as item_source,
											l.longitude,	l.latitude,	l.location_name ,
											 '' as reporter_first,  '' as reporter_last,  '' as reporter_email  
												FROM feed_item f 
														 LEFT OUTER JOIN feed a ON f.feed_id = a.id 
														 LEFT OUTER JOIN location l ON l.id = f.location_id
												WHERE  submited_to_ushahidi <> 1 AND f.id = ".$feedid;
						}
														 
														 
						$feeds = $db->query($sql1);
						if(count($feeds) == 0)
						{
							echo json_encode(array('message' => '<span style=color:red >Feed already submited.</span>'));
							return;
						}
							
						$feed = $feeds[0];
						
						$xmlcontent = "task=report";
				  	
						//$reportdata="api?task=report&incident_title=Test&incident_description=Testing+with+the+api.&incident_date=03/18/2009&incident_hour=10&incident_minute=10&incident_ampm=pm&incident_category=2,4,5,7&latitude=-1.28730007&longitude=36.82145118200820&location_name=accra&person_first=Henry+Addo&person_last=Addo&person_email=henry@ushahidi.com&resp=xml "
							
							$xmlcontent .=	"&incident_title=".$feed->item_title; //"</incident_title>" ; // - Required. The title of the incident/report.
							$xmlcontent .=	"&incident_description=".$feed->item_description; //"</incident_description>" ; //incident_description - Required. The description of the incident/report.
							$xmlcontent .=	"&incident_date=".date('m/d/Y', strtotime($feed->item_date)); //"</incident_date>" ;//incident_date - Required. The date of the incident/report. It usually in the format mm/dd/yyyy.
							$xmlcontent .=	"&incident_hour=".date('h', strtotime($feed->item_date)); //"</incident_hour>"; //"incident_hour - Required. The hour of the incident/report. In the 12 hour format.
					  	$xmlcontent .=	"&incident_minute=".date('i', strtotime($feed->item_date)) ; //."</incident_minute>"; //incident_minute - Required. The minute of the incident/report.
							$xmlcontent .=	"&incident_ampm=";
													 if(date('H', strtotime($feed->item_date))<= 12) $xmlcontent .= "am"; else $xmlcontent .= "pm";
								 							//	$xmlcontent .= "</incident_ampm>"; //"incident_ampm - Required. Is the incident/report am or pm. It of the form, am or pm.
							$xmlcontent .=	"&incident_category=".$categoryid; //"</incident_category>";//	"incident_category - Required. The categories the incident/report belongs to. It should be a comma separated value csv
							$xmlcontent .=	"&latitude=".(!empty($feed->latitude) ? $feed->latitude:"0"); //"</latitude>"; //"latitude - Required. The latitude of the location of the incident report.
							$xmlcontent .=  "&longitude=".(!empty($feed->longitude) ?	$feed->longitude:"0") ; //"</longitude>"; //"longitude - Required. The longitude of the location of the incident/report.
							$xmlcontent .=	"&location_name=".(!empty($feed->location_name)? $feed->location_name :"unknown") ; //"</location_name>"; 	//"location_name - Required. The location of the incident/report.
							$xmlcontent .=	!empty($feed->reporter_first) ? "&person_first=".$feed->reporter_first: ""; //"</person_first>"; //person_first - Optional. The first name of the person submitting the incident/report.
							$xmlcontent .=	!empty($feed->reporter_last)? "&person_last=".$feed->reporter_last:""; //"</person_last>"; //person_last - Optional. The last name of the person submitting the incident/report.
							$xmlcontent .=	!empty($feed->reporter_email)? "&person_email=".$feed->reporter_email:""; //."</person_email>"; //person_email - Optional. The email address of the person submitting the incident/report.
							$xmlcontent .=	"&resp=json";//</resp></root>"; 	//resp - Optional. The data exchange, either XML or JSON. When not specified, JSON is used.
								
						//	echo 	$xmlcontent;
						//	exit(0);
/*"
incident_photo[] - Optional. Photos to accompany the incident/report.
incident_news - Optional. A news source regarding the incident/report. A news feed.
incident_video - Optional. A video link regarding the incident/report. Video services like youtube.com, video.google.com, metacafe.com,etc
 "*/
 /** #########  Apala required changes ########## DB Call below. ::factory('settings')*/
						$ushahidi_url = ORM::factory('settings', 1)->ushahidi_url;	
						
						if (empty($ushahidi_url))
						
						{		
								//echo json_encode(array('message' => '<span style=color:red >The ushahidi instance url is not set. Contact Admin.</span>'));
						$ushahidi_url = url::base();
								//return;
						}	
							
						$ch = curl_init(); 
						curl_setopt($ch, CURLOPT_HEADER, 0); 
						curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); 
						curl_setopt($ch, CURLOPT_URL, $ushahidi_url."/api/index?"); 
						curl_setopt($ch, CURLOPT_POST, 1); 
						curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlcontent); 
						$content=curl_exec($ch); 
						
					  //	{"payload":{"success":"true"},"error":{"code":"0","message":"No Error."}}
						
						$status = false;
						if(strlen(strstr($content,"success\":\"true"))>0)
							$status = true;
							
						$rattingIncrement = Kohana::config('settings.on_submit_feed_increment_source_rate_by') ;
						
						if ($status)
						{
									  $sql2 = "";
									  $sql3 = "";
								if($categoryid == 2 || $categoryid == 10 || $categoryid == 11)
							  {				
							  			$sql1 = "UPDATE message SET submited_to_ushahidi = 1 WHERE id=".$feedid ;
							  			$sql2 = "UPDATE reporter SET weight = weight +  ".$rattingIncrement."  WHERE weight + ".$rattingIncrement." <= 100 AND id IN (SELECT reporter_id FROM message WHERE id = ".$feedid." ) ";
											$sql3 = "SELECT weight FROM reporter  WHERE id IN (SELECT reporter_id FROM message WHERE id = ".$feedid." ) ";
							  }
								else
								{
							  			$sql1 = "UPDATE feed_item SET submited_to_ushahidi = 1 WHERE id=".$feedid ;
							  			$sql2 = "UPDATE feed SET weight = weight +  ".$rattingIncrement."  WHERE weight +  ".$rattingIncrement."  <= 100 AND id IN (SELECT feed_id FROM feed_item WHERE id = ".$feedid." ) ";
							  			$sql3 = "SELECT weight FROM feed  WHERE id IN (SELECT feed_id FROM feed_item WHERE id = ".$feedid." ) ";
								}	
								$update = $db->query($sql1);			
								$update = $db->query($sql2);	
								$weightrs = $db->query($sql3);
								$weight_value = round($weightrs[0]->weight,0);										  							  
	
								echo json_encode(array('message' => '<span style=color:green >Success!</span>','weight'=>$weight_value));		
						}
						else
								echo ($content);
						
		//		}
					//url::redirect("/main/index/category/".$categoryid."/page/1");
    }
    
    
    /**
    	This function update the tags.
    */

		private function add_tags($id,$tag,$tablename)
		{
		/** #########  Apala required changes ########## DB Call below and line 417, new Tags_Model(),::factory('tags')*/
					if(ORM::factory('tags')->where('tagged_id',$id)->where('tablename',$tablename)->where('tags.tags',$tag)->count_all() == 0)
					{	
						$tags = new Tags_Model();
						$tags->tagged_id = $id;
						$tags->tablename = $tablename;
						$tags->tags = $tag;
						$tags->save();
					}
		}
		
		/**
				Mark the tag as false
		*/
		public function Ajax_mark_tag_false($tagid,$feedid,$tablename)
		{
				if(request::is_ajax())
				{	
					$this->auto_render=false;
					$db = new Database();
					$sql1 = "UPDATE tags SET correct_yn = 0  WHERE id = ".$tagid." ";
					$tags = $db->query($sql1);		
					$tagnew_tags = util::showtags($feedid,$tablename);
					echo json_encode(array('tags' => $tagnew_tags));	
				}
		}
		
	
		/**
				Add a tags.
		*/		
		public function Ajax_tagging($id,$tag,$tablename)
		{
				if(request::is_ajax())
				{		$this->auto_render=false;
						$this->add_tags($id,$tag,$tablename);		
						$tagnew_tags = util::showtags($id,$tablename);	
						echo json_encode(array('tags' => $tagnew_tags));	
				}
		}
		
		/**
		*		This function help the tagging feeds
		*/
		public function tagging($feed,$object_id,$cat,$category_id,$page_val,$page_no)
		{			
					if($_POST)
					{
							$this->update_tags($object_id,$_POST["tag_$object_id"]);
							url::redirect("/main/index/category/$category_id/page/".$page_no );	
					}			
		}

/**
		*		This function help the veracity selector
		*/
		public function veracity($category_id)
		{			
					if($_POST)
					{
							$_SESSION['veracity_min'] = isset($_POST['veracity_min'])?$_POST['veracity_min']:0;
							$_SESSION['veracity_max'] = isset($_POST['veracity_max'])?$_POST['veracity_max']:100;
					}
							url::redirect("/main/index/category/".$category_id."/page/1" );	
								
		}
		
	/**
	*
	*   //get all the admin feeds in database.
	*/
		private function get_new_feeds($category_id)
		{  		
			//Use the service wrapper to make an async call to get an parser any new content
			$service = new ServiceWrapper($this->API_URL."RunNextProcessingJob.php");
			$service->MakeAsyncPostRequest(array("key" => "test"));
    }

		 
/** 
#########  Apala required changes ##########
	
		These operations/functions should go through the API  BEGIN line 186 - 565

*/ 
		 
		 
		 
/**
This is the index function called by default.


*/
    public function index($categoryname="",$category_id = 0,$page,$page_no)
    {		
        $this->template->header->this_page = 'home';
        $this->template->content = new View('main');
        
        $this->template->content->auth = null;
        
       if(isset( $_SESSION['auth_user']))
       {
         $this->template->content->auth = $_SESSION['auth_user'] ;
			 }
			//try getting new feeds and cache them to the database.
		
/** #########  Apala required changes ##########	
		These operations/functions should go through the API  BEGIN line 569 - 609
*/ 
		 
			  $this->get_new_feeds($category_id);
				$messages = new Messages_Controller();
				$messages->auto_render=false;
				
				if($category_id == 11)
				{
					$messages->load_tweets();
				}
			
        // Get all active top level categories
        $parent_categories = array();
        /** #########  Apala required changes ########## DB Call below. ::factory('category')*/
        foreach (ORM::factory('category')
				->where('category_visible', '1')
				->where('parent_id', '0')
				->find_all() as $category)
        {
            // Get The Children
			$children = array();
			foreach ($category->children as $child)
			{
				$children[$child->id] = array( 
					$child->category_title, 
					$child->category_color
				);
			}
			
			// Put it all together
        $parent_categories[$category->id] = array( 
				$category->category_title, 
				$category->category_color,
				$children
			);
        }
        $this->template->content->categories = $parent_categories;


		
		
		// Get Default Color
		$this->template->content->default_map_all = Kohana::config('settings.default_map_all');
		
		// Get Twitter Hashtags
		$this->template->content->twitter_hashtag_array = array_filter(array_map('trim', 
			explode(',', Kohana::config('settings.twitter_hashtags'))));
		
		// Get Report-To-Email
		$this->template->content->report_email = Kohana::config('settings.site_email');
		
/** #########  Apala required changes ##########	
		These settings should go through the API  BEGIN line 671 - 684
*/ 
		// Get SMS Numbers
		$phone_array = array();
		$sms_no1 = Kohana::config('settings.sms_no1');
		$sms_no2 = Kohana::config('settings.sms_no2');
		$sms_no3 = Kohana::config('settings.sms_no3');
		if (!empty($sms_no1)) {
			$phone_array[] = $sms_no1;
		}
		if (!empty($sms_no2)) {
			$phone_array[] = $sms_no2;
		}
		if (!empty($sms_no3)) {
			$phone_array[] = $sms_no3;
		}
		$this->template->content->phone_array = $phone_array;
		

	
	//Cache items to the database.
		
/** #########  Apala required changes ##########	
		These variables and sql statements should go through the API  BEGIN line 691 - 812
*/ 
	// Filter By Category
		  $categoryYes = ( isset($category_id) && !empty($category_id) && !$category_id == 0 );		
		  $category_filter = $categoryYes	? "  a.category_id = ".$category_id."  " : " 1=1 ";		  		
		  $category_filter2 =	" r.service_id = ".($category_id == 2?" 1 ":($category_id == 10? " 2 " : " 3 "));		
		  
		  $veracity_filter =	"";
		  if(isset( $_SESSION['veracity_min']) && isset( $_SESSION['veracity_max'])){
			 $veracity_filter =	"	AND weight >=	".$_SESSION['veracity_min']." AND weight <= ".$_SESSION['veracity_max']." ";
			}	
			else
			{
				$veracity_filter =	"	AND weight >=	0 AND weight <= 100 ";
			}

		$numItems_per_page =  Kohana::config('settings.items_per_page');
		

                $coreFolder = DOCROOT . "/../Core/";
                $coreSetupFile = $coreFolder."Setup.php";
                $workflowFile = $coreFolder."Workflows/GetPagedContentByState.php";
                $workflowData = json_encode(array("state" => "new_content", "pagestart" => ($page_no-1)*$numItems_per_page, "pagesize" => $numItems_per_page));
                include_once($coreSetupFile);
                $workflow = new \Swiftriver\Core\Workflows\ContentServices\GetPagedContentByState();
                $json = $workflow->RunWorkflow($workflowData, "swiftriver_apala");
                $return = json_decode($json);

                /* APALA - removed in favor of calls to the file
                $docroot = DOCROOT;
                $service = new ServiceWrapper("http://local.apala.com/Core/ServiceAPI/ContentServices/GetPagedContentByState.php");
                $params = json_encode(array("state" => "new_content", "pagestart" => ($page_no-1)*$numItems_per_page, "pagesize" => $numItems_per_page));
                $json = $service->MakePOSTRequest(array("key" => "swiftriver_apala", "data" => $params), 5);
                //echo($json);
                $return = json_decode($json);
                */

                $Feedlist = array();
                if(isset($return->contentitems)) {
                    foreach($return->contentitems as $content)
                                {
                                    $feed = new GenericClass() ;
                                    $feed->item_source = $content->source->name; // $content->source->id;
                                    $feed->sourceid = $content->source->id ;
                                     $feed->category_id = 5 ; //REMEBER to remove this hard coded category ID.
                                     $feed->weight =	($content->source->score ? $content->source->score : "not yet rated");
                                     $feed->id = $content->id;
                                     // echo $content->state ;echo "<br/>";
                                     $feed->item_date = date("c", $content->date);
                                     $feed->item_link = $content->link ;
                                     //echo $content->text[0]->languageCode;echo "<br/>";
                                     $feed->item_title = $content->text[0]->title;
                                     $feed->item_description = $content->text[0]->text[0];
                                     $feed->tags = $content->tags;

                                     $Feedlist[] = $feed;

        }
    }
       
				
		$pagination = new Pagination(array(
				'base_url' => '/main/index/category/5', //.$category_id ,
				'uri_segment' => 'page',
				'items_per_page' => (int) $numItems_per_page,
				'style' => 'digg',
				'total_items' => isset($return->totalcount) ? $return->totalcount : 0 // number of feed items
				));

		
	  $Feedlist = //$db->query($sql." Limit ".$numItems_per_page*($page_no - 1) ." , ".$numItems_per_page);
		// Get RSS News Feeds
		$this->template->content->feeds = $Feedlist;
		$this->template->content->current_page = $page_no;
					
			  // Get Summary
        // XXX: Might need to replace magic no. 8 with a constant
        $this->template->content->feedcounts = isset($return->totalcount) ? $return->totalcount : 0;
        
        $feed_summary_sql = " SELECT f.feed_name as feed_name ,f.feed_url as feed_url ,count(fi.id) as total 
															FROM `feed` f ,feed_item fi 
															WHERE fi.feed_id = f.id AND f.category_id NOT IN (1,11) AND submited_to_ushahidi = 0 GROUP BY f.feed_name 
															UNION 
															SELECT f.feed_name as feed_name ,concat('http://twitter.com/statuses/user_timeline/',f.feed_url,'.rss') as feed_url,count(fi.id) as total 
															FROM `feed` f ,feed_item fi 
															WHERE fi.feed_id = f.id AND f.category_id IN (1) AND  submited_to_ushahidi = 0  GROUP BY f.feed_name 
															UNION 
															SELECT  twitter_hashtags as feed_name, concat('http://twitter.com/search?q=', REPLACE(replace(twitter_hashtags,'#',''),',',' ' )) as 
															feed_url ,count(m.id) as total
															FROM settings s , message m WHERE m.submited_to_ushahidi = 0  Group BY feed_name ";
															
    $db = new Database(); 	 	
		$this->template->content->feedsummary = $db->query($feed_summary_sql);
		
		$AnalyicQuery = " SELECT 'Submitted' as title,
										(select count(*) FROM feed_item WHERE  submited_to_ushahidi = 1)+
										(select count(*) FROM message WHERE  submited_to_ushahidi = 1) as count,
										(select count(*) FROM feed_item )+(select count(*) FROM message ) as total
										UNION
										SELECT 'Sources Trusted' as title,
										(select count(*) FROM feed WHERE  weight > 99)+
										(select count(*) FROM reporter WHERE  weight > 99) as count,
										(select count(*) FROM feed )+(select count(*) FROM reporter ) as total
										UNION
										SELECT 'tags added' as title,
										(select count(*) FROM tags WHERE  tablename IN ('feed_item','message')) as count,
										(select count(*) FROM feed )+(select count(*) FROM reporter ) as total
										UNION
										SELECT 'tags approved' as title,
										(select count(*) FROM tags WHERE  tablename  IN ('feed_item','message') AND correct_yn = 1) as count,
										(select count(*) FROM feed )+(select count(*) FROM reporter ) as total
										 ";
		
		$this->template->content->analyticSummary = $db->query($AnalyicQuery);
		
		//	echo	$AnalyicQuery ;		
		//	exit(0);
		
		$this->template->content->pagination = $pagination;
		$this->template->content->selected_category = $category_id;
		$feedjs = new View('feed_functions_js');
		
		// Pack the javascript using the javascriptpacker helper		
		$this->template->header->js2 = $feedjs;
		
		//feed item content.
		$feed_item_template  = new View('feed_item');
		$this->template->content->feed_item_list = $feed_item_template;
		$this->template->content->feed_item_list->feeds = $Feedlist; 

	}
	
	/*
	* Ushahidi Stats HTML/JavaScript
    * @return mixed  Return ushahidi stats HTML code.
	*/
/** #########  Marked for deletion from here to the end of the file. pending testing. ##########	
*/ 
	private function _ushahidi_stats( )
	{	
		// Make sure cURL is installed
		if (!function_exists('curl_exec')) {
			throw new Kohana_Exception('footer.cURL_not_installed');
			return false;
		}
		/** #########  Apala required changes ########## DB Call below. ::factory('settings')*/
		$settings = ORM::factory('settings', 1);
		$stat_id = $settings->stat_id;
		
		if($stat_id == 0) return ''; 
		$url = 'http://tracker.ushahidi.com/px.php?task=tc&siteid='.$stat_id;
		
		$curl_handle = curl_init();
		curl_setopt($curl_handle,CURLOPT_URL,$url);
		curl_setopt($curl_handle,CURLOPT_CONNECTTIMEOUT,15); // Timeout set to 15 seconds. This is somewhat arbitrary and can be changed.
		curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER,1); // Set cURL to store data in variable instead of print
		$buffer = curl_exec($curl_handle);
		curl_close($curl_handle);
		
		try {
			$tag = @simplexml_load_string($buffer); // This works because the tracking code is only wrapped in one tag
		} catch (Exception $e) {
			// In case the xml was malformed for whatever reason, we will just guess what the tag should be here
			$tag = '<!-- Piwik -->
					<script type="text/javascript">
					var pkBaseURL = (("https:" == document.location.protocol) ? "https://tracker.ushahidi.com/piwik/" : "http://tracker.ushahidi.com/piwik/");
					document.write(unescape("%3Cscript src=\'" + pkBaseURL + "piwik.js\' type=\'text/javascript\'%3E%3C/script%3E"));
					</script><script type="text/javascript">
					try {
					  var piwikTracker = Piwik.getTracker(pkBaseURL + "piwik.php", '.$stat_id.');
					  piwikTracker.trackPageView();
					  piwikTracker.enableLinkTracking();
					} catch( err ) {}
					</script><noscript><p><img src="http://tracker.ushahidi.com/piwik/piwik.php?idsite='.$stat_id.'" style="border:0" alt=""/></p></noscript>
					<!-- End Piwik Tag -->
					';
		}
		
		return $tag;

	}
	
	
	/*
	* Google Analytics
	* @param text mixed  Input google analytics web property ID.
    * @return mixed  Return google analytics HTML code.
	*/
	private function _google_analytics($google_analytics = false)
	{
		$html = "";
		if (!empty($google_analytics)) {
			$html = "<script type=\"text/javascript\">
				var gaJsHost = ((\"https:\" == document.location.protocol) ? \"https://ssl.\" : \"http://www.\");
				document.write(unescape(\"%3Cscript src='\" + gaJsHost + \"google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E\"));
				</script>
				<script type=\"text/javascript\">
				var pageTracker = _gat._getTracker(\"" . $google_analytics . "\");
				pageTracker._trackPageview();
				</script>";
		}
		return $html;
	}

        /**
         * Escape string
         */
        private function _escape_string($str) {
            if( $str != "" ){
                $str = str_replace(array('\''),array('\\\''),$str);
                $str = "'".$str."'";
            }else {
                return "";
            }
            return $str;
        }
	
} // End Main
