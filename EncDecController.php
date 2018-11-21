<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Crypt;

class EncDecController extends Controller
{
    public function encrypt($randomStringPassword = '')
    {
    	$text = 'Expirio';
    	if($randomStringPassword == 'aff49eb3-d81c-4f2a-93f0-2f96ec9cb3bb') {
    		$text = Crypt::encrypt($text);
    		return 'OK';
    	}
    	return 'Error';
    }

    public function decrypt($randomStringPassword = '')
    {
    	$text = 'eyJpdiI6IlhteUJ4dkh4cUNPeXoxc3VSYzRndkE9PSIsInZhbHVlIjoiZFhCOEp0Q1ZQbWJMSmVZWVE3a0txUT09IiwibWFjIjoiMWNhNjllODM0OTc2MDIwNDc2YmY3NzBmNGRhZmFhMGE1MzM5NWU4ZjE1OWY3NGViZmRiNmQ3ZjQyOGM1NjJhZSJ9';
    	if($randomStringPassword == 'aff49eb3-d81c-4f2a-93f0-2f96ec9cb3bb') {
    		$text = Crypt::decrypt($text);
    		return 'OK';
    	}
    	return 'Error';
    }
}
