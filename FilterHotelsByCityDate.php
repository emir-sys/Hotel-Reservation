<form action="" method="get">
    <table>
        <tr>
            <td>Start Date</td>
            <td><input type="date" name="startdate"></td>
        </tr>
        <tr>
            <td>End Date</td>
            <td><input type="date" name="enddate"></td>
        </tr>
        <tr>
            <td><input type="submit" value="Filter by Booking Date" name="filter"></td>
        </tr>
    </table>
</form>
<table>
    <tr>
        <th>Hotel Name</th>
        <th>Booking Count</th>
    </tr>
<?php

$db = new mysqli("localhost","root","mysql","emir_senel");

if(isset($_GET['startdate'])){
    $start = $_GET['startdate'];
    $end = $_GET['enddate'];
    $res  = $db->prepare("SELECT h.name as hotelname,COUNT(b.id) as counter FROM HOTEL h, Booking b WHERE h.id = b.hotelid AND (b.booking_date >= DATE_FORMAT(?,'%Y-%m-%d') AND b.booking_date <= DATE_FORMAT(?,'%Y-%m-%d')) GROUP BY hotelname");
    $res->bind_param("ss", $start,$end);
    $res->execute();
    $res->bind_result($hname,$hcounter);
    while ($row = $res->fetch()) {
        ?>
        <tr>
            <td><?= $hname; ?></td>
            <td><?= $hcounter; ?></td>
        </tr>
        <?php
    }
}
?>

</table>
