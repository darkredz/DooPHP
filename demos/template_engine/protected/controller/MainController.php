<?php
/**
 * Description of MainController
 *
 * @author darkredz
 */
class MainController extends DooController{

    public function index(){
        $data['baseurl'] = Doo::conf()->APP_URL;
        
        // include files are automatically parsed and compiled when the parent template file is compiled
        // if you have changes in a include file, just touch or modified & save the parent file to compile it.
        // However, using variable in template as an include filename would not generate any compile file,
        // You would need to compile in manually, <!-- include "variable_include" --> or $this->view()->render('variable_include');
        $data['file'] = 'variable_include';
        $data['nested'] = 'Hello! DooPHP';

        $data['username'] = 'doodemo';
        $data['pwd'] = '1234';

        $data['messages'] = array('Please callback, thanks.','$1000 cash to earn','Supernova photos','Weather today is very hot!');

        $data['user'] = array(
                            'kee' => 'Lee Kee Seng',
                            'john' => 'John Smith',
                        );

        // used in template as member.total.male, member.titalKids.male
        $data['member']= array(
                                'total'=>array('male'=>100, 'female'=>301),
                                'totalKids'=>array('male'=>60, 'female'=>201),                        
                                'totalTeen'=>array('male'=>40, 'female'=>100),                        
                        );

		if(Doo::cache('front')->testPart('messages', 3600)==false){
			echo 'Regenerated because cache has expired!';
			$data['usermsg'] = array(
                            'leng' =>array('Please callback, thanks.','$1000 cash to earn','Supernova photos','Weather today is very hot!'),
                            'john' =>array('Hi google','I am so happy now!','cool day huh?'),
                            'john2' =>array('Hi google','I am so happy now!','cool day huh?'),
                            'john3' =>array('Hi google','I am so happy now!','cool day huh?'),
                            'john4' =>array('Hi google','I am so happy now!','cool day huh?'),
                            'john5' =>array('Hi google','I am so happy now!','cool day huh?'),
                        );
		}
        $data['msgdetails'] = array(
                                array('subject'=>'Cool stuff on my doormat', 'date'=>'2009-09-13', 'attachment'=>array('pdf'=>'benchmark.pdf', 'doc'=>'readme.doc')),
                                array('subject'=>'Message 2 here hi!', 'date'=>'2029-12-03', 'attachment'=>array('pdf'=>null, 'doc'=>null))
                            );


        // Objects can be used in the template too!
        // Used in template as winner.@fullname, winner.@Physical.@height
        Doo::loadModel('Winner');
        $obj = new Winner;
        $obj->fullname = 'Mr. Object';
        $obj->gender = 'unisex';

        $obj->Physical->weight = 562;
        $obj->Physical->height = 180;

        $data['winner'] = $obj;
        $data['winners'] = array();

        for($i=0;$i<4;$i++){
            $obj = new Winner;
            $obj->fullname = 'Mr. Object '.$i;
            $obj->gender = 'unisex';
            $obj->Physical->weight = rand(200,600);
            $obj->Physical->height = rand(150,200);
            $data['winners'][] = $obj;
        }
        
        //blog post with tags, template engine using loop with assoc array (Tag)
        Doo::loadModel('Blog');
        Doo::loadModel('Tag');
        $data['posts'] = array();
        for($i=0;$i<3;$i++){
            $obj = new Blog;
            $obj->title = 'This is a title '.$i;
            $obj->content = 'Read this content '.$i;
            $obj->Tag = array();
            for($g=0;$g<3;$g++){
                $tag = new Tag;
                $tag->name = 'tag' . $g;
                $obj->Tag[] = $tag;
            }
            $data['posts'][] = $obj;
        }
        
        
        $this->view()->render('about', $data);
        
        /* passing a true will enable the engine to process the template and compiled files
           If the template file is newer, then it will be compiled again. */
        # $this->view()->render('about', $data, true);

        /*  This is useful in production mode where DooPHP doesn't auto process it for performance.
            If you want it to force compile everytime, just pass in both as true */
        # $this->view()->render('about', $data, true, true);

    }

    public function url(){
        $data['title'] = 'URL used in this demo';
        $data['content'] = 'Replace :var with your values.<br/><em>Request type */GET = You can test and visit these links.</em>';
        $data['baseurl'] = Doo::conf()->APP_URL;

        include Doo::conf()->SITE_PATH .'protected/config/routes.conf.php';
        $data['printr'] = array();
        $n = 1;
        foreach($route as $req=>$r){
            foreach($r as $rname=>$value){
                //$rname_strip = (strpos($rname, '/')===0)? substr($rname, 1, strlen($rname)) : $rname;
                $rname_strip = 'index.php'.$rname;
                $data['printr'][$n++ .strtoupper(" $req")] = '<a href="'.Doo::conf()->APP_URL.$rname_strip.'">'.$rname.'</a>';
            }
        }
        $this->view()->render('template', $data);
    }

    public function example(){
        $data['baseurl'] = Doo::conf()->APP_URL;
        $data['printr'] = file_get_contents(Doo::conf()->SITE_PATH .'protected/config/routes.conf.php');
        $this->view()->render('example', $data);
    }

    public function template_source(){
        print_r(file_get_contents( Doo::conf()->SITE_PATH .'protected/view/about.html' ));
        exit;
    }

}
?>