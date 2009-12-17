<?php

class BlogController extends DooController {

    public $data;

	function home() {
        Doo::loadHelper('DooPager');
        Doo::loadModel('Post');

        $p = new Post();
        $p->status = 1;     //published post only

        $pager = new DooPager(Doo::conf()->APP_URL.'page', $p->count(), 4, 10);
        if(isset($this->params['pindex']))
            $pager->paginate(intval($this->params['pindex']));
        else
            $pager->paginate(1);

        //limit post and order by create time descendingly, get only Posts
        //$this->data['posts'] = $p->limit($pager->limit, null, 'createtime');

        //limit post and order by create time descendingly with post's tags
        $this->data['posts'] = $p->relateTag(
                                    array(
                                        'limit'=>$pager->limit,
                                        'desc'=>'post.createtime',
                                        'asc'=>'tag.name',
                                        'match'=>false      //Post with no tags should be displayed too
                                    )
                            );
        
        $this->data['pager'] = $pager->output;
        $this->data['rootUrl'] = Doo::conf()->APP_URL;

        //prepare sidebar data, tags and archive list
        $this->prepareSidebar();
        
        $this->render('index', $this->data);
	}

    /**
     * Prepare sidebar data, random tags and archive list
     */
    private function prepareSidebar(){

        //if tags cache exist, skip retrieving from DB, expires every 5 minutes
        $cacheTagOK = Doo::cache('front')->testPart('sidebarTag', 300);
        if(!$cacheTagOK){
            echo '<h2>Cache expired. Get Tags from DB!</h2>';
            //get random 10 tags
            Doo::loadModel('Tag');
            $tags = new Tag();
            $this->data['randomTags'] = $tags->limit(10, null, null, array('custom'=>'ORDER BY RAND()'));
        }else{
            $this->data['randomTags'] = array();
        }

        //if archive cache exist, skip retrieving from DB, archive expires when Post added, updated, deleted
        $cacheArchiveOK = Doo::cache('front')->testPart('sidebarArchive', 31536000);
        if(!$cacheArchiveOK){
            echo '<h2>Cache expired. Get Archives from DB!</h2>';
            //you can pass data to constructor to set the Model properties
            Doo::loadModel('Post');
            $p = new Post(array('status'=>1));
            $this->data['archives'] = $p->getArchiveSummary();
        }else{
            $this->data['archives'] =array();
        }
    }

    /**
     * This shows the same content as home(), but with pagination
     * Show error if Page index is invalid (negative)
     */
	function page() {
        if(isset($this->params['pindex']) && $this->params['pindex']>0)
    		$this->home();
        else
            return 404;
	}

    /**
     * Show single blog post page
     */
	function getArticle() {
        Doo::loadModel('Post');
        $p = new Post();
        $p->id = $this->params['postId'];
        $p->status = 1;

		$this->data['post'] = $p->relateTag(
									array(
										'limit'=>'first',
										'asc'=>'tag.name',
										'match'=>false      //Post with no tags should be displayed too
									)
							);
		
		//If post not found
		if($this->data['post']==Null)
			return 404;
			
		//get approved comments if totalcomment more than 0
		if($this->data['post']->totalcomment > 0){
			Doo::loadModel('Comment');
			$c = new Comment;
			$c->post_id = $this->data['post']->id;
			$c->status = 1;
			$this->data['comments'] = $c->find(array('asc'=>'createtime'));
		}
		
        $this->data['rootUrl'] = Doo::conf()->APP_URL;

        //prepare sidebar data, tags and archive list
        $this->prepareSidebar();

        $this->render('blog_single', $this->data);
	}

    /**
     * This shows the list of Posts in a month
     * Show error if month and year value is invalid
     */
    function getArchive(){
        $year = intval($this->params['year']);
        $month = intval($this->params['month']);

        //Check if date valid, Min date should be installation date of the Blog (2009 here)
        if($year<2009 || $year>2082 || $month<1 || $month>12)
            return 404;

        Doo::loadModel('Post');
        Doo::loadHelper('DooPager');
        
        $p = new Post();
		$p->status = 1;
		
        $totalArchive = $p->countArchive($year, $month);

        // Should display no post error here
        if($totalArchive<1)
            return 404;

        $pager = new DooPager(Doo::conf()->APP_URL."archive/$year/$month/page", $totalArchive, 4, 10);
        if(isset($this->params['pindex']))
            $pager->paginate(intval($this->params['pindex']));
        else
            $pager->paginate(1);

        //get post in that month
		$this->data['posts'] =  $p->getArchiveList($year, $month, $pager->limit);
        $this->data['pager'] = $pager->output;
        $this->data['rootUrl'] = Doo::conf()->APP_URL;

        //prepare sidebar data, tags and archive list
        $this->prepareSidebar();

        $this->render('index', $this->data);
    }

    /**
     * This shows the list of Posts filter by a Tag name
     * Show error if the tag name has no Posts
     */
	function getTag() {
        Doo::loadHelper('DooPager');
        Doo::loadModel('Post');
        Doo::loadModel('Tag');

        $p = new Post();
        $p->status = 1;     //published post only

        $tag = new Tag;
        $tag->name = trim(urldecode($this->params['name']));
        
        $totalPosts = $tag->relatePost(array('select'=>'COUNT(tag.id) AS total', 'asArray'=>true));
        $totalPosts = $totalPosts[0]['total'];

        if($totalPosts<1){
            return 404;
        }

        $pager = new DooPager(Doo::conf()->APP_URL."tag/$tag->name/page", $totalPosts, 4, 10);
        if(isset($this->params['pindex']))
            $pager->paginate(intval($this->params['pindex']));
        else
            $pager->paginate(1);

        //limit post and filter by the related Tag name
        $this->data['posts'] = $p->relateTag(
                                    array(
                                        'limit'=>$pager->limit,
                                        'desc'=>'post.createtime',
                                        'where'=>'tag.name=?',
                                        'param'=>array($tag->name),
                                        'match'=>false      //Post with no tags should be displayed too
                                    )
                            );

        $this->data['pager'] = $pager->output;
        $this->data['rootUrl'] = Doo::conf()->APP_URL;

        //prepare sidebar data, tags and archive list
        $this->prepareSidebar();

        $this->render('index', $this->data);
	}


    /**
     * Validate if post exists and the post must be publised (For inserting comment)
     */
    static function checkPostExist($id){
        Doo::loadModel('Post');
        $p = new Post;
        $p->id = $id;
        $p->status = 1;

        //if Post id doesn't exist, return an error
        if($p->find(array('limit'=>1, 'select'=>'id'))==Null)
            return 'Post not found in database';
    }

    function newComment(){
        foreach($_POST as $k=>$v){
            $_POST[$k] = trim($v);
        }

        if($_POST['url']=='http://' || empty($_POST['url']))
            unset($_POST['url']);

        //strip html tags in comment
        if(!empty($_POST['content'])){
            $_POST['content'] = strip_tags($_POST['content']);
        }

        Doo::loadModel('Comment');
        $c = new Comment($_POST);

        $this->prepareSidebar();

        // 'skip' is same as DooValidator::CHECK_SKIP
        if($error = $c->validate( 'skip' ) ){
            $this->data['rootUrl'] = Doo::conf()->APP_URL;
            $this->data['title'] =  'Oops! Error Occured!';
            $this->data['content'] =  '<p style="color:#ff0000;">'.$error.'</p>';
            $this->data['content'] .=  '<p>Go <a href="javascript:history.back();">back</a> to post.</p>';
            $this->render('error', $this->data);
        }
        else{
            Doo::autoload('DooDbExpression');
            $c->createtime = new DooDbExpression('NOW()');
            $c->insert();

            $this->data['rootUrl'] = Doo::conf()->APP_URL;
            $this->render('comment', $this->data);
        }
    }

}
?>