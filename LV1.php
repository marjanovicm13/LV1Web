<!DOCTYPE HTML>
<html>

    <head>
    </head>

    <body>

        <?php
            include_once('simple_html_dom.php');

            interface iRadovi{
                public function create($data);
                public function read();
                public function save();
            }

            class DiplomskiRadovi implements iRadovi
            {
                private $id = NULL;
                private $naziv_rada = NULL;
                private $tekst_rada = NULL;
                private $link_rada = NULL;
                private $oib_tvrtke = NULL;

                function __construct($data) {
                    $this->id = uniqid();
                    $this->naziv_rada = $data['naziv_rada'];
                    $this->tekst_rada = $data['tekst_rada'];
                    $this->link_rada = $data['link_rada'];
                    $this->oib_tvrtke = $data['oib_tvrtke'];
                }

                function create($data) {
                    self::__construct($data);
                }

                function read(){
                    $servername = "localhost";
                    $username = "root";
                    $password = "";
                    $dbname = "diplomskiradovi";

                    $conn = new mysqli($servername, $username, $password, $dbname);
                    if ($conn->connect_error) {
                            die("Connection failed: " . $conn->connect_error);
                    }
                    
                    $sql = "SELECT * FROM `diplomski_radovi`";
                    $output = $conn->query($sql);
                    if ($output->num_rows > 0) {
                            while($item = $output->fetch_assoc()) {
                                    echo "<br><br><br>ID: " . $item["id"] .
                                    "<br><br>OIB tvrtke: " . $item["oib_tvrtke"] .
                                    "<br><br>Naziv rada: " . $item["naziv_rada"] .
                                    "<br><br>Link rada: " . $item["link_rada"] .
                                    "<br><br>Tekst rada: " . $item["tekst_rada"];
                            }
                    }
                    $conn->close();
                }

                function save(){
                    $servername = "localhost";
                    $username = "root";
                    $password = "";
                    $dbname = "diplomskiradovi";

                    $conn = new mysqli($servername, $username, $password, $dbname);
                    if ($conn->connect_error) {
                            die("Connection failed: " . $conn->connect_error);
                    }

                    $id = $this->id;
                    $naziv = $this->naziv_rada;
                    $tekst = $this->tekst_rada;
                    $link = $this->link_rada;
                    $oib = $this->oib_tvrtke;

                    $sql = "INSERT INTO `diplomski_radovi` (`id`, `naziv_rada`, `tekst_rada`, `link_rada`, `oib_tvrtke`) VALUES ('$id', '$naziv', '$tekst', '$link', '$oib')";
                    if($conn->query($sql) === true) {
                            $this->read();
                    }
                    else {
                            echo "Error! " . $sql . "<br>" . $conn->error;
                    };
                    $conn->close();
                }
            }


           $url = 'https://stup.ferit.hr/index.php/zavrsni-radovi/page/3';
           $fp  = fopen($url, 'r');
           $html = file_get_html($url);

           if($html)
           {
                foreach($html->find('article') as $article) {
                        $image = $article->find('img', 0);

                        $rad = array(
                                'naziv_rada' => $article->find('h2.entry-title a')->plaintext,
                                'tekst_rada' => $article->find('div.fusion-post-content-container p')->plaintext,
                                'link_rada' => $article->find('h2.entry-title a')->href,
                                'oib_tvrtke' => substr($image->src, strrpos($image->src, '/') + 1, -4)
                        );
                        $newRad = new DiplomskiRadovi($rad);
                        $newRad->save();
                }
            }
            fclose($fp);  
       
        ?>

    </body>

</html>


