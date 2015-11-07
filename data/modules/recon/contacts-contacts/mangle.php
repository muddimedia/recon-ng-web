<?php
ini_set('max_execution_time',500);
//Module details
$module_name_here = "Contact Name Mangler";
$module_path_here = "recon/contacts-contacts/mangle";
$allowed_actions = ["options", "info"];

//Run module
if(isset($_POST['module_option_maxlength']) && strlen($_POST['module_option_maxlength'])>0 && isset($_POST['module_option_overwrite']) && strlen($_POST['module_option_overwrite'])>0 && isset($_POST['module_option_pattern']) && strlen($_POST['module_option_pattern'])>0 && isset($_POST['module_option_source']) && strlen($_POST['module_option_source'])>0 && isset($_POST['module_option_substitute']) && strlen($_POST['module_option_substitute'])>0)
{
    //Configuration & Functions
    require_once("../../../../includes/config.php");
    require_once("../../../../includes/functions.php");
    $module_maxlength = urldecode($_POST['module_option_maxlength']);
    $module_overwrite = urldecode($_POST['module_option_overwrite']);
    $module_pattern = urldecode($_POST['module_option_pattern']);
    $module_source = urldecode($_POST['module_option_source']);
    $module_substitute = urldecode($_POST['module_option_substitute']);
    $sid = manager_recon("init", NULL);
    $use_module = manager_recon("use", array($module_path_here, $sid));
    $set_module_maxlength = manager_recon("set", array('MAX-LENGTH', $module_maxlength, $sid));
    $set_module_overwrite = manager_recon("set", array('OVERWRITE', $module_overwrite, $sid));
    $set_module_pattern = manager_recon("set", array('PATTERN', $module_pattern, $sid));
    $set_module_source = manager_recon("set", array('SOURCE', $module_source, $sid));
    $set_module_substitute = manager_recon("set", array('SUBSTITUTE', $module_substitute, $sid));
    if(isset($_POST['module_option_domain']) && strlen($_POST['module_option_domain'])>0)
    {
        $set_module_domain = manager_recon("set", array('DOMAIN', urldecode($_POST['module_option_domain']), $sid));
    }
    $run_module = manager_recon("run", $sid);
    echo "<pre>";
    print_r($run_module);
    echo "</pre>";
    return;
}

//Show data based on action
if(strlen($action)>0 && in_array($action, $allowed_actions))
{
    if($action=="options")
    {
?>
        Module Name: <b><?php echo $module_name_here; ?></b><br>
        Module path: <b><?php echo $module_path_here; ?></b><br>
        <br><br>
        <form role='form' id='runmodule' action='' method='post'>
        DOMAIN: <input type='text' name='module_option_domain' value="" class='form-control'><br>
        MAX-LENGTH: <input type='text' name='module_option_maxlength' value="30" class='form-control'><br>
        OVERWRITE: <input type='text' name='module_option_overwrite' value="False" class='form-control'><br>
        PATTERN: <input type='text' name='module_option_pattern' value="<fn>.<ln>" class='form-control'><br>
        SOURCE: <input type='text' name='module_option_source' value="default" class='form-control'><br>
        SUBSTITUTE: <input type='text' name='module_option_substitute' value="-" class='form-control'><br>
        <button type='submit' class='btn btn-success'>Run</button><br>
        </form>
<?php
    }
    elseif($action=="info")
    {
?>
        Name: <?php echo $module_name_here; ?><br>
        Path: <?php echo "modules/$module_path_here.py"; ?><br>
        Author: Tim Tomes (@LaNMaSteR53)<br>
        <br>
        Description:<br>Applies a mangle pattern to all of the contacts stored in the database, creating email addresses or usernames for each harvested contact. Updates the 'contacts' table with the results.<br>
        <br>
        Options:<br>
        <table class='table'>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Current Value</th>
                    <th>Required</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>DOMAIN</td>
                    <td></td>
                    <td>no</td>
                    <td>target email domain</td>
                </tr>
                <tr>
                    <td>MAX-LENGTH</td>
                    <td>30</td>
                    <td>yes</td>
                    <td>maximum length of email address prefix or username</td>
                </tr>
                <tr>
                    <td>OVERWRITE</td>
                    <td>False</td>
                    <td>yes</td>
                    <td>overwrite existing email addresses</td>
                </tr>
                <tr>
                    <td>PATTERN</td>
                    <td>False</td>
                    <td>&lt;fn&gt;.&lt;ln&gt;</td>
                    <td>pattern applied to mangle first and last name</td>
                </tr>
                <tr>
                    <td>SOURCE</td>
                    <td>default</td>
                    <td>yes</td>
                    <td>source of input (see 'show info' for details)</td>
                </tr>
                <tr>
                    <td>SUBSTITUTE</td>
                    <td>-</td>
                    <td>yes</td>
                    <td>character to substitute for invalid email address characters</td>
                </tr>
            </tbody>
        </table>
        <br>
        Source Options:<br>
        <table class='table table-condensed'>
            <tr>
                <td>default</td>
                <td>SELECT rowid, first_name, middle_name, last_name, email FROM contacts ORDER BY first_name</td>
            </tr>
            <tr>
                <td>&lt;string&gt;</td>
                <td>string representing a single input</td>
            </tr>
            <tr>
                <td>&lt;path&gt;</td>
                <td>path to a file containing a list of inputs</td>
            </tr>
            <tr>
                <td>query &lt;sql&gt;</td>
                <td>database query returning one column of inputs</td>
            </tr>
        </table>
        <br>
        Comments:<br>
            * Pattern options: <?php echo htmlentities("<fi>,<fn>,<mi>,<mn>,<li>,<ln>"); ?><br>
            * Example: <?php echo htmlentities("<fi>.<ln> => j.doe@domain.com"); ?><br>
            * Note: Omit the 'domain' option to create usernames<br>
        <script>
        $("#runmodule").submit(function(e){    
                e.preventDefault();  
                $( "#result" ).empty().append( "" );
                $( "#loading" ).empty().append( '<img src="img/loading64.gif" alt="Loading..." width="" height="">' );
                $.post("<?php echo "data/modules/$module_path_here.php"; ?>", $("#runmodule").serialize(),
                function(data){
                    $( "#loading" ).empty().append( "" );
                    $( "#result" ).empty().append( data );
                    var mydata = $("#result").html();
                });
        });
        </script>
<?php
    }
    else
    {
        echo "Invalid action";
    }
}
else
{
    echo "Invalid action";
}
?>
