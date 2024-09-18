<table>
    <tr>
        <th>#</th>
        <th>Name</th>
        <th>Select</th>
    </tr>
    <?php
	
	$db = new mysqli("localhost","root","mysql","emir_senel");

    $res = $db->prepare("SELECT * FROM HOTEL");
    $res->execute();
    $res->bind_result($id, $name);
    while ($row = $res->fetch()) {
    ?>
        <tr>
            <td><?= $id; ?></td>
            <td><?= $name; ?></td>
            <td><a href="BookingInfo.php?hotelid=<?= $id ?>">Select</a></td>
        </tr>
    <?php
    }
    ?>
</table>
<?php
if (isset($_GET['hotelid'])) {
?>
    <form action="" method="get">
        <input type="hidden" name="hotelid" value="<?= $_GET['hotelid']; ?>">
        <?php
        $hotelid = $_GET['hotelid'];
        $res = $db->prepare("SELECT * FROM ROOMTYPE");
        $res->execute();
        ?>
        <select name="roomtypeid">
            <?php
            $res->bind_result($id, $type, $cost);
            while ($row = $res->fetch()) {
            ?>
                <option value="<?= $id; ?>"><?= $type ?></option>
            <?php
            }
            ?>
        </select>
        <input type="submit" value="Filter by Room Type" name="filterroomtype">
        <input type="submit" value="See Agency Earnings From Bookings" name="filteragency">
        <?php
        $res = $db->prepare("SELECT distinct c.* FROM CLIENT c INNER JOIN BOOKING b ON b.clientid=c.id WHERE hotelid=$hotelid");
        $res->execute();
        ?>
        <select name="clientid">
            <?php
            $res->bind_result($id, $name, $surname);
            while ($row = $res->fetch()) {
            ?>
                <option value="<?= $id; ?>"><?= $name . " " . $surname ?></option>
            <?php
            }
            ?>
        </select>
        <input type="submit" value="Invoice Of Client" name="filterclient">
    </form>

    <?php
    if (isset($_GET['filterroomtype'])) {
        $typeid = $_GET['roomtypeid'];
        $query = "SELECT COUNT(1) as counter FROM BOOKING b, ROOM r WHERE r.typeid=$typeid AND b.hotelid=$hotelid";
        $res = $db->prepare($query);
        $res->execute();
        $res->bind_result($counter);
        if ($row = $res->fetch()) {
            echo "Total " . $counter . " Bookings made to selected room type.<br>";
        }
    }
    if (isset($_GET['filteragency'])) {
    ?>
        <table>
            <tr>
                <th>#</th>
                <th>Agency</th>
                <th>Type</th>
                <th>Income From Bookings</th>
                <th>Select</th>
            </tr>
            <?php
            $query = "SELECT ta.id as tid,ta.name as tname,ta.type as ttype,SUM(rt.price)*0.1 as tearning FROM BOOKING b INNER JOIN HOTEL h ON b.hotelid = h.id INNER JOIN ROOM r ON h.id = r.hotelid INNER JOIN ROOMTYPE rt ON r.typeid = rt.id INNER JOIN TRAVELAGENT ta ON ta.id=b.agencyid WHERE b.hotelid=$hotelid GROUP BY tid,tname,ttype";
            $res = $db->prepare($query);
            $res->execute();
            $res->bind_result($tid, $tname, $ttype, $tearning);
            while ($row = $res->fetch()) {
            ?>
                <tr>
                    <td><?= $tid ?></td>
                    <td><?= $tname ?></td>
                    <td><?= $ttype ?></td>
                    <td><?= $tearning ?></td>
                    <td><a href="BookingInfo.php?hotelid=<?= $id ?>&filteragencybooking=<?= $tid ?>">Select</a></td>
                </tr>
            <?php
            }
            ?>
        </table>
    <?php
    }
    if (isset($_GET['filteragencybooking'])) {
        $agencyid = $_GET['filteragencybooking'];
    ?>
        <table>
            <tr>
                <th>Booking Date</th>
                <th>Check In Date</th>
                <th>Check Out Date</th>
                <th>Room Type</th>
                <th>Client Name</th>
                <th>Client Surname</th>
                <th>Room No</th>
                <th>Price</th>
            </tr>
            <?php
            $query = "SELECT distinct booking_date,checkin_date,checkout_date,rt.type,c.name,c.surname,r.id,rt.price FROM BOOKING b INNER JOIN HOTEL h ON b.hotelid = h.id INNER JOIN ROOM r ON h.id = r.hotelid INNER JOIN ROOMTYPE rt ON r.typeid = rt.id INNER JOIN CLIENT c ON c.id = b.clientid WHERE h.id=$hotelid AND b.agencyid=$agencyid";
            $res = $db->prepare($query);
            $res->execute();
            $res->bind_result($bdate, $cidate, $codate, $type, $cname, $csurname, $rid, $price);
            while ($row = $res->fetch()) {
            ?>
                <tr>
                    <td><?= $bdate ?></td>
                    <td><?= $cidate ?></td>
                    <td><?= $codate ?></td>
                    <td><?= $type ?></td>
                    <td><?= $cname ?></td>
                    <td><?= $csurname ?></td>
                    <td><?= $rid ?></td>
                    <td><?= $price ?></td>
                </tr>
            <?php
            }
            ?>
        </table>
    <?php
    }

    if (isset($_GET['filterclient'])) {
        $clientid = $_GET['clientid'];
    ?>
        <table>
            <tr>
                <th>Booking Date</th>
                <th>Check In Date</th>
                <th>Check Out Date</th>
                <th>Room Type</th>
                <th>Room No</th>
                <th>Total Cost</th>
            </tr>
            <?php
            $query = "SELECT distinct booking_date,checkin_date,checkout_date,rt.type,r.id,(rt.price*DATEDIFF(checkout_date,checkin_date)) as totalcost FROM BOOKING b INNER JOIN HOTEL h ON b.hotelid = h.id INNER JOIN ROOM r ON h.id = r.hotelid INNER JOIN ROOMTYPE rt ON r.typeid = rt.id INNER JOIN CLIENT c ON c.id = b.clientid WHERE h.id=$hotelid AND b.clientid = $clientid";
            $res = $db->prepare($query);
            $res->execute();
            $res->bind_result($bdate, $cidate, $codate, $type, $rid, $price);
            while ($row = $res->fetch()) {
            ?>
                <tr>
                    <td><?= $bdate ?></td>
                    <td><?= $cidate ?></td>
                    <td><?= $codate ?></td>
                    <td><?= $type ?></td>
                    <td><?= $rid ?></td>
                    <td><?= $price ?></td>
                </tr>
            <?php
            }
            ?>
        </table>
<?php
    }
}
?>