<?php
$keepExisting 	= false;
$backup		 	= false;

if(count($argv) >= 2 )
{
	if($argv[1] == 'process' && count($argv) >= 3 )
	{
		process($argv[2]);
	}
	else if($argv[1] == 'process-all' )
	{
		$config = parse_ini('filecopier-config.ini',false,false);
		//print_r($config);
		//exit();
		foreach($config as $k=>$v)
		{
			process($k,  $v);
		}
	}
}
function process( $file,  $config = null  )
{
	global $argv,$keepExisting, $backup;
	
	// gettings extra params
	$keepExisting = getSetting( $config, $argv, 'keep_existing', '-ke', false, false);
	$recursive = getSetting( $config, $argv, 'recursive', '-r', false, false);
	$debug = getSetting( $config, $argv, 'debug', '-d', false, false);
	$backup = getSetting( $config, $argv, 'backup', '-b', false, false);
	
	$destFile = $debug ? 'test'.$file : $file;
	$sourceFile = 'data'.$file;
	
	if(!file_exists($sourceFile))
		die( "sourceFile does not exist ".$sourceFile);
	
	recurse_copy($sourceFile, $destFile);
}
function recurse_copy($src,$dst)
{
	if ( is_file($src ) ) {
		$p = pathinfo( $dst, PATHINFO_DIRNAME);
		create_dir($p );
		return copy_file($src, $dst);
	}
    $dir = opendir($src);
	create_dir($dst);
	
    while(false !== ( $file = readdir($dir)) ) {
        if (( $file != '.' ) && ( $file != '..' )) {
            if ( is_dir($src . '/' . $file) ) {
                recurse_copy($src . '/' . $file, $dst . '/' . $file);
            }
            else {
                copy_file($src . '/' . $file, $dst . '/' . $file);
			}
        }
    }
    closedir($dir);
	//exit();
} 
function copy_file( $src, $dst )
{
	global $keepExisting, $backup;
	
	if(file_exists($dst))
	{
		if($keepExisting)
		{
			echo "Skipping \t".$dst.' already exists'.PHP_EOL;
			return;
		}else if($backup) {
			$bkp = backup_file( $dst );
			echo "Backuping \t".$dst.' to '.$bkp.PHP_EOL;
			
		}else{
			echo "Overwriting \t".$dst.PHP_EOL;
		}
	}
	echo "Copying \t".$src.' to '.$dst.PHP_EOL;
	return copy($src, $dst);
}
function backup_file( $f )
{
	for($i = 1; $i < 20; $i++)
	{
		$dst = $f.'.bak.'.$i;
		if(!file_exists( $dst ) )
		{
			copy($f, $dst);
			return $dst;
		}
	}
}

function create_dir( $p )
{
	if(!file_exists($p)) {
		echo "Creating dir \t".$p.PHP_EOL;
		@mkdir($p, 0666, true);
	}
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