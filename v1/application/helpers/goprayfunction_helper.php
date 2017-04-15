<?php

if ( ! function_exists('getBrowser'))
{
	// Reference : http://php.net/manual/en/function.get-browser.php#101125
	function getBrowser($userAgentParam)
	{
	    $u_agent = $userAgentParam;
	    $bname = 'Unknown';
	    $platform = 'Unknown';
	    $version= "";

	    //First get the platform?
	    if (preg_match('/linux/i', $u_agent)) {
	        $platform = 'linux';
	    }
	    elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
	        $platform = 'mac';
	    }
	    elseif (preg_match('/windows|win32/i', $u_agent)) {
	        $platform = 'windows';
	    }
	   
	    // Next get the name of the useragent yes seperately and for good reason
	    if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent))
	    {
	        $bname = 'Internet Explorer';
	        $ub = "MSIE";
	    }
	    elseif(preg_match('/Firefox/i',$u_agent))
	    {
	        $bname = 'Mozilla Firefox';
	        $ub = "Firefox";
	    }
	    elseif(preg_match('/Chrome/i',$u_agent))
	    {
	        $bname = 'Google Chrome';
	        $ub = "Chrome";
	    }
	    elseif(preg_match('/Safari/i',$u_agent))
	    {
	        $bname = 'Apple Safari';
	        $ub = "Safari";
	    }
	    elseif(preg_match('/Opera/i',$u_agent))
	    {
	        $bname = 'Opera';
	        $ub = "Opera";
	    }
	    elseif(preg_match('/Netscape/i',$u_agent))
	    {
	        $bname = 'Netscape';
	        $ub = "Netscape";
	    }
	   
	    // finally get the correct version number
	    $known = array('Version', $ub, 'other');
	    $pattern = '#(?<browser>' . join('|', $known) .
	    ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
	    if (!preg_match_all($pattern, $u_agent, $matches)) {
	        // we have no matching number just continue
	    }
	   
	    // see how many we have
	    $i = count($matches['browser']);
	    if ($i != 1) {
	        //we will have two since we are not using 'other' argument yet
	        //see if version is before or after the name
	        if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
	            $version= $matches['version'][0];
	        }
	        else {
	            $version= $matches['version'][1];
	        }
	    }
	    else {
	        $version= $matches['version'][0];
	    }
	   
	    // check if we have a number
	    if ($version==null || $version=="") {$version="?";}
	   
	    return array(
	        'userAgent' => $u_agent,
	        'name'      => $bname,
	        'version'   => $version,
	        'platform'  => $platform,
	        'pattern'    => $pattern
	    );
	}
}

if ( ! function_exists('catatLog'))
{
	/*
	* @GET Parameter
	* $method = GET , POST , PUT , DELETE
	* $requestUri = mendapatkan link
	* $user_agent = mencatat user agent
	* $ip_address = mencacat ip address
	* $browser = mencatat browser dan didapatkan result melalui user agent
	* $platform = mencatat platform yang digunakan melalui user agent
	* $time = mendaptkan jam , menit dan detik saat melakukan fungsi ini.
	*/

	function catatLog($x = array())
	{
		$resultUa = getBrowser($x['user_agent']);

		$data = array( 
				'method' => @$x['method'],
				'requestUri' => @$x['requestUri'],
				'user_agent' => @$resultUa['userAgent'],
				'ip_address' => @$x['ip_address'],
				'browser' => @$resultUa['name'].' '.$resultUa['version'],
				'platform' => @$resultUa['platform'],
				'time' => date('H:i:s')
			);
		
		$file = FCPATH.'logs/'.date('d_m_Y').'/'.date('H').'.txt';
		
		$content = $data['method'];
		$content.= " , ".$data['requestUri'];
		$content.= " , ".$data['platform'];
		$content.= " , ".$data['user_agent'];
		$content.= " , ".$data['ip_address'];
		$content.= " , ".$data['browser'];
		$content.= " , ".$data['time'].PHP_EOL;

		// is directory created?
		if ( ! is_dir(FCPATH.'logs/'.date('d_m_Y')))
		{
			mkdir(FCPATH.'logs/'.date('d_m_Y'));
			@chmod ( FCPATH.'logs/'.date('d_m_Y') , 0754);
		}

		// is file created?
		if ( ! file_exists(FCPATH.'logs/'.date('d_m_Y').'/'.date('H').'.txt'))
		{	
			fopen(FCPATH.'logs/'.date('d_m_Y').'/'.date('H').'.txt' , 'w');
			@chmod ( FCPATH.'logs/'.date('d_m_Y').'/'.date('H').'.txt' , 0754);
		}

		// ok, put that content from variable data
		file_put_contents($file , $content , FILE_APPEND);
	}
}

if ( ! function_exists('trimLower'))
{
	function trimLower($string)
	{
		$string = trim($string);
		$string = strtolower($string);

		return $string;
	}
}