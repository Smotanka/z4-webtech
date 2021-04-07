<?php
require_once "requirements.php";
function customError($errno, $errstr)
{
    echo "<b>Error:</b> [$errno] $errstr";
}

//set error handler
set_error_handler("customError");
$at=new attendanceController();
$names=$at->getNames();
$dates=$at->getDates();
$tables=$at->getTables();
$dataPoints = array();
$cnt=1;

foreach ($tables as $table){

    $dataPoints[]=array( "label"=> $cnt.". Prednáška", "y"=>$at->getCountUser($table) );
    $cnt++;
}

?>
<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <title>Webtech-2</title>
    <meta name="author" content="Martin Smetanka">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Martin Smetanka">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
            integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN"
            crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"
            integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
            crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"
            integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
            crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
          integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm"
          crossorigin="anonymous">
    <script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
    <script src="resources/js/table-sort-js-master/table-sort-js-master/public/table-sort.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.slim.js" integrity="sha256-HwWONEZrpuoh951cQD1ov2HUK5zA5DwJ1DNUXaM6FsY=" crossorigin="anonymous"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/jq-3.3.1/dt-1.10.24/datatables.min.css"/>
    <script type="text/javascript" src="https://cdn.datatables.net/v/bs4/jq-3.3.1/dt-1.10.24/datatables.min.js"></script>

</head>
<body style="background-color: whitesmoke">

<div class="modal fade" id="Modal" tabindex="-1" role="dialog" aria-labelledby="Modal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header ">
                <h5 class="modal-title" id="exampleModalLongTitle"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="data">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary " data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<header style="margin: 2rem">

</header>
<div class="card" id="table-card" style="margin: auto;width: 75%; height: 100%;border: 5px  #ffca18">
    <div class="card-header text-black bg-warning" ><h2 style="font-family: monospace">Dochádzka predmetu Webtech-2</h2><a href="index.php" role="button" ><img src="resources/images/refresh-arrow.png" alt="refresh" style="margin: auto;height: 1rem;width: 1rem"></a></div>
    <div class="card-body">
        <script>

            function modalText(text,name,date){

                text=text.replace(/-/g, " ")
                let str1="Log osoby ";
                str1=date.concat("\n",str1,name.replace(/-/g, " ")," ");
                document.getElementById("exampleModalLongTitle").innerText=str1;
                    document.getElementById("data").innerText=text.replace(/X/g, "\n");
            }

        </script>


        <table class=" table-responsive table-striped" id="table" style="margin: auto">
            <thead>
            <tr>
                <th scope="col">Meno</th>
                <?php
                $lec_num=0;
                    foreach ($dates as $date){
                        $lec_num++;
                        echo "<th scope='col' style='width: 10rem;cursor: pointer;font-size: small' >$lec_num. Prednáška <br>$date</th>";

                    }
                ?>
                <th scope="col" style='width: 10rem;cursor: pointer'>Účasť</th>
                <th scope="col" style='width: 10rem;cursor: pointer'>Dokopy</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $lec_num=0;
            foreach ($names as $name) {
                $tr_name=trim($name,'"');
                echo "<tr>
                        <td  style='width: 12rem'>$tr_name</td>";
                foreach ($tables as $table) {

                    $time = $at->getTime( $name, $table);
                    $string=$at->getDetailedActivity($name, $table);
                    $trimmed_name=str_replace(" ","-",$name);
                    $lecture=$at->getLastDate($table);
                    if(strpos($time,"c")){
                        $time=str_replace("c","",$time);
                        echo "<td class='hoverable'><a role='button'  style='color: red;cursor: pointer' onclick=modalText('{$string}','$trimmed_name','$lecture') data-toggle='modal' data-target='#Modal'>" . $time . "</a></td>";
                    }else{
                        echo "<td class='hoverable'><a role='button' style='cursor: pointer;' onclick=modalText('{$string}','$trimmed_name','$lecture') data-toggle='modal' data-target='#Modal'>" . $time . "</a></td>";
                    }
                }
                echo    "<td>" . $at->getAttendance($name) . "</td>
                        <td>" . $at->getTotalTime($name) . "</td>
                    </tr>";

            }
            ?>
            </tbody>
        </table>
    </div>
</div>
<div>
    <script>
        function  graphLoad() {

            var chart = new CanvasJS.Chart("chartContainer", {
                animationEnabled: true,
                exportEnabled: true,
                theme: "light1", // "light1", "light2", "dark1", "dark2"
                title:{
                    text: "Grafický prehľad",
                    fontFamily:"monospace"
                },
                axisY:{
                    includeZero: true
                },
                data: [{
                    type: "column", //change type to bar, line, area, pie, etc
                    indexLabel: "{y}",
                    includeZero: true,
                    indexLabelFontColor: "#5A5757",
                    indexLabelPlacement: "outside",
                    dataPoints: <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>
                }]
            });
            chart.render();

        }
        $(document).ready( function () {

            $('#table').DataTable();
        } );

    </script>
</div>
<style>
    .pagination > li > a
    {
        background-color: white;
        color: black;
    }

    .pagination > li > a:focus,
    .pagination > li > a:hover,
    .pagination > li > span:focus,
    .pagination > li > span:hover
    {
        color: #5a5a5a;
        background-color: #eee;
        border-color: #ddd;
    }

    .pagination > .active > a
    {
        color: white;
        background-color: #ffca18 !Important;
        border: solid 1px #ffca18 !Important;
    }

    .pagination > .active > a:hover
    {
        background-color: #ffca18 !Important;
        color: black;
        border: solid 1px #ffca18;
    }
    .hoverable:hover{
        font-size: large;
        font-weight: bold;

    }

</style>
<hr style="width: 75%">
<div class="card" id="table-card" style="margin: auto;width: 75%; height: 600px;border: 5px  #ffca18;">
    <div class="card-body" style="padding: 5rem">
    <div id='chartContainer' style="max-height: 100%; max-width: 100%;"><script>graphLoad()</script></div>
    </div>
    <div class="card-footer">

    </div>

</div>



<footer style="margin: 2rem;float: left">
<p>Autor: Martin Smetanka</p>
</footer>
</body>
</html>
