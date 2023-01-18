<?php 


    include_once("conn.php");

    $method = $_SERVER["REQUEST_METHOD"];

    if($method === "GET") {

        $bordasQuery = $conn->query("SELECT * FROM bordas;");
        $bordas = $bordasQuery->fetchAll();

        $massassQuery = $conn->query("SELECT * FROM massas;");
        $massas = $massassQuery->fetchAll();

        $saboresQuery = $conn->query("SELECT * FROM sabores;");
        $sabores = $saboresQuery->fetchAll();


    } else if ($method === "POST") {
        $data = $_POST;

        $borda = $data["borda"];
        $massa = $data["massa"];
        $sabores = $data["sabores"];

       


        if(count($sabores) >3) {
            $_SESSION["msg"] = "SELECIONE NO MÁXIMO 3 SABORES!";
            $_SESSION["status"] = "warning";

        }
        else {
         
            $stmt = $conn->prepare("INSERT INTO pizzas (borda_id, massa_id) VALUES (:borda, :massa)");

            $stmt->bindParam(":borda", $borda, PDO::PARAM_INT);
            $stmt->bindParam(":massa", $massa, PDO::PARAM_INT);

            $stmt->execute();

            $pizzaId = $conn->lastInsertId();

            $stmt = $conn->prepare("INSERT INTO pizza_sabor (pizza_id, sabor_id) VALUES (:pizza, :sabor)");

            foreach($sabores as $sabor) {
                
                $stmt->bindParam(":pizza", $pizzaId, PDO::PARAM_INT);
                $stmt->bindParam(":sabor", $sabor, PDO::PARAM_INT);


                $stmt->execute();

                $_SESSION["msg"] = "PEDIDO REALIZADO COM SUCESSO";
                $_SESSION["status"] = "success"; 
 

            }

            $stmt = $conn->prepare("INSERT INTO pedidos (pizza_id, status_id) VALUES (:pizza, :status)");

            $statusId = 1;

            $stmt->bindParam(":pizza", $pizzaId);
            $stmt->bindParam(":status", $statusId);

            $stmt->execute();

        }


        header("Location: ..");
    }
?>