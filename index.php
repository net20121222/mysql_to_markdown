<?php 
require 'vendor/autoload.php';
error_reporting( E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_WARNING );
// Using Medoo namespace
use Medoo\Medoo;

$config = [
	'db'=> trim($_GET['name']),
	'host'=> trim($_GET['host']),
	'user'=> trim($_GET['user']),
	'pwd'=> trim($_GET['pwd']),
	'charset'=> trim($_GET['charset']),
];
$flag = false;
if($config['db']){

	setcookie('config',json_encode($config));


	$flag = true;
    // Initialize
$db = new Medoo([
    'database_type' => 'mysql',
    'database_name' => $config['db'],
    'server' => $config['host'],
    'username' => $config['user'],
    'password' => $config['pwd'],
    'charset' => $config['charset']?$config['charset']:'utf8',
]);
$rs = $db->query('show tables')->fetchAll();
foreach($rs as $v){
	$tb = $v[0];
	$tbs[] = $tb;
	/**
	 * view table comments 
	 * @var string
	 */
	$sql  = "select *
	  from information_schema.tables
	  where table_schema = '".$config['db']."'
	   and table_name = '".$tb."'";
	$fet = $db->query($sql)->fetch();
	$table_comm = $fet['TABLE_COMMENT']; 
 
	$sql = "SHOW FULL COLUMNS FROM ".$tb;
	$cols = $db->query($sql)->fetchAll();
	unset($list);
	foreach($cols as $v1){ 
		$list[] = [ 
			$v1['Field'],
			$v1['Comment'] 
		];
	}
	$arr[$tb] = [$table_comm,$list];
	 
}
$str = '';
foreach($arr as $tab=>$li){
	$str .= "## 表名 ".$tab. "[".$li[0]."]\n";
	$str .= "字段名|备注\n- | :-: \n";
	foreach($li[1] as $v){
		$str .= $v[0]."|".$v[1]."\n";
	}
	
}
}

$cookie = $_COOKIE['config'];
if($cookie){
	$cookie = json_decode($cookie,true);
	 
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Bootswatch: Default</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="stylesheet" href="https://bootswatch.com/_vendor/bootstrap/dist/css/bootstrap.min.css" media="screen">
 
    <script>

      
    </script>
  </head>
  <body>
 <div class="container" style="margin-top: 100px;">

      <div class="page-header" id="banner" style="margin-bottom: 60px;">
        <div class="row">
          <div class="col-lg-8 col-md-7 col-sm-6">
            
            <?php if($flag == true){?>
            	<h3>复制以下内容，放到markdown编辑器中使用。 </h3>
            <?php }else{?>
            	<h3>MYSQL表结构生成Markdown格式 </h3>
            <?php } ?>	
          </div>
          
        </div>
      </div>			

<?php if($flag == true){?>
<div class="alert alert-dismissible alert-info">
   <textarea style="width: 100%;height: 300px;"><?php echo $str;?></textarea>
</div>
<?php }else{?>

 	 
<form method="get">
<div class="form-group">
  <label class="col-form-label" for="inputDefault">数据库主机地址</label>
  <input type="text" class="form-control"  value="<?php echo trim($_GET['host']?:$cookie['host']);?>" placeholder="" name="host">
</div>

<div class="form-group">
  <label class="col-form-label" for="inputDefault">用户名</label>
  <input type="text" class="form-control"   value="<?php echo trim($_GET['user']?:$cookie['user']);?>" placeholder="" name="user">
</div>

<div class="form-group">
  <label class="col-form-label" for="inputDefault">密码</label>
  <input type="text" class="form-control"  value="<?php echo trim($_GET['pwd']?:$cookie['pwd']);?>" placeholder="" name="pwd">
</div>

<div class="form-group">
  <label class="col-form-label" for="inputDefault">数据库名</label>
  <input type="text" class="form-control" placeholder=""   value="<?php echo trim($_GET['db']?:$cookie['db']);?>" name="name">
</div>
 
<div class="form-group">
  <label class="col-form-label" for="inputDefault">字符集(默认utf8)</label>
  <input type="text" class="form-control" placeholder=""   value="<?php echo trim($_GET['charset']?:$cookie['charset']);?>" name="charset">
</div>
 
<button type="submit" class="btn btn-primary">生成</button>
</form>

<?php }?>
</div>
  </body>
</html>	
