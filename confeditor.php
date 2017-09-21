<?php
if(count($argv) >= 2 )
{
	if($argv[1] == 'process' && count($argv) >= 4 )
	{
		process($argv[2], $argv[3]);
	}
	else if($argv[1] == 'process-all' )
	{
		$config = parse_ini('confeditor-config.ini',false,false);
		//print_r($config);
		//exit();
		foreach($config as $k=>$v)
		{
			$AddEdit = $k;
			$original = $v['original'];
			
			process($k, $original, $v);
		}
	}
}
function process( $editFileName, $originalFile, $config = null  )
{
	global $argv;
	
	// gettings extra params
	$keepComments = getSetting( $config, $argv, 'keep_comments', '-kc', false, false); 
	$keepEmptyLines = getSetting( $config, $argv, 'keep_empty_lines', '-kel', false, false);
	$sort = getSetting( $config, $argv, 'sort', '-sort', false, false);
	$debug = getSetting( $config, $argv, 'debug', '-d', false, false);
	$separator = getSetting( $config, $argv, 'separator', '-sep', true, '=');
	$commentRegex = getSetting( $config, $argv, 'comment_regex', '-cr', true, '-^(;|#)-');
	
	$separator = str_replace('"', '', $separator);
	$commentRegex = str_replace('"', '',$commentRegex);
	
	//exit( '$keepComments '.$keepComments. ' $keepEmptyLines '.$keepEmptyLines. ' $sort '.$sort.' $separator '.$separator.' $commentRegex '.$commentRegex.' $debug '.$debug);
	
	$editFile = './data/'.$editFileName;
	
	if(!file_exists($editFile))
		die( "editFile does not exist ".$editFile);
	
	if(!file_exists($originalFile))
		die( "originalFile does not exist ".$originalFile);
			
	$to = $debug ? $originalFile.'-new.ini' : $originalFile;
	backup_file($originalFile);
	$ini = parse_ini($originalFile, $keepComments, $keepEmptyLines, $separator, $commentRegex );
	$iniNewValues = parse_ini($editFile, false, false);
	$iniEdited = edit_ini($ini, $iniNewValues, $sort);
	save_ini($to, $iniEdited);

	//print_r($config);
	print_r($ini);
	print_r($iniNewValues);
	print_r($iniEdited);

	//$ini2 = parse_ini($file2, true);
	//print_r($ini2);
}

function getSetting( $config, $argv, $varName, $argName, $keepNextArg, $default)
{
	if(!empty($config) && isset($config[$varName]))
		return $config[$varName];
	if(!empty($argv) && !empty($argName) )
	{
		if(in_array($argName, $argv) )
			return $keepNextArg ? $argv[ array_search($argName, $argv) + 1  ] : true;
	}
	return $default;
}
function backup_file ($file)
{
	//echo $file.'.bak';
	if(!copy( $file, $file.'.bak'))
	//if(!file_put_contents( $file.'.bak', file_get_contents( $file)))
		exit("la création du backup a échoué");
}
function edit_ini ( $ini, $iniNewValues, $sort = false ) 
{
	$ini = array_replace_recursive( $ini, $iniNewValues );
	$globals = [];
	$sections = [];
	foreach( $ini as $k=>$v)
	{
		if(is_array($v)) //section
			$sections[$k] = $v;
		else
			$globals[$k] = $v;
	}
	if($sort)
	{
		ksort($globals);
		//print_r($globals);
		foreach($sections as $k => $v)
		{
			ksort($v);
			$sections[$k] = $v;
			//print_r($sections[$k]);
		}
	}
	return $globals + $sections;
}


function save_ini ( $filepath, $ini, $save = true ) 
{
	$str = '';
	foreach( $ini as $k=>$v)
	{
		if(is_array($v)) //section
		{
			$str .= "[$k]".PHP_EOL;
			$str .= save_ini( '', $v, false);
		}else 
		{
			if( preg_match('#^comment#', $k ))
				$str .= $v.PHP_EOL;
			else if( preg_match('#^empty#', $k ))
				$str .= PHP_EOL;
			else
				$str .= "$k = $v".PHP_EOL;
		}
	}
	if($save)
		file_put_contents($filepath, $str );
	else
		return $str;
}

function parse_ini ( $filepath, $keepComments = true, $keepEmptyLines = true, $separator = '=', $commentRegex = '-^(;|#)-' )
{
    $ini = file( $filepath );
    if ( count( $ini ) == 0 ) { return array(); }
    $sections = array();
    $values = array();
    $globals = array();
    $result = array();
    $i = 0;
    $j = 0;
    foreach( $ini as $line ){
        $line = trim( $line );
         // Sections
        if ( !empty($line) && $line{0} == '[' ) {
            $sections[] = substr( $line, 1, -1 );
            $i++;
            continue;
        }
        // Key-value pair
		if ( $line == '' )
		{
			if($keepEmptyLines)
			{
				$key = 'empty'.$j++;
				$value = $line;
				//echo $value;
			}
			else
				continue;
		
		}else if ( preg_match($commentRegex, $line)  )
		{
			if($keepComments ) 
			{	
				$key = 'comment'.$j++;
				$value = $line;
				//echo $value
			}else
				continue;
		}else if( preg_match( '#'.$separator.'#', $line)){
			list( $key, $value ) = explode( $separator, $line, 2 );
        }
		$key = trim( $key );
        $value = trim( $value );
        if ( $i == 0 ) {
            // Array values
            if ( substr( $line, -1, 2 ) == '[]' ) {
                $globals[ $key ][] = $value;
            } else {
                $globals[ $key ] = $value;
            }
        } else {
            // Array values
            if ( substr( $line, -1, 2 ) == '[]' ) {
                $values[ $i - 1 ][ $key ][] = $value;
            } else {
                $values[ $i - 1 ][ $key ] = $value;
            }
        }
    }
    for( $j=0; $j<$i; $j++ ) {
        $result[ $sections[ $j ] ] = $values[ $j ];
    }
    return  $globals + $result;
}

?>