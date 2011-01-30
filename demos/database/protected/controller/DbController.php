<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DbController
 *
 * @author leng
 */
class DbController extends DooController{
    //put your code here
    public function allFood(){
        $data['printr'] = Doo::db()->find('Food');
        $data['title'] = 'All Food in DB';
        $data['content'] = 'SQL executed. <pre>'. implode("\n\r", Doo::db()->showSQL()) .'</pre>';
        $data['baseurl'] = Doo::conf()->APP_URL;
        $this->view()->render('template', $data);
    }

    public function all_food_with_type(){
        $data['printr'] = Doo::db()->relate('Food', 'FoodType');
        $data['title'] = 'All Food with FoodType in DB';
        $data['content'] = 'SQL executed. <pre>'. implode("\n\r", Doo::db()->showSQL()) .'</pre>';
        $data['baseurl'] = Doo::conf()->APP_URL;
        $this->view()->render('template', $data);
    }

    public function foodtype_with_its_food(){
        $data['printr'] = Doo::db()->relate('FoodType','Food');
        $data['title'] = 'All FoodType with its Food list in DB';
        $data['content'] = 'SQL executed. <pre>'. implode("\n\r", Doo::db()->showSQL()) .'</pre>';
        $data['baseurl'] = Doo::conf()->APP_URL;
        $this->view()->render('template', $data);
    }

    public function foodtype_with_its_food_matched(){
        $data['printr'] = Doo::db()->relate('FoodType','Food', array('match'=>true));
        $data['title'] = 'All FoodType with its Food list in DB';
        $data['content'] = 'SQL executed. <pre>'. implode("\n\r", Doo::db()->showSQL()) .'</pre>';
        $data['baseurl'] = Doo::conf()->APP_URL;
        $this->view()->render('template', $data);
    }


    public function food_with_type_by_name(){
        try{
            //for related types to get one object, use limit first instead of limit 1
            $data['printr'] = Doo::db()->relate('Food', 'FoodType',
                                    array(
                                        'limit'=>'first',
                                        'where'=>'food.name LIKE ?',
                                        'param'=>array('%'. str_replace('%20', ' ', $this->params['foodname']).'%')
                                ));
        }catch(Exception $err){
            //to debug if there's any SQL error
            echo '<pre>';
            print_r( Doo::db()->showSQL() );
            print_r($ee);
            exit;
        }
        $data['title'] = 'Food with FoodType by food name in DB';
        $data['content'] = 'SQL executed. <pre>'. implode("\n\r", Doo::db()->showSQL()) .'</pre>';
        $data['baseurl'] = Doo::conf()->APP_URL;
        $this->view()->render('template', $data);
    }


    public function get_recipe_using_model(){
        $food = Doo::db()->find('Food', array(
                                        'limit'=>1,
                                        'where'=>'name LIKE ?',
                                        'param'=>array('%'. str_replace('%20', ' ', $this->params['foodname']).'%')
                                ));
        //if not found
        if(!isset($food->id))
            return array('/db/failed/Food not found!', 404);

        $data['printr'] = $food->get_recipe();
        $data['title'] = 'Recipe by food name in DB';
        $data['content'] = 'SQL executed. <pre>'. implode("\n\r", Doo::db()->showSQL()) .'</pre>';
        $data['baseurl'] = Doo::conf()->APP_URL;
        $this->view()->render('template', $data);
    }

    public function get_food_by_id_using_model(){
        Doo::loadModel('Food');
        $food = new Food;
        $food->id = $this->params['id'];
        $food = $food->get_by_id();
        //if not found
        if(!isset($food->id))
            return array('/db/failed/Food id not found!', 404);

        $data['printr'] = $food;
        $data['title'] = 'Recipe by food id in DB';
        $data['content'] = 'SQL executed. <pre>'. implode("\n\r", Doo::db()->showSQL()) .'</pre>';
        $data['baseurl'] = Doo::conf()->APP_URL;
        $this->view()->render('template', $data);
    }

    public function get_articles_by_foodname(){
        $food = Doo::loadModel('Food', true);
        $food->name = str_replace('%20', ' ', $this->params['foodname']);
        $food = Doo::db()->find($food, array('limit'=>1));     //needs to match exactly
        print_r($food);
        //if not found
        if(!isset($food->id))
            return array('/db/failed/Food name not found! Need to match exactly', 404);

        $food_with_articles = Doo::db()->relate($food, 'Article', array('limit'=>'first'));

        $data['printr'] = $food_with_articles;
        $data['title'] = 'Food with Articles by food name in DB';
        $data['content'] = 'SQL executed. <pre>'. implode("\n\r", Doo::db()->showSQL()) .'</pre>';
        $data['baseurl'] = Doo::conf()->APP_URL;
        $this->view()->render('template', $data);
    }

    public function get_articles_by_foodname_desc(){
        $food = Doo::loadModel('Food', true);
        $food->name = str_replace('%20', ' ', $this->params['foodname']);
        $food = Doo::db()->find($food, array('limit'=>1));     //needs to match exactly
        //
        //if not found
        if(!isset($food->id))
            return array('/db/failed/Food name not found! Need to match exactly', 404);

        $food_with_articles = Doo::db()->relate($food, 'Article',
                                        array(
                                           'limit'=>'first',
                                           'desc'=>'createtime'
                              ));

        $data['printr'] = $food_with_articles;
        $data['title'] = 'Food with Articles by food name in DB';
        $data['content'] = 'SQL executed. <pre>'. implode("\n\r", Doo::db()->showSQL()) .'</pre>';
        $data['baseurl'] = Doo::conf()->APP_URL;
        $this->view()->render('template', $data);
    }


    public function get_articles_by_foodname_desc_not_draft(){
        $food = Doo::loadModel('Food', true);
        $food->name = str_replace('%20', ' ', $this->params['foodname']);
        $food = Doo::db()->find($food, array('limit'=>1));     //needs to match exactly
        
        //if not found
        if(!isset($food->id))
            return array('/db/failed/Food name not found! Need to match exactly', 404);

        $food_with_articles = Doo::db()->relate($food, 'Article',
                                        array(
                                           'limit'=>'first',
                                           'desc'=>'createtime',
                                           'where'=>'article.draft<>1'
                              ));

        $data['printr'] = $food_with_articles;
        $data['title'] = 'Food with Articles by food name in DB';
        $data['content'] = 'SQL executed. <pre>'. implode("\n\r", Doo::db()->showSQL()) .'</pre>';
        $data['baseurl'] = Doo::conf()->APP_URL;
        $this->view()->render('template', $data);
    }


    public function insertFood(){
        if(!isset($this->params['foodname']) && !isset($this->params['desc']) && !isset($this->params['location']) && !isset($this->params['foodtype']))
            return array('/db/failed/Fields cannot be empty', 404);
        Doo::loadModel('Food');
        Doo::loadModel('FoodType');

        $foodtype = new FoodType;
        $foodtype->name = $this->params['foodtype'];
        $foodtype = $this->db()->find($foodtype, array('limit'=>1));

        //Or use this
        # $foodtype = Doo::db()->find('FoodType', array('where'=>'name=?', 'param'=>$this->params['foodtype'], 'limit'=>1) );

        //if not found
        if(!isset($foodtype->id))
            return array('/db/failed/Food type not found!', 404);

        $food = new Food;
        $food->food_type_id = $foodtype->id;
        $food->name = str_replace('%20', ' ', $this->params['foodname']);
        $food->description = str_replace('%20', ' ', $this->params['desc']);
        $food->location = str_replace('%20', ' ', $this->params['location']);

        $food->id = $this->db()->insert($food);
        // Doo::db()->insert($food);
        
        $data['printr'] = $food;
        $data['title'] = 'Food Inserted!';
        $data['content'] = 'SQL executed. <pre>'. implode("\n\r", Doo::db()->showSQL()) .'</pre>';
        $data['baseurl'] = Doo::conf()->APP_URL;
        $this->view()->render('template', $data);
    }

    public function insert_food_with_ingredient(){
        if(!isset($this->params['ingredient']) && !isset($this->params['foodname']) && !isset($this->params['desc']) && !isset($this->params['location']) && !isset($this->params['foodtype']))
            return array('/db/failed/Fields cannot be empty', 404);
        Doo::loadModel('Food');
        Doo::loadModel('FoodType');
        Doo::loadModel('Ingredient');

        $foodtype = new FoodType;
        $foodtype->name = $this->params['foodtype'];
        $foodtype = $this->db()->find($foodtype, array('limit'=>1));
        
        //if not found
        if(!isset($foodtype->id))
            return array('/db/failed/Food type not found!', 404);

        $food = new Food;
        $food->food_type_id = $foodtype->id;
        $food->name = str_replace('%20', ' ', $this->params['foodname']);
        $food->description = str_replace('%20', ' ', $this->params['desc']);
        $food->location = str_replace('%20', ' ', $this->params['location']);

        $ingredient = new Ingredient;
        $ingredient->name = str_replace('%20', ' ', $this->params['ingredient']);

        try{
            $food->id = Doo::db()->relatedInsert($food, array($ingredient));
        }catch(Exception $err){
            //to debug if there's any SQL error
            echo '<pre>';
            print_r( Doo::db()->showSQL() );
            print_r($err);
            exit;
        }

        $data['printr'] = $food;
        $data['title'] = 'Food Inserted with Ingredient ' . $this->params['ingredient'];
        $data['content'] = 'SQL executed. <pre>'. implode("\n\r", Doo::db()->showSQL()) .'</pre>';
        $data['baseurl'] = Doo::conf()->APP_URL;
        $this->view()->render('template', $data);
    }

    public function food_with_ingredients(){
        try{
            $food = Doo::db()->relate('Food', 'Ingredient');
        }catch(Exception $err){
            //to debug if there's any SQL error
            echo '<pre>';
            print_r( Doo::db()->showSQL() );
            print_r($err);
            exit;
        }        $data['printr'] = $food;
        $data['title'] = 'Food with Ingredients';
        $data['content'] = 'SQL executed. <pre>'. implode("\n\r", Doo::db()->showSQL()) .'</pre>';
        $data['baseurl'] = Doo::conf()->APP_URL;
        $this->view()->render('template', $data);
    }

    public function ingredients_with_food(){
        try{
            $food = Doo::db()->relate('Ingredient', 'Food');
        }catch(Exception $err){
            //to debug if there's any SQL error
            echo '<pre>';
            print_r( Doo::db()->showSQL() );
            print_r($err);
            exit;
        }        $data['printr'] = $food;
        $data['title'] = 'Ingredient with Food';
        $data['content'] = 'SQL executed. <pre>'. implode("\n\r", Doo::db()->showSQL()) .'</pre>';
        $data['baseurl'] = Doo::conf()->APP_URL;
        $this->view()->render('template', $data);
    }

    public function food_ingredients_update(){
        if(!isset($this->params['ingredient']) && !isset($this->params['foodname']) && !isset($this->params['location']) )
            return array('/db/failed/Fields cannot be empty', 404);

        $food = Doo::loadModel('Food', true);
        $food->name = str_replace('%20', ' ', $this->params['foodname']);
        $food = Doo::db()->find($food, array('limit'=>1));     //needs to match exactly
        
        //if not found
        if(!isset($food->id))
            return array('/db/failed/Food name not found! Need to match exactly', 404);

        $food->location =  str_replace('%20', ' ', $this->params['location']);
        
        $ingredient = Doo::loadModel('Ingredient', true);
        $ingredient->name = str_replace('%20', ' ', $this->params['ingredient']);

        Doo::db()->relatedUpdate($food, array($ingredient), array('field'=>'location'));

        $data['printr'] = array($food, $ingredient);
        $data['title'] = 'Food with Ingredients Update';
        $data['content'] = 'SQL executed. <pre>'. implode("\n\r", Doo::db()->showSQL()) .'</pre>';
        $data['baseurl'] = Doo::conf()->APP_URL;
        $this->view()->render('template', $data);
    }

    public function nasilemak_type_article(){
        // include is to be used on a Single result only
        $data['printr'] = Doo::db()->relate('Food', 'FoodType', array(
                                                        'where'=>"food.name='Nasi Lemak'",
                                                        'include'=>'Article',
                                                        'includeWhere'=>'article.draft=?',
                                                        'includeParam'=>array(0),
                                                        'limit'=>'first'
            ));
        $data['title'] = 'Food, Food Type and Articles';
        $data['content'] = 'SQL executed. <pre>'. implode("\n\r", Doo::db()->showSQL()) .'</pre>';
        $data['baseurl'] = Doo::conf()->APP_URL;
        $this->view()->render('template', $data);
    }

    public function db_error(){
        $data['printr'] = null;
        $data['title'] = 'DB Error';
        $data['content'] = $this->params['msg'];
        $data['baseurl'] = Doo::conf()->APP_URL;
        $this->view()->render('template', $data);
    }
}
?>
