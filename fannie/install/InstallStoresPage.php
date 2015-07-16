<?php
/*******************************************************************************

    Copyright 2011 Whole Foods Co-op

    This file is part of CORE-POS.

    CORE-POS is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    CORE-POS is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    in the file license.txt along with IT CORE; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

*********************************************************************************/

//ini_set('display_errors','1');
include('../config.php'); 
include('util.php');
include('db.php');
include_once('../classlib2.0/FannieAPI.php');

/**
    @class InstallStoresPage
    Class for the Stores install and config options
*/
class InstallStoresPage extends \COREPOS\Fannie\API\InstallPage {

    protected $title = 'Fannie: Store Settings';
    protected $header = 'Fannie: Store Settings';

    public $description = "
    Class for the Stores install and config options page.
    ";
    public $themed = true;

    // This replaces the __construct() in the parent.
    public function __construct() {

        // To set authentication.
        FanniePage::__construct();

        // Link to a file of CSS by using a function.
        $this->add_css_file("../src/style.css");
        $this->add_css_file("../src/javascript/jquery-ui.css");
        $this->add_css_file("../src/css/install.css");

        // Link to a file of JS by using a function.
        $this->add_script("../src/javascript/jquery.js");
        $this->add_script("../src/javascript/jquery-ui.js");

    // __construct()
    }

    public function preprocess()
    {
        global $FANNIE_OP_DB, $FANNIE_STORE_MODE, $FANNIE_STORE_ID;
        $model = new StoresModel(FannieDB::get($FANNIE_OP_DB));
        $posted = false;

        // save info
        if (is_array(FormLib::get('storeID'))) {
            $ids = FormLib::get('storeID');
            $names = FormLib::get('storeName', array());
            $hosts = FormLib::get('storeHost', array());
            $drivers = FormLib::get('storeDriver', array());
            $users = FormLib::get('storeUser', array());
            $passwords = FormLib::get('storePass', array());
            $op_dbs = FormLib::get('storeOp', array());
            $trans_dbs = FormLib::get('storeTrans', array());
            $push = FormLib::get('storePush', array());
            $pull = FormLib::get('storePull', array());

            for($i=0; $i<count($ids); $i++) {
                $model->reset();
                $model->storeID($ids[$i]);
                $model->description( isset($names[$i]) ? $names[$i] : '' );
                $model->dbHost( isset($hosts[$i]) ? $hosts[$i] : '' );
                $model->dbDriver( isset($drivers[$i]) ? $drivers[$i] : '' );
                $model->dbUser( isset($users[$i]) ? $users[$i] : '' );
                $model->dbPassword( isset($passwords[$i]) ? $passwords[$i] : '' );
                $model->opDB( isset($op_dbs[$i]) ? $op_dbs[$i] : '' );
                $model->transDB( isset($trans_dbs[$i]) ? $trans_dbs[$i] : '' );
                $model->push( in_array($ids[$i], $push) ? 1 : 0 );
                $model->pull( in_array($ids[$i], $pull) ? 1 : 0 );
                $model->save();
            }

            $posted = true;
        }

        // delete any marked stores
        if (is_array(FormLib::get('storeDelete'))) {
            foreach(FormLib::get('storeDelete') as $id) {
                $model->reset();
                $model->storeID($id);
                $model->delete();
            }

            $posted = true;
        }

        if (FormLib::get('addButton') == 'Add Another Store') {
            $model->reset();
            $model->description('NEW STORE');
            $model->save();

            $posted = true;
        }

        // redirect to self so refreshing the page
        // doesn't repeat HTML POST
        if ($posted) {
            // capture POST of input field
            installTextField('FANNIE_STORE_ID', $FANNIE_STORE_ID, 1);
            installSelectField('FANNIE_STORE_MODE', $FANNIE_STORE_MODE, array('STORE'=>'Single Store', 'HQ'=>'HQ'),'STORE');
            header('Location: InstallStoresPage.php');
            return false;
        }

        return true;
    }

    /**
      Define any CSS needed
      @return a CSS string
    */
    function css_content(){
        return '
            tr.highlight td {
                background-color: #ffffcc;
            }
        ';
    //css_content()
    }

    public function body_content()
    {
        include(dirname(__FILE__) . '/../config.php');
        ob_start();

        echo showInstallTabs('Stores');
        ?>

<form action=InstallStoresPage.php method=post>
<h1 class="install">
    <?php 
    if (!$this->themed) {
        echo "<h1 class='install'>{$this->header}</h1>";
    }
    ?>
</h1>
<p class="ichunk">Revised 23Apr2014</p>
<?php
if (is_writable('../config.php')){
    echo "<div class=\"alert alert-success\"><i>config.php</i> is writeable</div>";
}
else {
    echo "<div class=\"alert alert-danger\"><b>Error</b>: config.php is not writeable</div>";
}
?>
<hr />
<h4 class="install">Stores</h4>
<p class="ichunk" style="margin:0.0em 0em 0.4em 0em;">
<?php
$model = new StoresModel(FannieDB::get($FANNIE_OP_DB));
$model->dbHost($FANNIE_SERVER);
$myself = $model->find();
if (count($myself) == 0) {
    echo '<i>No entry found for this store. Adding one automatically...</i><br />';
    $model->description('CURRENT STORE');
    $model->save();
} else if (count($myself) > 1) {
    echo '<i>Warning: more than one entry for store host: ' . $FANNIE_SERVER . '</i><br />';
} else {
    echo '<i>This store is #' . installTextField('FANNIE_STORE_ID', $FANNIE_STORE_ID, $myself[0]->storeID()) . '</i><br />';
}
$model->reset();
echo '<label>Mode</label>';
echo installSelectField('FANNIE_STORE_MODE', $FANNIE_STORE_MODE, array('STORE'=>'Single Store', 'HQ'=>'HQ'),'STORE');

$supportedTypes = array('none'=>'');
if (extension_loaded('pdo') && extension_loaded('pdo_mysql'))
    $supportedTypes['PDO_MYSQL'] = 'PDO MySQL';
if (extension_loaded('mysqli'))
    $supportedTypes['MYSQLI'] = 'MySQLi';
if (extension_loaded('mysql'))
    $supportedTypes['MYSQL'] = 'MySQL';
if (extension_loaded('mssql'))
    $supportedTypes['MSSQL'] = 'MSSQL';
?>
<table class="table">
<tr>
    <th>Store #</th><th>Description</th><th>DB Host</th>
    <th>Driver</th><th>Username</th><th>Password</th>
    <th>Operational DB</th>
    <th>Transaction DB</th>
    <th>Push</th>
    <th>Pull</th>
    <th>Delete Entry</th>
</tr>
<?php foreach($model->find('storeID') as $store) {
    printf('<tr %s>
            <td>%d<input type="hidden" name="storeID[]" value="%d" /></td>
            <td><input type="text" class="form-control" name="storeName[]" value="%s" /></td>
            <td><input type="text" class="form-control" name="storeHost[]" value="%s" /></td>',
            ($store->dbHost() == $FANNIE_SERVER ? 'class="info"' : ''),
            $store->storeID(), $store->storeID(),
            $store->description(),
            $store->dbHost()
    );
    echo '<td><select name="storeDriver[]" class="form-control">';
    foreach($supportedTypes as $key => $label) {
        printf('<option %s value="%s">%s</option>',
            ($store->dbDriver() == $key ? 'selected' : ''),
            $key, $label);
    }
    echo '</select></td>';
    printf('<td><input type="text" class="form-control" name="storeUser[]" value="%s" /></td>
            <td><input type="password" class="form-control" name="storePass[]" value="%s" /></td>
            <td><input type="text" class="form-control" name="storeOp[]" value="%s" /></td>
            <td><input type="text" class="form-control" name="storeTrans[]" value="%s" /></td>
            <td><input type="checkbox" name="storePush[]" value="%d" %s /></td>
            <td><input type="checkbox" name="storePull[]" value="%d" %s /></td>
            <td><input type="checkbox" name="storeDelete[]" value="%d" /></td>
            </tr>',
            $store->dbUser(),
            $store->dbPassword(),
            $store->opDB(),
            $store->transDB(),
            $store->storeID(), ($store->push() ? 'checked' : ''),
            $store->storeID(), ($store->pull() ? 'checked' : ''),
            $store->storeID()
    );

} ?>
</table>
</p>
<hr />
<h4 class="install">Testing Connections</h4>
<p class="ichunk" style="margin:0.0em 0em 0.4em 0em;">
    <ul>
<?php foreach($model->find('storeID') as $store) {
    $test = db_test_connect($store->dbHost(),
                            $store->dbDriver(),
                            $store->transDB(),
                            $store->dbUser(),
                            $store->dbPassword()
                           );
    echo '<li> Store #' . $store->storeID() . ': ' . ($test ? 'Connected' : 'No connection') . '</li>';
} ?>
    </ul>
    <i>Note: it's OK if this store's connection fails as long as it succeeds
       on the "Necessities" tab.</i>
</p>
<hr />
<p>
<button type=submit name="saveButton" value="Save" class="btn btn-default">Save</button>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<button type=submit name="addButton" value="Add Another Store" class="btn btn-default">Add Another Store</button>
</p>
</form>

<?php

        return ob_get_clean();

    // body_content
    }

// InstallStoresPage
}

FannieDispatch::conditionalExec(false);

?>
