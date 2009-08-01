
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
        <?php include "header.php"; ?>
    </head>
	<body>
      <?php include "nav.php"; ?>
      <div class="content">
	  	<h1>Template Syntax Example</h1>
        <p class="normal">Variables are output with <span>{</span>{varname}}</p>
        <pre>
<span style="color:yellow"># A simple variable, in Controller it's $data['variable']</span>
<span>{</span>{variable}}

<span style="color:yellow"># Using assoc array, $data['user']['fullname'] = 'DooPHP'</span>
<span>{</span>{user.fullname}}

<span style="color:yellow"># Using functions/template tag with variables, case insensitive</span>
<span>{</span>{upper(variable)}}
<span>{</span>{UPPER(user.fullname)}}

<span style="color:yellow"># You can pass in arguments with the functions</span>
<span>{</span>{sample_with_args(variable, 'This is an argument')}}
        </pre>

        <p class="normal">Include a template file &lt;!-- include 'file' --&gt;</p>
<pre>
<span style="color:yellow"># Including a template file </span>
&lt;!-- include 'template_name_without_dot_html' --&gt;

<span style="color:yellow"># Including a template file which is in a sub directory</span>
&lt;!-- include 'folder/templatename' --&gt;

<span style="color:yellow"># Including a template file where the name is from a variable</span>
&lt;!-- include "<span>{</span>{filename}}" --&gt;
        </pre>

        <p class="normal">Looping a list &lt;!-- loop users --&gt;</p>
<pre>
<span style="color:yellow"># Looping a simple array $data['users']=array('john','doo','marie') </span>
&lt;!-- loop users --&gt;
    &lt;li&gt; <span>{</span>{users' value}} &lt;/li&gt;
&lt;!-- endloop --&gt;

<span style="color:yellow"># Or a shorter alternative ... </span>
&lt;!-- loop users --&gt;
    &lt;li&gt; <span>{</span>{users' v}} &lt;/li&gt;
&lt;!-- endloop --&gt;

<span style="color:yellow"># The loop name can be change ... </span>
&lt;!-- loop users --&gt;
    &lt;li&gt; <span>{</span>{loop' v}} &lt;/li&gt;
&lt;!-- endloop --&gt;



<span style="color:yellow"># Functions can be used in loop </span>
&lt;!-- loop users --&gt;
    &lt;li&gt; <span>{</span>{upper(users' value)}} &lt;/li&gt;
&lt;!-- endloop --&gt;

<span style="color:yellow"># Looping an assoc array</span>
 $data['users']=array(
    'john'=>array('name'=>'John Smith', 'gender'=>'male'),
    'lee'=>array('name'=>'Bruce Lee', 'gender'=>'male')
 );

&lt;!-- loop users --&gt;
    &lt;li&gt; <span>{</span>{users' key}} fullname is <span>{</span>{users' value.name}} gender is <span>{</span>{users' value.gender}} &lt;/li&gt;
&lt;!-- endloop --&gt;
        </pre>

        <p class="normal">Using Objects in Template <span>{</span>{object.@property}}</p>
        <pre>
<span style="color:yellow"># A simple object</span>
Doo::loadModel('SomeModel');
$obj = new SomeModel;
$obj->fullname = 'My Cool Name';
$obj->SomeObject->weight = 88
$data['obj'] = $obj;

<span>{</span>{obj.@fullname}}
<span>{</span>{upper(obj.@fullname)}}
<span>{</span>{obj.@SomeObject.@weight}}


<span style="color:yellow"># Looping an array of Object</span>
&lt;!-- loop users --&gt;
    &lt;li&gt;
        Name:   <span>{</span>{users' value.@fullname}}
        Gender: <span>{</span>{users' value.@gender}}
        Weight: <span>{</span>{users' v.@Physical.@weight}}
        Height: <span>{</span>{l' v.@Physical.@height}}
    &lt;/li&gt;
&lt;!-- endloop --&gt;
        </pre>


       <span class="totop"><a href="#top">BACK TO TOP</a></span>  
       </div>
	</body>
</html>
