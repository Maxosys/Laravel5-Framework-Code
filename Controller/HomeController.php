<?php

class HomeController extends BaseController {

	/*
	|--------------------------------------------------------------------------
	| Default Home Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	|	Route::get('/', 'HomeController@showWelcome');
	|
	*/
	
		public $metatitle 		 = '';
		public $metaDes  		 = '';
		public $metakey    		 = '';
	
	public function __construct()
	{
		 $this->beforeFilter('auth', array('except' => array('index','show','register','home','login','fblogin','logout','profile','citieslist','institueslist','landing','forgotpassword','getbragloaction','savecoverpicpo','userinterst','serachforall')));		 
		
		 $this->Talentmodel	   =  new  Talents();  
	}
	
	
	public function index()
	{
		//if(Session::has('userId'))
		if(isset($_SESSION['userId']))
	   {
			//$accountType 	= Session::get('accountType');
			$accountType 	= $_SESSION['accountType'];
			
			if($accountType=='ga')
			{
				return Redirect::to('home');
			}
			else
			{
				return Redirect::to('home');
			}
	   }
	
		return View::make('frontend.landingpage');
	}
	
	public function privacy()
	{
		return View::make('frontend.privacy');
	}
	
	public function landing()
	{
		//return View::make('frontend.privacy');
		
		//if(Session::has('userId'))
		if(isset($_SESSION['userId']))
		{
			//$accountType 	= Session::get('accountType');
			$accountType 	= $_SESSION['accountType']; //Session::get('accountType');
			
			if($accountType=='ga')
					{
						return Redirect::to('home');
					}
					else
					{
						return Redirect::to('home');
					}
		}
		
		return View::make('frontend.newlandingpage');
	}
	
	
	public function home($username='')
	{
		if(isset($_SESSION['userId']))
		{
			$bragData['metatitle'] = 'BragOut | Connect with People of your Interests';
			
			$bragData['metaDes'] = 'Connect with people of same interests like you. BragOut is interest-based social media site where you can share your interests with others of similar interests.';
			
			$bragData['metakey'] = 'bragout ,sports enthusiast,fitness enthusiast,physical activity enthusiast,passion in sports,passion in fitness,athlete,find talent,marathon running,dive,yoga,mountain climbing';
			
			
		//if(!empty($_SESSION['userId']))
		//{   
		$bragModel 		=  new 	Brag();		
		$Talentmodel  	=  new  Talents();
		
		//$userId = Session::get('userId');   
		 $userId =$_SESSION['userId']; 
		
		if(Input::get('uid'))
		{
			$userId = Input::get('uid');		
		}		
		
		 $data['users_interst_relationtable'] =  $this->Talentmodel->getTalentIdsFromuserInrestTable($userId);
		
		//$accountType = Session::get('accountType');
		$accountType = $_SESSION['accountType'];
		
	
		if($accountType=='ga')
		{
			if(count($data['users_interst_relationtable'])<1)
			{
					//return Redirect::to('profile/index');
					//return Redirect::to('home');
			}
		}
		else
		{
			return Redirect::to('recruiterprofile/index');
		}
	
		if(empty($username))
		{
	    //$userName=Session::get('userName');
		 $userName=$_SESSION['userName'];
			
		}
		else
		{
			$userName = $username;
			//$userId   = Request::segment(3); 
		}	
			
		// Get all post Id according to User Id
		$dataInst = $bragModel->getPostsIdbyUserId($userId);	
		
		
		//print_r($dataInst);
		//exit;
		
        if(!empty($dataInst))
          {
		// Get all posts according Posts
		
			$bragData['bragData'] = $bragModel->getAllPostsByInterest($dataInst);
		
          }
		
		
  $bragData['users_interst_relationtable'] = $this->Talentmodel->getTalentIdsFromuserInrestTable($userId);
	
		$dataInst_specialpost = $bragModel->getSpecialPostsIdbyUserId($userId);	
	
		
		if(!empty($dataInst_specialpost))
          {
			  		
		 $bragData['SpecialpostData'] = $bragModel->getAllspecialPostsByInterest($dataInst_specialpost);	
		
		  }		  
	
		return View::make('frontend.home',$bragData);
	   }
		else
	    {
			return Redirect::to('home/login');
			
		}
	}
	
	public function profile($username='')
	{
	
		if(empty($username))
		{
			//$userName=Session::get('userName');
			$userName = $_SESSION['userName'];
		}
		else
		{
			$userName = $username;
		}
		
			//$accountType = Session::get('accountType');
			  $accountType = $_SESSION['accountType'];
			
		
			if($accountType=='ga')
			{
				return Redirect::to('profile/index');
			}
			else
			{
				return Redirect::to('recruiterprofile/index');
			}
	}
	
	
	public function fblogin()
	{
		$user= new User;
		
		
		$fbid  = $_POST['fbid'];
		$email = $_POST['email'];
		
		$checkFbUserAlreadyRegister = DB :: table('users')->where('email',$email)->get();
	
		if(!empty($checkFbUserAlreadyRegister)&&isset($checkFbUserAlreadyRegister[0]))
		{
			// Fb login true 
			$dbfbid = $checkFbUserAlreadyRegister[0]->fbid;
			if($dbfbid==$fbid)
			{
				$email = $_POST['email'];
				$loginPass = '12345678';
			}
			else
			{
				echo 2;
				exit;
			}
		}
		else
		{
				$dateOfBirth='';
			
				if(isset($_POST['birthday']))
				{
					$birthday =  $_POST['birthday'];
					$dob = explode('/',$birthday);
				
					$day   = $dob[1];
					$month = $dob[0];
					$year  = $dob[2];					
						
					$dateOfBirth = $day.'-'.$month.'-'.$year;				
				}
					
					$loginPass = '12345678';
					
					$codeCon = time().rand();
					
					$user->userName 	= $_POST['firstname'];  //$_POST['username'];
					$user->firstName 	= $_POST['firstname'];
					$user->lastName 	= $_POST['lastname'];
					$user->email 		= $email;	
					$password 			= Hash::make($loginPass);
					$user->password     = $password;
					$user->gender       = $_POST['gender'];
					$user->dateOfBirth  = $dateOfBirth;
					$user->userType 	= 'ga';
					$user->accountType  = 'ga';
					$user->codeCon  	= $codeCon;
					$user->fbid  		= $fbid;
					$user->active 		='1';
					$user->save();
					
					$userId = $user->userId;
					
				  $facebookImgPath =  'http://graph.facebook.com/'.$fbid.'/picture?type=large';		
				  $image = file_get_contents($facebookImgPath);						
				 
				  $file_name_34    = $userId."_34_thumb.jpg";
				  $file_thumb_157  = $userId."_157_thumb.jpg";
				  $file_thumb_orig = $userId."_orignal.jpg";
				  
				  $new_name_34 = "assets/images/profilepic/".$file_name_34."";
				  $new_name_thumbs_157 = "assets/images/profilepic/".$file_thumb_157."";
				  $new_name_thumbs_original = "assets/images/profilepic/".$file_thumb_orig."";
				
				file_put_contents($new_name_34, $image);
				file_put_contents($new_name_thumbs_157, $image);
				file_put_contents($new_name_thumbs_original, $image);
					
				$this->smart_resize_image($new_name_34 , 34 , 34 , false , $new_name_34 , false , false ,100 );
				$this->smart_resize_image($new_name_thumbs_157 , 157 , 157 , false , $new_name_thumbs_157 , false , false ,100 );
		}
			
			// fb login 
				
			if (Auth::attempt(array('email' =>$email, 'password' =>$loginPass,'active' => 1),true))
			{
						if(Auth::check())
						{
						// store information in session
							
					Session::put('userId', Auth::user()->userId);
					Session::put('prouserId', Auth::user()->userId);
					Session::put('userName', Auth::user()->userName);
					Session::put('firstName', Auth::user()->firstName);
					Session::put('lastName', Auth::user()->lastName);
					Session::put('userType', Auth::user()->userType);
					Session::put('accountType', Auth::user()->accountType);
					Session::put('dateOfBirth', Auth::user()->dateOfBirth);

					  $_SESSION['userId']           = Auth::user()->userId; 
					  $_SESSION['prouserId']        = Auth::user()->userId; 
					  $_SESSION['userName']         = Auth::user()->userName; 
					  $_SESSION['firstName']        = Auth::user()->firstName; 
					  $_SESSION['lastName']         = Auth::user()->lastName; 
					  $_SESSION['userType']         = Auth::user()->userType; 
					  $_SESSION['accountType']      = Auth::user()->accountType; 
					  $_SESSION['dateOfBirth']      = Auth::user()->dateOfBirth; 								
								
							// END store information in session
							
							// Get user Information form session
								
									$userName		= Session::get('userName');	
									$userId 		= Session::get('userId');			
							
									$accountType 	= Session::get('accountType');
									$prouserId 		= Session::get('prouserId');
								
							// end 
							
							if($accountType=='ga')
							{								
								return Redirect::to('home');
								exit;
							}
							else
							{
								return Redirect::to('home');
								exit;
							}															
						}
					}
					else
					{
						
						return View::make('frontend.login')->with('message','Email or Password is not Correct');						
					}			
	
	}
	
	function smart_resize_image($file,
                              $width              = 0, 
                              $height             = 0, 
                              $proportional       = false, 
                              $output             = 'file', 
                              $delete_original    = true, 
                              $use_linux_commands = false,
  							  $quality = 100
  		 ) {
			 
			
      
    if ( $height <= 0 && $width <= 0 ) return false;

    # Setting defaults and meta
    $info                         = getimagesize($file);
    $image                        = '';
    $final_width                  = 0;
    $final_height                 = 0;
    list($width_old, $height_old) = $info;
	$cropHeight = $cropWidth = 0;


    # Calculating proportionality
    if ($proportional) {
      if      ($width  == 0)  $factor = $height/$height_old;
      elseif  ($height == 0)  $factor = $width/$width_old;
      else                    $factor = min( $width / $width_old, $height / $height_old );

      $final_width  = round( $width_old * $factor );
      $final_height = round( $height_old * $factor );
    }
    else {
      $final_width = ( $width <= 0 ) ? $width_old : $width;
      $final_height = ( $height <= 0 ) ? $height_old : $height;
	  $widthX = $width_old / $width;
	  $heightX = $height_old / $height;
	  
	  $x = min($widthX, $heightX);
	  $cropWidth = ($width_old - $width * $x) / 2;
	  $cropHeight = ($height_old - $height * $x) / 2;
    }

	//print_r($info);
	//echo $info[2];
    # Loading image to memory according to type
    switch ( $info[2] ) {
      case IMAGETYPE_JPEG:  $image = imagecreatefromjpeg($file);  break;
      case IMAGETYPE_GIF:   $image = imagecreatefromgif($file);   break;
      case IMAGETYPE_PNG:   $image = imagecreatefrompng($file);   break;
      default: return false;
    }
    
    
    # This is the resizing/resampling/transparency-preserving magic
    $image_resized = imagecreatetruecolor( $final_width, $final_height );
    if ( ($info[2] == IMAGETYPE_GIF) || ($info[2] == IMAGETYPE_PNG) ) {
      $transparency = imagecolortransparent($image);
      $palletsize = imagecolorstotal($image);

      if ($transparency >= 0 && $transparency < $palletsize) {
        $transparent_color  = imagecolorsforindex($image, $transparency);
        $transparency       = imagecolorallocate($image_resized, $transparent_color['red'], $transparent_color['green'], $transparent_color['blue']);
        imagefill($image_resized, 0, 0, $transparency);
        imagecolortransparent($image_resized, $transparency);
      }
      elseif ($info[2] == IMAGETYPE_PNG) {
        imagealphablending($image_resized, false);
        $color = imagecolorallocatealpha($image_resized, 0, 0, 0, 127);
        imagefill($image_resized, 0, 0, $color);
        imagesavealpha($image_resized, true);
      }
    }
    imagecopyresampled($image_resized, $image, 0, 0, $cropWidth, $cropHeight, $final_width, $final_height, $width_old - 2 * $cropWidth, $height_old - 2 * $cropHeight);
	
	
    # Taking care of original, if needed
    if ( $delete_original ) {
      if ( $use_linux_commands ) exec('rm '.$file);
      else @unlink($file);
    }

    # Preparing a method of providing result
    switch ( strtolower($output) ) {
      case 'browser':
        $mime = image_type_to_mime_type($info[2]);
        header("Content-type: $mime");
        $output = NULL;
      break;
      case 'file':
        $output = $file;
      break;
      case 'return':
        return $image_resized;
      break;
      default:
      break;
    }
    
    # Writing image according to type to the output destination and image quality
    switch ( $info[2] ) {
      case IMAGETYPE_GIF:   imagegif($image_resized, $output);    break;
      case IMAGETYPE_JPEG:  imagejpeg($image_resized, $output, $quality);   break;
      case IMAGETYPE_PNG:
        $quality = 9 - (int)((0.9*$quality)/10.0);
        imagepng($image_resized, $output, $quality);
        break;
      default: return false;
    }

    return true;
  }
	public function login()
	{
		$msg = '';
	
		
		if(Request::isMethod('get'))
		{
			$confrimationcode = Input::get('c');
			$redirect 		  = Input::get('redirect');
			if(!empty($confrimationcode))
			{
			$res = DB::table('users')->where('codeCon',$confrimationcode)->update(array('active'=>1));
				if($res==1)
				{			
					$msg = 'You have verified successfully !';
				}
					else
				{
					$msg = 'You have already verified or wrong code !';
				}
			}
		}
		
		//if(Session::has('userId'))
		if(isset($_SESSION['userId']))
	  {
			//$accountType 	= Session::get('accountType');
			$accountType 	= $_SESSION['accountType'];
			
			
			if($accountType=='ga')
							{
								return Redirect::to('home');
							}
							else
							{
								return Redirect::to('home');
							}		
	  }
	  else
	  {
		if(Request::isMethod('post'))
		{
			$user= new User;
			$rules=array(
			'email'=>array('required'),
			'password'=>array('required'),
			);
			
			$validation= Validator::make(Input::all(), $rules);
			if($validation->fails())
			{
				return View::make('frontend.login')->withErrors($validation);
			}
			else
			{
				$email	  = Input::get('email');
				$password = Input::get('password');						
				
			
				    if (Auth::attempt(array('email' =>$email, 'password' =>$password,'active' => 1)))
					{
						if(Auth::check())
						{
							// store information in session
							
							  $_SESSION['userId']           = Auth::user()->userId; 
							  $_SESSION['prouserId']        = Auth::user()->userId; 
							  $_SESSION['userName']         = Auth::user()->userName; 
							  $_SESSION['firstName']        = Auth::user()->firstName; 
							  $_SESSION['lastName']         = Auth::user()->lastName; 
							  $_SESSION['userType']         = Auth::user()->userType; 
							  $_SESSION['accountType']      = Auth::user()->accountType; 
							  $_SESSION['dateOfBirth']      = Auth::user()->dateOfBirth; 

					        Session::put('userId', Auth::user()->userId);
							Session::put('prouserId', Auth::user()->userId);
							Session::put('userName', Auth::user()->userName);
							Session::put('firstName', Auth::user()->firstName);
							Session::put('lastName', Auth::user()->lastName);
							Session::put('userType', Auth::user()->userType);
							Session::put('accountType', Auth::user()->accountType);
							Session::put('dateOfBirth', Auth::user()->dateOfBirth);
							//session_start();
							  
							
							// END store information in session
							
							// Get user Information form session
								
								$_SESSION['userName']     = Session::get('userName'); 
						        $_SESSION['userId']       = Session::get('userId');		
							    $_SESSION['accountType']  = Session::get('accountType');
						        $_SESSION['prouserId']     = Session::get('prouserId');
								
								$userName		= Session::get('userName');
								$userId 		= Session::get('userId');			
								$accountType 	= Session::get('accountType');
								$prouserId 		= Session::get('prouserId');
								
							// end 
							
							//return View::make('frontend.home')->with('message', 'Successfully Login');
							
							if($accountType=='ga')
							{ 
								return Redirect::to('home');
							}
							else
							{
								return Redirect::to('home');
							}							
						}
					}
					else
					{				
			// check condition for Confirm or not
			
					$selectLike = DB :: table('users')->where('email',$email)->first();					
					if(isset($selectLike->active))
					{
					if($selectLike->active==0)
					{
					/*$msg = 'Confirm Email Address Please! <br/>
					Please confirm your email address by clicking on the link in the email we sent you when you signed up. This will help us know we’re sending your account information to the right place and person. Also, please look in your spam and/or junk folder if you do not find the email in your inbox. As a new site, it is possible your emails from BragOut are sent to your Junk or Spam folder.';*/
					$msg = 'Oops, Something Wrong?<br/> Confirm Email Address Please! <br/>
					Please confirm your email';

					return View::make('frontend.login')->with('message',$msg);
					}
					}
		// end 					
						
						return View::make('frontend.login')->with('message','Email or Password is not Correct');
					}			
		     }			
		}
		}
		return View::make('frontend.login')->with('message',$msg);
	}
	
	public function register()
	{
		
		//if(Session::has('userId'))
		if(isset($_SESSION['userId']))
	    {
			//$accountType 	= Session::get('accountType');
			
				$accountType 	= $_SESSION['accountType']; //Session::get('accountType');
			
				if($accountType=='ga')
							{
								return Redirect::to('home');
							}
							else
							{
								return Redirect::to('home');
							}		
	    }	
		
		if(Request::isMethod('post'))
		{		
			$requestArr = array("firstName"=>$_POST['firstName'],"lastName"=>$_POST['lastName'],"email"=>$_POST['email'],"gender"=>$_POST['gender'],"day"=>$_POST['day'],"month"=>$_POST['month'],"year"=>$_POST['year']);
			
			
			$user = new User;
			$rules=array(						
			'email'=>'required|email|unique:users',
			'password'=>'required|min:6',
			'cpassword'=>'required|same:password'
			);
			
			$validation = Validator::make(Input::all(), $rules);
			
			if ($validation->fails())
			{
				return View::make('frontend.newlandingpage')->withErrors($validation)->with('requestArr',$requestArr);
			}
			else
			{
					$day   = $_POST['day'];
					$month = $_POST['month'];
					$year  = $_POST['year'];
					
					$dateOfBirth = $day.'-'.$month.'-'.$year;
					
					$codeCon = time().rand();
					
					
					$user->userName 	= $_POST['firstName']; //$_POST['username'];
					$user->firstName 	= $_POST['firstName'];
					$user->lastName 	= $_POST['lastName'];
					$user->email 		= $_POST['email'];
					$password 			= Hash::make($_POST['password']);
					$user->password     = $password;
					$user->gender       = $_POST['gender'];
					$user->dateOfBirth  = $dateOfBirth;
					$user->userType 	= $_POST['accountType'];
					$user->accountType  = $_POST['accountType'];
					$user->codeCon  	= $codeCon;
					$user->active 		='0';
					$user->save();
		
		//$_POST['email'] = 'ankit.sharma@nanowebtech.com';
		//$_POST['email'] = 'nanowebtech16@gmail.com';
		
		$confirmationurl = URL::to('/').'/home/login?redirect=y&c='.$codeCon;		
	    $emailData1 = array('emaildata'=>$_POST['email'],'username'=>$_POST['firstName'],'confirmationurl'=>$confirmationurl);
		
		$thankyou = array('emaildata'=>$_POST['email'],'username'=>$_POST['firstName'],'confirmationurl'=>$confirmationurl);		
	
		
		try
		{
			Mail::send('emails.confirmation', $emailData1, function($message)
			{
						$message->to($_POST['email'], $_POST['username'])->subject('Bragout : Email Confirmation');
			});
					
			//$msg = 'You have successfully sent confirmation mail on your E-mail id .';
			$msg = 'Congratulations! You are currently the newest and coolest Brag-ster. Your confirmation email has been sent. If you do not see an email from us then please check your Spam folder. Click the link to confirm this email is yours.';
			
			
			Mail::send('emails.thankyou', $thankyou, function($messaget)
			{
				$messaget->to($_POST['email'], $_POST['username'])->subject('BragOut.com: Thank YOu');
			});			
		}
		catch(Exception $e)
		{
				$msg = 'Confirmation Mail not sending on your E-Mail id. Please contact to Admin';			
				$msg .=  $e->getMessage();			
		}	
		
			// Direct login
			
			
			
	if(Auth::attempt(array('email' =>$_POST['email'], 'password' =>$_POST['password']),true))
		{
			if(Auth::check())
			{
				// store information in session
							
								Session::put('userId', Auth::user()->userId);
								Session::put('prouserId', Auth::user()->userId);
								Session::put('userName', Auth::user()->userName);
								Session::put('firstName', Auth::user()->firstName);
								Session::put('lastName', Auth::user()->lastName);
								Session::put('userType', Auth::user()->userType);
								Session::put('accountType', Auth::user()->accountType);
								Session::put('dateOfBirth', Auth::user()->dateOfBirth);
									
									  $_SESSION['userId']           = Auth::user()->userId; 
							  $_SESSION['prouserId']        = Auth::user()->userId; 
							  $_SESSION['userName']         = Auth::user()->userName; 
							  $_SESSION['firstName']        = Auth::user()->firstName; 
							  $_SESSION['lastName']         = Auth::user()->lastName; 
							  $_SESSION['userType']         = Auth::user()->userType; 
							  $_SESSION['accountType']      = Auth::user()->accountType; 
							  $_SESSION['dateOfBirth']      = Auth::user()->dateOfBirth; 
								
							// END store information in session
							
							// Get user Information form session
								
									$userName		= Session::get('userName');	
									$userId 		= Session::get('userId');			
							
									$accountType 	= Session::get('accountType');
									$prouserId 		= Session::get('prouserId');
								
							// end 
							
				if($accountType=='ga')
				{								
					return Redirect::to('home');
					exit;
				}
				else
				{
					return Redirect::to('home');
					exit;
				}															
			}
		}
		else
		{			
			return View::make('frontend.login')->with('message','Email or Password is not Correct');
		}		
			
			// end direct login
		
					
	//exit;

	return View::make('frontend.newlandingpage')->with('message',$msg)->with('requestArr',$requestArr);		
								   
			}		
		}
		
		/*if (Session::has('id'))
		{   			
			$user = DB::table('users')->where('user_id',Session::has('id'))->first();
			return View::make('register')->with('userdata',$user);
		}*/
		
		return View::make('frontend.newlandingpage');
	}	

	public function contact()
	{
		return View::make('contact');
	}
	
	public function logout()
	{
		//Auth::logout();
	//	Session::flush();	
       session_destroy();	
		return Redirect::to('/');
	}
	
	public function citieslist()
	{
		$q	=	$_POST['searchword'];
		
		$conObj = new Cityconstlist();		
		$conrecord = $conObj->findCityByChar($q);
		
		$data['q'] = $q;
		$data['conrecord'] = $conrecord;
		
		return View::make('ajax.citieslist',$data);
	}
	
	public function getbragloaction()
	{
		$q	=	$_POST['searchword'];
		
		$conObj = new Cityconstlist();		
		$conrecord = $conObj->findCityByChar($q);
		
		$data['q'] = $q;
		$data['conrecord'] = $conrecord;
		
		return View::make('ajax.getbragloaction',$data);
	}
	
	public function institueslist()
	{
		$q	=	$_POST['searchword'];
		
		$InstitutesOBJ = new Institutes();		
		$conrecord = $InstitutesOBJ->findInstitutesByChar($q);
		
		$data['q'] = $q;
		$data['conrecord'] = $conrecord;
		
		return View::make('ajax.intlist',$data);
	}
	
	//================================= function using to search cities and talents  in header search start==========================================//
	public function userinterst()
	{
			
		//$userId = Session::get('userId');
		$q	=	$_POST['searchword'];
		
				
		$talentrecord =  $this->Talentmodel->gettalentBytalentcategorytosearch($q);
		//echo'<pre>'; print_r($talentrecord); die;
		$data['q'] = $q;
		$data['talentrecord'] = $talentrecord;
		
		return View::make('ajax.usersearch',$data);
	}
	
	
	public function citiessearchlist()
	{
		$q	=	$_POST['searchword'];
		
		$conObj = new Cityconstlist();		
		$conrecord = $conObj->findCityByChar($q);
		
		$data['q'] = $q;
		$data['conrecord'] = $conrecord;
		
		return View::make('ajax.usersearchcities',$data);
	}
	 
	
	//-----------------------------forgotenpassword case--------------------------------------//
	public function forgotpassword()
	{
	  $user= new User;
	 
	    if(Request::isMethod('post'))
		{
		
		 $email =$_POST['email'];
		 $emailrecord =$user->forgotpassword($email);
		 if($emailrecord == true)
		  {
	        $length = '7';
			$characters = 'ABCDEFGHIJKLMNPQRSTUXYVWZ123456789';
			$charactersLength = strlen($characters);
			$randomString = '';
			for ($i = 0; $i < $length; $i++) 
			{
				$randomString .= $characters[rand(0, $charactersLength - 1)];
		    }
              //echo'<pre>';print_r($randomString);die;
			  $codeCon = time().rand();
			  //echo'<pre>';print_r($codeCon);die;
			  $password =$randomString;
			  $subject="ReSet- Password Request"; 
              $from ="mail.nanowebtech.com"; 
			  $to =$email;
			  $content= $password;
			  
			  $confirmationurl = URL::to('/').'/home/login';
			  
			  $password1 = array('password'=>$content,'confirmationurl'=>$confirmationurl,'email'=>$email);
			  
			
			  $passsend =Mail::send('emails.reset', $password1 , function ($message) 
			  {
					$message->from('mail@nanowebtech.com', 'bragout.com');
                    
					$message->to($_POST['email']);
			  });	
             if(!empty($passsend)) 
		     {
		        $data = array(
				'password'=>Hash::make($password),
				);
				$updatepassword =DB::table('users')->where('email',$email)->update($data);
				if($updatepassword ==1)
				{
				   Session::flash('message', 'Your Password is Updated in Data Field And have successfully sent New Password on your E-mail id.');
				}
				else
				{
				   Session::flash('message','Your Password is NOt Updated in Data Field Try Again.');
				   
				}
		     }		   
            else
			{
			    Session::flash('message1','New Password not sending on your E-Mail id. Please contact to Admin.');
			}
		   }
		else
		{
		   Session::flash('emailcong','Your email Do not exits Please Check Your email Once Agian.');
        }		
	}
		return View::make('frontend/forgotpassword');
	}
	
 public function reset()
 {
  return View::make('emails/reset');
 } 
 /*--------------------------------forgotpassword case End--------------------------*/
 public function savecoverpicpo()
 {	
	$loggedin_user_id = '';
	
	if(isset($_REQUEST['loggedin_user_id']))
	{
		$loggedin_user_id = $_REQUEST['loggedin_user_id'];
		$backgroundPositionX = $_REQUEST['backgroundPositionX'];
		$backgroundPositionY = $_REQUEST['backgroundPositionY'];
		$data = array(
					'coverPicXPos'=>$backgroundPositionX,
					'coverPicYPos'=>$backgroundPositionY				
					);
			return $updatepassword =DB::table('users')->where('userId',$loggedin_user_id)->update($data);
	
	}			
	
	
 }
 
//================================= End ==========================================//

public function getnotification()
{
	 //$userId = Session::get('userId'); 
	// $userId = 9; 
	
	//$data['getcrntgroupdata']= DB::table('group_members')->where('userId',$userId)->where('status',0)->get();
	 //$data['count']=count($data['getcrntgroupdata']);
	  //echo'<pre>'; print_r($getcrntgroupdata); die;
	 // return $getcrntgroupdata;
	return View::make('ajax.usersnotification');
	
	
}
//========================search for all===============================//

public function serachforall()
	{
		$keyword= $_POST['inputsearch'];
		if(!empty($keyword))
		{
		 /*$data['searchresult'] =  DB::select(DB::raw("(SELECT userId, userName FROM users WHERE userName LIKE '%".$keyword ."%') UNION (SELECT groupId as groupId ,groupName as groupName FROM groups WHERE groupName LIKE '%".$keyword ."%')"));
		 $data['keyword']=$keyword;
		//echo'<pre>'; print_r($data['searchresult']); die;*/
		
		$data['searchresult'] = DB::select(DB::raw("(SELECT * FROM users WHERE userName LIKE '%".$keyword ."%')"));
		$data['keyword']=$keyword;
		
	      return View::make('ajax.searchforall',$data);
			
		}
		
	}

}