<?php 
ob_start();
session_start();?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daily Expense Tracker | Report Generation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
  </head>
  <body background="../img/background_image.jpg">
    <?php
        if(!isset($_SESSION["user"])){
            header("Location:../login/");
        }
    ?>
    
    <div class="container"> 

    <div class="card" style="margin-top:15px;">
        <h2 class="text-center" style="margin-top:10px;">Report Generation & Export</h2> 
        
        <div class="card-body"> 
                    <div class="row">
                        <div class="col-md-6">
                        </div>
                        <div class="col-md-3">
                        </div>
                        <div class="col-md-3">
                        <h4><span class="badge rounded-pill text-bg-light"><?php if(isset($_SESSION["user"])){echo $_SESSION["user"];}?></span>
                        <a href="../logout/" class="btn btn-secondary">Logout</a>
                        </h4>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">

                            <form class="row g-3" action="" method="POST">
                                <div class="row-auto">
                                    <label for="from_date" class="">From Date</label>
                                    <input type="date" class="form-control" name="from_date" id="from_date" placeholder="Enter From Date" required>
                                </div>
                                <div class="row-auto">
                                    <label for="to_date" class="">To Date</label>
                                    <input type="date" class="form-control" name="to_date" id="to_date" placeholder="Enter To Date" required>
                                </div>
                                <div class="row-auto">
                                <label for="purchased_by" class="form-label">Spent By</label>
                                <select class="form-select" aria-label="Default select example" name="purchased_by" id="purchased_by" required>
                                    <option value="">Select Spent By</option>
                                    <?php ob_start(); require "../select/purchased_by.php"; ?>
                                </select> 
                                </div>   
                                <div class="row-auto">
                                    <input type="submit" name="generate_report" value="Generate" class="btn btn-primary mb-3">
                                </div>
                                <div class="row-auto">
                                    <a id="exporttable" class="btn btn-primary">Export To Excel</a>
                            
                                    <a href="../index.php" class="btn btn-primary">Back</a>
                                </div>

                            </form>

                            
                        </div>
                        <div class="col-md-3">
                        </div>
                        <div class="col-md-3">
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-12">
                        <?php 
                            if(isset($_POST['generate_report'])){

                                $from_date = $_POST['from_date'];
                                $to_date = $_POST['to_date'];
                                $purchased_by = $_POST['purchased_by'];
                                
                                require "../db/database.php";
                                                        
                                                        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE,PDO::FETCH_ASSOC);
                                                        try {

                                                            $sql = "SELECT Id,
                                                                                Product_Name,
                                                                                Buying_Description,
                                                                                Price,
                                                                                Purchased_By,
                                                                                Date_Purchased,
                                                                                Remarks,
                                                                                Created_By,
                                                                                Date_Created,
                                                                                Modified_By,
                                                                                Date_Modified,
                                                                                ROW_NUMBER() OVER (ORDER BY Date_Purchased) Row_Num, 
                                                                                (SELECT SUM(Price) 
                                                                                FROM daily_expenses
                                                                                WHERE Date_Purchased >= '$from_date' AND Date_Purchased <= '$to_date'
                                                                                AND Purchased_By='$purchased_by') Grand_Total
                                                                                FROM daily_expenses
                                                            WHERE Date_Purchased >= '$from_date' AND Date_Purchased <= '$to_date' AND Purchased_By='$purchased_by' AND Action_ != 'D'";

                                                            $stmt = $pdo->prepare($sql);

                                                            $stmt->execute();
                                                    
                                                            $result = $stmt->fetchAll();

                                                            $result_count=$stmt->rowCount();}

                                                            catch (PDOException $e) {
                                                                echo $e->getMessage();
                                                            }
                                                            

                                if($result_count>0){
                                    

                            ?>
                                    <div style="overflow-x:auto">
                                    <table id="htmltable" class="table table-striped table-responsive" style="width:2000px;">
                                                <thead class="table-dark">
                                                    <tr>
                                                    <th scope="col">#</th>
                                                    <th scope="col">Item Name</th>
                                                    <th scope="col">Spending Description</th>
                                                    <th scope="col">Price</th>
                                                    <th scope="col">Spent By</th>
                                                    <th scope="col">Date Spent</th>
                                                    <th scope="col">Remarks</th>
                                                    <th scope="col">Added By</th>
                                                    <th scope="col">Date Added</th>
                                                    <th scope="col">Modifed By</th>
                                                    <th scope="col">Date Modifed</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                            <?php 
                                                        foreach ($result as $row) {

                                                                ?>
                                                                    <tr>
                                                                    <th scope="row"><?php echo $row["Row_Num"]; ?></th>
                                                                    <td><?php echo $row["Product_Name"]; ?></td>
                                                                    <td><?php echo $row["Buying_Description"]; ?></td>
                                                                    <td><?php echo $row["Price"]." ₹"; ?></td>
                                                                    <td><?php echo $row["Purchased_By"]; ?></td>
                                                                    <td><?php if($row["Date_Purchased"]!=''){ echo date("d/m/Y", strtotime($row["Date_Purchased"])); } ?></td>
                                                                    <td><?php echo $row["Remarks"]; ?></td>
                                                                    <td><?php echo $row["Created_By"]; ?></td>
                                                                    <td><?php if($row["Date_Created"]!=''){ echo date("d/m/Y", strtotime($row["Date_Created"])); } ?></td>
                                                                    <td><?php echo $row["Modified_By"]; ?></td>
                                                                    <td><?php if($row["Date_Modified"]!=''){ echo date("d/m/Y", strtotime($row["Date_Modified"])); } ?></td>
                                                                    </tr>
                                                        <?php } ?>
                                                                    <tr>
                                                                    <td style="font-weight:900; font-size:large;">Grand Total</td>
                                                                    <td></td>
                                                                    <td></td>
                                                                    <td style="font-weight:900; font-size:large;"><?php echo $row["Grand_Total"]." ₹";?></td>
                                                                    <td></td>
                                                                    <td></td>
                                                                    <td></td>
                                                                    <td></td>
                                                                    <td></td>
                                                                    <td></td>
                                                                    <td></td>
                                                                   </tr>
                                                        
                                                                    
                                                </tbody>
                                        </table>
                                        </div>
                                                <?php
                                                                   
                                                        }
                                                        else {?>
                                                            <h4 class="text-centre">No records found. Please change dates and generate again.</h4>
                                                        <?php
                                                        }
                                                     
                                                    }?> 
                        </div>
                    </div>            
                                        </div>
                                        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js" type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/gh/bbbootstrap/libraries@main/jquery.table2excel.min.js"></script>
    <script src="../js/export.js" type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
    </body>
</html>