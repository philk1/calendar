<?
/*
 Philip Kronfli
 5/15/13
 CMSC 433 
 Web Project Part 1
*/
$path = preg_replace('/\/www\/(\w+)\/.*/', '/www-data/\1/read-write/', getcwd());
$data_file = $path . 'events.json';


if($_SERVER['REQUEST_METHOD'] == 'POST'){
  
  if($_REQUEST['action']=='create'){
    $data = get_data();
    $event = array(
		   'id' => uniqid("",false),
		   'title' => stripslashes($_REQUEST['title']),
		   'start' => stripslashes($_REQUEST['start']),
		   'end' => stripslashes($_REQUEST['end'])
		   ); 
    array_unshift($data, $event);
    if(file_put_contents($data_file, json_encode($data), LOCK_EX)) {
      json(array('result' => true, 'message' => json_encode($data)));
    } 
    else {
      json(array('result' => false, 'message' => 'Unable to create'));
    }
  }
  
  if($_REQUEST['action']=='delete'){
    $data = get_data();
    $temp = array();
    $toDelete = $_REQUEST['id'];
    for ($i=0; $i<count($data); $i++){
      if($data[$i]["id"]!=$toDelete){
	array_unshift($temp, $data[$i]);
      }
    }
    if(file_put_contents($data_file, json_encode($temp), LOCK_EX)) {
      json(array('result' => true, 'message' => json_encode($temp)));
    }
    else {
      json(array('result' => false, 'message' => 'Unable to delete'));
    }

      
  }

}


else if($_SERVER['REQUEST_METHOD'] == 'GET'){
  $data = get_data();
  echo json_encode($data);
}


function get_data() {
  global $data_file;
  $data = json_decode(file_get_contents($data_file), true);
  return $data == NULL ? array() : $data;
}


function json($data) {
  header('Content-type: application/json; charset=utf-8');
  header('Pragma: no-cache');
  header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
  print json_encode($data);
}

?>