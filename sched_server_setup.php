<?
require_once("docutil.php");
page_head("Setting up a scheduling server");
echo "
<h2>Setting up a scheduling server</h2>

<p>
The BOINC scheduling server runs as a CGI or fast CGI
program under Apache or similar web server.
The host must have access to the project's BOINC database.

<p>
The scheduling server uses an auxiliary program called 'feeder';
the two programs communicate through a shared-memory segment.
<p>
Both programs read a configuration file <b>config.xml</b>
with the following form:
<pre>
&lt;config>
    &lt;db_name>david_test&lt;/db_name>
    &lt;db_passwd>&lt;/db_passwd>
    &lt;shmem_key>0xbeefcafe&lt;/shmem_key>
    &lt;key_dir>/home/david/boinc_keys&lt;/key_dir>
    &lt;upload_dir>/home/david/boinc_projects/test/upload&lt;/upload_dir>
    &lt;user_name>david&lt;/user_name>
&lt;/config>
</pre>
The elements are as follows:
<p>
<table border=1 cellpadding=10>
<tr><td>db_name</td><td>The name of the BOINC database</td></tr>
<tr><td>db_password</td><td>The password of the BOINC database</td></tr>
<tr><td>shmem_key</td><td>
The identifier of the shared-memory segment;
it is an arbitrary 32-bit quantity, but must be unique on the host.
</td></tr>
<tr><td>key_dir</td><td>
The directory containing the file upload authentication private key.
</td></tr>
<tr><td>upload_dir</td><td>
The directory where uploaded files are stored
(this is used by the <a href=data_server_setup.php>data server</a>).
</td></tr>
<tr><td>user_name</td><td>
This name is prepended to web log error messages
to distinguish between multiple servers on a single host.
</td></tr>
</table>
<p>
You must modify your Apache config file
to allow execution of the scheduling server.
For example:

<pre>
ScriptAlias /boinc-cgi/ \"/users/barry/cgi/\"

&lt;Directory \"/users/barry/cgi/\">
    AllowOverride None
    Options None
    Order allow,deny
    Allow from all
&lt;/Directory>
</pre>
";
page_tail();
?>
