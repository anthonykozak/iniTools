# iniTools
ini file merger &amp; conf file copier

# ini Merger
Place ini files in data/ with only the lines you wanna add or edit.
Edit confeditor-config.ini to tell what are the destination files to edit for each sample.

### Command Lines :

`confeditor.php process_all [options]`
This command will execute every entries in the confeditor-config.ini file


#### Config file options :
	
option | values | default | description
keep_comments | 1/0 | default:0 | keep comment lines in the target file

	keep_empty_lines 1/0 default:0
	sort default:0
	debug default:0
	separator default:'='
	comment_regex default:'-^(;|#)-'
	
`confeditor.php process sample-file target-file [options]`
This command will merge the target-file with the lines inside the data/sample-file

--Cmd options :
	-kc : keep_comments 1/0 default:0
	-kel : keep_empty_lines 1/0 default:0
	-sort : sort default:0
	-d : debug default:0
	-sep : separator default:'='
	-cr : comment_regex default:'-^(;|#)-'