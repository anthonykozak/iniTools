# iniTools
ini file merger &amp; conf file copier

# ini Merger
Place ini files in data/ with only the lines you wanna add or edit.
Edit confeditor-config.ini to tell what are the destination files to edit for each sample.

### Command Lines :

```
php confeditor.php process_all [options]
```
This command will execute every entries in the confeditor-config.ini file


#### Config file options :
	
option | values | default | description
--- | --- | --- | ---
keep_comments | 1/0 | 0 | keep comment lines in the target file
keep_empty_lines | 1/0 | 0 | keep empty lines in target file
sort | 1/0 | 0 | sort ini keys in each section
debug | 1/0 | 0 | do not write in files but create a '-new' file instead
separator | string | '=' | separator for key & values
comment_regex | regex | '-^(;|\# )-' | what defines a comment

```
php confeditor.php process sample-file target-file [options]
```
This command will merge the target-file with the lines inside the data/sample-file

#### Cmd options :
	
option | equivalent | values | default
--- | --- | --- | ---
-kc | keep_comments | 1/0 | 0
-kel | keep_empty_lines | 1/0 | 0
-sort | sort | 1/0 | 0
-d | debug | 1/0 | 0
-sep | separator | string | '='
-cr | comment_regex | string | '-^(;|\# )-'








# File Copier
Place folder and files that you want to copy in data/
Edit filecopier-config.ini to configure which files and folder you want to copy, backup and overwrite

### Command Lines :

```
php filecopier.php process_all [options]
```
This command will execute every entries in the filecopier-config.ini file


#### Config file options :
	
option | values | default | description
--- | --- | --- | ---
keep_existing | 1/0 | 0 | keep file if already exists, do not overwrite it
backup | 1/0 | 0 | create a backup of the file if exists, then copy the new file
debug | 1/0 | 0 | write the copy  in a test/ folder

```
php confeditor.php process filepath [options]
```
This command will copy the file located in data/filepath to filepath

#### Cmd options :
	
option | equivalent | values | default
--- | --- | --- | ---
-ke | keep_existing | 1/0 | 0
-b | backup | 1/0 | 0
-d | debug | 1/0 | 0
