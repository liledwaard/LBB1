<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mouse shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <link rel="stylesheet" href="styles.css">
</head>

<body>

    <class="klasOne">
    <header>
    <h1 align = "center">Mouse shop</h1>
    </header>

    <h2 align = "center">Виконав: Ковальов Єгор Євгенійович</h2>
    <h2 align = "center">ст.гр. КІУКІ-20-3</h2>
    <h3 align = "center">Варіант: 5</h3>

    <div class='main'>
        <div class='klas'>
             <hr>
            <div class='klasTwo'>
                <h4>Categories:</h4>
                <?php
                    $sqlC = 'SELECT * FROM category';
                    $connectionC = new PDO('mysql:host=localhost;dbname=eshop','root','');
                    foreach ($connectionC->query($sqlC) as $row) {
                       echo ( '<input id="checkbox" type="checkbox" name="categories[]"value = "'.$row['ID_Category'].'"/>'.$row['c_name'].'</br>');
                    }
                 ?>
            </div>
            <hr class="klasOne">
            <div class='klasTwo'>
                <form action="index.php" method="post">
                <h4>Manufacturer:</h4>

                <?php
                    $sqlC = 'SELECT * FROM vendors';
                    $connectionC = new PDO('mysql:host=localhost;dbname=eshop','root','');
                    foreach ($connectionC->query($sqlC) as $row) {
                       echo ( '<input id="checkbox" type="checkbox" name="vendors[]" value = "'.$row['ID_Vendors'].'" />'.$row['v_name'].'</br>');
                    }
                 ?>
            </div>

                    <hr>
            <div class='klasTwo'>
                <h4>Price:</h4>
                <label>From:</label>
                <input type="number" name='price[]' value='0' class='sto'/>
                <br>
                <br>
                <label>To:</label>
                <input type="number" name='price[]' value='9999' class='sto'/>
            </div>
            <hr>
                <form method="POST" >
                  <input type="submit" name="submit" value="Filter" class="'klasThree">
                </form>
            
        </form>
        </div>

    <?php
    $connection = new PDO('mysql:host=localhost;dbname=eshop','root','');

    class Item {

        private $manufacturer;
        private $price;
        private $name;
        private $category;


        function set_manufacturer($manufacturer) {
            $dsn = 'mysql:host=localhost;dbname=eshop;charset=utf8';
            $username = 'root';
            $password = '';
            $pdo = new PDO($dsn, $username, $password);

            $query = 'SELECT v_name FROM vendors WHERE ID_Vendors ='.$manufacturer;
            $stmt = $pdo->prepare($query);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->manufacturer = $row['v_name'];
        }

        function set_category($category) {
            $dsn = 'mysql:host=localhost;dbname=eshop;charset=utf8';
            $username = 'root';
            $password = '';
            $pdo = new PDO($dsn, $username, $password);

            $query = 'SELECT c_name FROM category WHERE ID_Category = :category';
            $stmt = $pdo->prepare($query);
            $stmt->bindValue(':category',$category);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->category = $row['c_name'];
        }
  
        function __construct($name, $category, $manufacturer, $price){
            $this->name = $name;
            $this->set_category($category);
            $this->set_manufacturer($manufacturer);
            $this->price = $price;
        }

        function set_name($name) {
            $this->name = $name;
        }

        function set_price($price) {
            $this->price = $price;
        }

        function get_category() {
            return $this->category;
        }
        
        function get_price() {
            return $this->price;
        }
        
        function get_name() {
            return $this->name;
        }

        function get_manufacturer() {
            return $this->manufacturer;
        }

        function to_string()
           {
               return "Product name: {$this->get_name()} Category: {$this->get_category()} Vendor: {$this->get_manufacturer()} Price: {$this->get_price()}\n\t";
           }
    }

    $store = array();

    function setItems ($sql){
        global $connection;
        global $store;
        $store = array();
        foreach ($connection->query($sql) as $row) {
            $store[] = new Item ($row['name'],$row['FID_Category'],$row['FID_Vendor'],$row['price']);
        }

    }

    ?>


    <?php
  $vendors = $_POST['vendors'];
  $categories = $_POST['categories'];
  $price = $_POST['price'];

  $sqlRequest = 'SELECT * FROM items WHERE ';
  if(empty($vendors))
  {
    echo("You haven't turned to a single manufacturer.\n");
  }
  else
  {

    $N = count($vendors);
    for($j=0; $j < $N; $j++)
    {
      $sqlRequest .= '(FID_Vendor = '.$vendors[$j].' )';
      if ($j != $N-1){
        $sqlRequest .= ' OR ';
      }
    }
    if(!(empty($categories) && $price[0]==0 && $price[1]==0)){
        $sqlRequest .= ' AND ';
    }

  }


  if(empty($categories))
  {
    echo("You have not selected any categories.\n");
  }
  else
  {

    $N = count($categories);


    for($i=0; $i < $N; $i++)
    {
      $sqlRequest .= '(FID_Category = '.$categories[$i].' )';
      if ($i != $N-1){
        $sqlRequest .= ' OR ';
      }
    }
    if(!($price[0]==0 && $price[1]==0)){
        $sqlRequest .= ' AND ';
    }


  }

  if(empty($price))
  {

  }
  else
  {
    if ($price[0]>0){
       $sqlRequest .= ' (price >= '.$price[0].' ) ';
    }
    if ($price[0]>0 && $price[1]>0){
        $sqlRequest .= ' AND ';
    }

    if ($price[1]>0){
       $sqlRequest .= '( price < '.$price[1]. ' ) ';
    }
    echo $sqlRequest;
  }

  if (empty($vendors) && empty($categories) && $price[0]==0 && $price[1]==0){
    $sqlRequest = 'SELECT ID_Items, name, price, FID_Vendor, FID_Category FROM items ORDER BY ID_Items';
  }

  setItems($sqlRequest);



?>

    <main>
            <?php
                foreach ($store as $item){
                   echo nl2br ($item->to_string());
                }
            ?>
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
</body>

</html>