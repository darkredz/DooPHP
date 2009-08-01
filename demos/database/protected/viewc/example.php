
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
        <?php include "header.php"; ?>
    </head>
	<body>
      <?php include "nav.php"; ?>
      <div class="content">
	  	<h1>Database ORM Example</h1>
        <p class="normal">There methods <strong>find, relate, insert, update, relatedInsert, relatedUpdate, insert_attributes, update_attributes, and delete</strong> are the main operations in DooSqlMagic.
        And it should be sufficient for you to do DBRMS operations wihtout writing SQL manually.</p>
        <pre>
<span style="color:yellow"># Enable sql tracking for debug purpose, remove it in Production mode</span>
Doo::db()->sql_tracking = true;

<span style="color:yellow"># A simple Find all</span>
Doo::db()->find('Food');

<span style="color:yellow"># A simple relate search</span>
Doo::db()->relate('Food', 'FoodType');

<span style="color:yellow"># A simple relate search that ONLY returns FoodType that has Food</span>
Doo::db()->relate('FoodType','Food', array('match'=>true));

<span style="color:yellow"># A simple relate search that ONLY returns FoodType that has Food</span>
Doo::db()->relate('FoodType','Food', array('match'=>true));

<span style="color:yellow"># Find a record, this is exposed to sql injection and doesn't handle any escaping/quoting </span>
Doo::db()->find('Food', array(
                            'limit'=>1,
                            'where'=>'food.name = ' . $this->params['foodname'],
                    ));

<span style="color:yellow"># Instead, Do this! Pass array to param</span>
Doo::db()->find('Food', array(
                            'limit'=>1,
                            'where'=>'food.name = ?',
                            'param'=> array( $this->params['foodname'] )
                    ));

<span style="color:yellow"># Or create the Model object and search. Escaping/quoting is automatically done</span>
Doo::loadModel('Food');
$food = new Food;
$food->name = $this->params['foodname'];
Doo::db()->find($food, array('limit'=>1));
        </pre>

<p>Go ahead and <a class="file" href="http://doophp.com/download">download the code</a> to learn more!</p>
       <span class="totop"><a href="#top">BACK TO TOP</a></span>  
       </div>
	</body>
</html>
